import cv2
from deepface import DeepFace
import os
import time
import mysql.connector
import json
import numpy as np
import datetime

# --- AUTO CLEANUP (Hapus foto lama > 7 hari) ---
def cleanup_logs(folder_path, days=7):
    now = time.time()
    for f in os.listdir(folder_path):
        f_path = os.path.join(folder_path, f)
        # Jika file lebih tua dari 'days' hari, hapus
        if os.stat(f_path).st_mtime < now - (days * 86400):
            if os.path.isfile(f_path):
                os.remove(f_path)
                print(f"Cleanup: Menghapus foto lama {f}")

# Panggil fungsinya saat aplikasi pertama kali dijalankan
cleanup_logs("presensi_log", days=7)

# --- KONEKSI DATABASE ---
try:
    db = mysql.connector.connect(
        host="localhost", 
        user="root", 
        password="password", 
        database="db_presensi_ta"
    )
    cursor = db.cursor()
except Exception as e:
    print(f"Gagal koneksi database: {e}")
    exit()

# --- AMBIL SEMUA FITUR WAJAH DARI DB KE MEMORI ---
print("Memuat data wajah dari database...")
cursor.execute("SELECT nim, v_features FROM wajah_features")
rows = cursor.fetchall()

known_faces = []
for row in rows:
    known_faces.append({
        "nim": row[0],
        "vector": np.array(json.loads(row[1])) 
    })
print(f"Berhasil memuat {len(known_faces)} fitur wajah.")

log_path = "presensi_log"
if not os.path.exists(log_path): os.makedirs(log_path)

# --- VARIABEL KONTROL ---
absen_sesi_ini = set() 
tracking = {"name": None, "state": 0, "start_x": 0}
cap = cv2.VideoCapture(0)

def find_match_in_db(current_embedding):
    """Menghitung jarak wajah menggunakan Cosine Similarity secara manual (aman dari update library)"""
    best_match = None
    min_dist = 0.35 # Threshold Facenet (0.35 - 0.40 biasanya pas)
    
    for face in known_faces:
        # Perhitungan Cosine Distance manual
        a = np.dot(face["vector"], current_embedding)
        b = np.linalg.norm(face["vector"]) * np.linalg.norm(current_embedding)
        dist = 1 - (a / b)
        
        if dist < min_dist:
            min_dist = dist
            best_match = face["nim"]
    return best_match

print("Memulai Kamera... Tekan 'q' untuk keluar.")

while True:
    ret, frame = cap.read()
    if not ret: break
    display_frame = frame.copy()

    try:
        # TIPS PERFORMA: Ganti detector_backend ke 'opencv' jika MTCNN terlalu lambat/laggy
        face_objs = DeepFace.represent(
            img_path=frame, 
            model_name='Facenet', 
            detector_backend='mtcnn', # Ganti ke 'opencv' jika berat
            enforce_detection=False
        )
        
        if face_objs and len(face_objs) > 0 and face_objs[0]['face_confidence'] > 0.9:
            obj = face_objs[0]
            current_embedding = obj["embedding"]
            area = obj["facial_area"]
            x, y, w, h = area['x'], area['y'], area['w'], area['h']
            
            # 1. Cari kecocokan
            name = find_match_in_db(current_embedding)
            
            status = "WAJAH TIDAK DIKENAL"
            color = (0, 0, 255)

            if name:
                if name in absen_sesi_ini:
                    status = f"{name}: SUDAH ABSEN"
                    color = (255, 0, 0)
                else:
                    # Inisialisasi Tracking jika wajah baru
                    if tracking["name"] != name:
                        tracking = {"name": name, "state": 1, "start_x": x}
                    
                    diff = x - tracking["start_x"]
                    
                    # --- LOGIKA LIVENESS (Toleh Kanan-Kiri) ---
                    if tracking["state"] == 1:
                        status = ">>> TOLEH KANAN >>>"
                        color = (0, 165, 255) # Oranye
                        if diff < -60: # Sensitivitas tolehan
                            tracking["state"] = 2
                            tracking["start_x"] = x
                    
                    elif tracking["state"] == 2:
                        status = "<<< TOLEH KIRI <<<"
                        color = (0, 255, 255) # Kuning
                        if diff > 60:
                            tracking["state"] = 3
                    
                    elif tracking["state"] == 3:
                        # PROSES SIMPAN ABSENSI
                        absen_sesi_ini.add(name)
                        tracking["state"] = 4
                        waktu = time.strftime('%Y-%m-%d %H:%M:%S')
                        file_name = f"BUKTI_{name}_{time.strftime('%H%M%S')}.jpg"
                        cv2.imwrite(f"{log_path}/{file_name}", frame)
                        
                        try:
                            cursor.execute(
                                "INSERT INTO presensi (nim, kode_mk, waktu_masuk, bukti_liveness) VALUES (%s, %s, %s, %s)", 
                                (name, "IF123", waktu, file_name)
                            )
                            db.commit()
                            print(f"DATABASE UPDATED: {name} berhasil absen.")
                        except Exception as db_err:
                            print(f"Gagal simpan ke DB: {db_err}")
                            
                    elif tracking["state"] == 4:
                        status = "BERHASIL DICATAT!"
                        color = (0, 255, 0) # Hijau

            # Gambar visual ke layar
            cv2.rectangle(display_frame, (x, y), (x+w, y+h), color, 2)
            cv2.putText(display_frame, status, (x, y-10), cv2.FONT_HERSHEY_SIMPLEX, 0.6, color, 2)
            
        else:
            # Jika wajah hilang dari kamera, reset state agar user harus ulang toleh kanan-kiri
            tracking = {"name": None, "state": 0, "start_x": 0}

    except Exception as e:
        # Menampilkan error di terminal tapi kamera tetap jalan
        print(f"Sistem Error: {e}")

    cv2.imshow('Presensi AI - Database Mode', display_frame)
    
    if cv2.waitKey(1) & 0xFF == ord('q'):
        break

cap.release()
cv2.destroyAllWindows()
db.close()