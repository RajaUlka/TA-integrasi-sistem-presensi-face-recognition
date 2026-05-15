import os
import mysql.connector
from deepface import DeepFace
import json

# --- KONEKSI DATABASE ---
db = mysql.connector.connect(
    host="localhost",
    user="root",
    password="password",
    database="db_presensi_ta"
)
cursor = db.cursor()

db_path = "db_wajah"

def enroll_faces():
    print("--- MEMULAI PROSES PENDAFTARAN WAJAH KE DATABASE ---")
    
    # Ambil semua file foto di folder db_wajah
    for file_name in os.listdir(db_path):
        if file_name.endswith((".jpg", ".png", ".jpeg")):
            # Ambil NIM dari nama file (3312311128_1.jpg -> 3312311128)
            nim = file_name.split('_')[0].split('.')[0]
            img_full_path = os.path.join(db_path, file_name)
            
            try:
                print(f"Memproses {file_name}...")
                
                # 1. AI merubah foto jadi angka (Embeddings)
                # Kita pakai Facenet karena paling seimbang antara cepat & akurat
                embeddings = DeepFace.represent(img_path=img_full_path, 
                                               model_name='Facenet', 
                                               detector_backend='mtcnn')[0]["embedding"]
                
                # 2. Ubah array angka jadi string JSON agar bisa masuk kolom TEXT
                vector_data = json.dumps(embeddings)
                
                # 3. Simpan ke Tabel wajah_features
                sql = "INSERT INTO wajah_features (nim, v_features) VALUES (%s, %s)"
                val = (nim, vector_data)
                cursor.execute(sql, val)
                db.commit()
                
                print(f"SUKSES: Fitur wajah {nim} telah disimpan.")
                
            except Exception as e:
                print(f"GAGAL memproses {file_name}: {e}")

    print("\n--- SEMUA WAJAH TELAH TERDAFTAR DI DATABASE ---")

if __name__ == "__main__":
    enroll_faces()
