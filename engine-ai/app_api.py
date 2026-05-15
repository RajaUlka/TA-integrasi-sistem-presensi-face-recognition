from flask import Flask, request, jsonify
from flask_cors import CORS
import cv2
import numpy as np
import base64
from deepface import DeepFace
import mysql.connector
import os
import json
import logging
import time
from datetime import datetime

# Matikan log bawaan DeepFace
logging.getLogger("deepface").setLevel(logging.ERROR) 

app = Flask(__name__)
CORS(app) 

# --- KONFIGURASI DATABASE ---
db = mysql.connector.connect(
    host="localhost",
    user="root",
    password="password", # Sesuaikan dengan password database-mu ya
    database="db_presensi_ta" 
)
# TAMBAHAN PENTING: buffered=True agar tidak error "Unread result found"
cursor = db.cursor(buffered=True) 

# --- KONFIGURASI FOLDER ---
LOG_FOLDER = r'C:\xampp\htdocs\File TA\engine-ai\presensi_log'

def cleanup_logs(folder_path, days=7):
    if not os.path.exists(folder_path):
        os.makedirs(folder_path)
    now = time.time()
    for f in os.listdir(folder_path):
        f_path = os.path.join(folder_path, f)
        if os.path.isfile(f_path):
            if os.stat(f_path).st_mtime < now - (days * 86400):
                os.remove(f_path)
                print(f"♻️ Cleanup: Menghapus foto lama {f}")

# Jalankan saat server Python pertama kali nyala
cleanup_logs(LOG_FOLDER, days=7)

def enhance_image(img):
    # 1. Gamma Correction (Mencerahkan area gelap secara cerdas)
    gamma = 1.2
    invGamma = 1.0 / gamma
    table = np.array([((i / 255.0) ** invGamma) * 255 for i in np.arange(0, 256)]).astype("uint8")
    img_gamma = cv2.LUT(img, table)

    # 2. CLAHE (Menajamkan kontras wajah agar fitur lebih jelas)
    lab = cv2.cvtColor(img_gamma, cv2.COLOR_BGR2LAB)
    l, a, b = cv2.split(lab)
    clahe = cv2.createCLAHE(clipLimit=2.0, tileGridSize=(8, 8))
    cl = clahe.apply(l)
    limg = cv2.merge((cl, a, b))
    final_img = cv2.cvtColor(limg, cv2.COLOR_LAB2BGR)
    
    return final_img

@app.route('/verify', methods=['POST'])
def verify_face():
    try:
        db.ping(reconnect=True, attempts=3, delay=2)
        global cursor
        cursor = db.cursor(buffered=True)

        data = request.json
        image_data = data['image'].split(",")[1]
        decoded_data = base64.b64decode(image_data)
        kode_mk = data.get('kode_mk', 'UNKNOWN')
        status_kehadiran = data.get('status_kehadiran', 'Hadir')
        np_data = np.frombuffer(decoded_data, np.uint8)
        img = cv2.imdecode(np_data, cv2.IMREAD_COLOR)
        img = enhance_image(img)

        print("Menerima foto dari Web, mengekstrak fitur wajah...")
        
        face_objs = DeepFace.represent(
            img_path=img, 
            model_name='Facenet',       
            detector_backend='mtcnn',   
            enforce_detection=True,
            anti_spoofing=True
        )

        if len(face_objs) == 0:
            return jsonify({"status": "failed", "message": "Wajah tidak terdeteksi"})

        incoming_embedding = np.array(face_objs[0]["embedding"])

        # FIX 1: Pakai nama tabel yang benar -> wajah_features
        cursor.execute("""
                    SELECT w.nim, w.v_features, m.nama 
                    FROM wajah_features w
                    JOIN mahasiswa m ON w.nim = m.nim
                """)
        db_faces = cursor.fetchall()

        best_match_nim = None
        best_match_nama = None
        min_distance = 0.30 

        for row in db_faces:
            db_nim = row[0]
            db_feature_str = row[1] 
            db_nama = row[2]
            
            db_embedding = np.array(json.loads(db_feature_str))
            
            # Hitung Cosine Distance
            distance = 1 - np.dot(incoming_embedding, db_embedding) / (np.linalg.norm(incoming_embedding) * np.linalg.norm(db_embedding))

            if distance < min_distance:
                min_distance = distance
                best_match_nim = db_nim
                best_match_nama = db_nama

        if best_match_nim:
            print(f"✅ Cocok! NIM: {best_match_nim} | Nama: {best_match_nama} | Status: {status_kehadiran}")
            
            waktu_sekarang = datetime.now()
            file_name = f"BUKTI_{best_match_nim}_{waktu_sekarang.strftime('%H%M%S')}.jpg"
            file_path = os.path.join(LOG_FOLDER, file_name)
            cv2.imwrite(file_path, img)

            # Insert ke MySQL (Tambahkan kolom status_kehadiran)
            sql = "INSERT INTO presensi (nim, kode_mk, waktu_masuk, bukti_liveness, status_kehadiran) VALUES (%s, %s, %s, %s, %s)"
            val = (best_match_nim, kode_mk, waktu_sekarang, file_name, status_kehadiran) 
            
            cursor.execute(sql, val)
            db.commit()

            return jsonify({
                "status": "success",
                "message": f"Presensi Berhasil",
                "nim": best_match_nim,
                "nama": best_match_nama # Pastikan variabel ini ada di atas ya (hasil db_faces)
            })

        print("❌ Wajah tidak cocok dengan data di database.")
        return jsonify({"status": "failed", "message": "Wajah tidak dikenali"})

    except Exception as e:
        print("Error System:", str(e))
        return jsonify({"status": "error", "message": str(e)})
    

@app.route('/register', methods=['POST'])
def register_new_face():
    try:
        data = request.json
        nim = data['nim']
        photos = data['photos'] # Array berisi 5 foto base64

        print(f"Menerima 5 sampel foto untuk NIM: {nim}, sedang memproses...")

        all_embeddings = []

        for photo_b64 in photos:
            # Decode Base64 ke gambar OpenCV
            image_data = photo_b64.split(",")[1]
            decoded_data = base64.b64decode(image_data)
            np_data = np.frombuffer(decoded_data, np.uint8)
            img = cv2.imdecode(np_data, cv2.IMREAD_COLOR)
            img = enhance_image(img)

            # Ekstrak Fitur (Tidak perlu anti-spoofing saat registrasi, yang penting jelas)
            face_objs = DeepFace.represent(
                img_path=img, 
                model_name='Facenet',       
                detector_backend='mtcnn',   
                enforce_detection=True
            )

            if len(face_objs) > 0:
                all_embeddings.append(face_objs[0]["embedding"])

        if len(all_embeddings) == 0:
            return jsonify({"status": "failed", "message": "Tidak ada wajah yang terdeteksi dari 5 foto."})

        # --- HITUNG RATA-RATA DARI 5 FOTO (Akurasi Maksimal) ---
        mean_embedding = np.mean(all_embeddings, axis=0)
        feature_string = json.dumps(mean_embedding.tolist())

        # --- SIMPAN KE DATABASE ---
        # Hapus data fitur lama jika mahasiswa ini mau update wajah (karena ON DELETE CASCADE mungkin belum menangani fitur override)
        cursor.execute("DELETE FROM wajah_features WHERE nim = %s", (nim,))
        
        # Insert fitur wajah baru
        sql = "INSERT INTO wajah_features (nim, v_features) VALUES (%s, %s)"
        cursor.execute(sql, (nim, feature_string))
        db.commit()

        print(f"✅ Wajah NIM {nim} berhasil didaftarkan ke Database!")
        return jsonify({"status": "success", "message": "Fitur wajah tersimpan."})

    except Exception as e:
        print("Error Register:", str(e))
        return jsonify({"status": "error", "message": str(e)})

if __name__ == '__main__':
    app.run(port=5000, debug=True)