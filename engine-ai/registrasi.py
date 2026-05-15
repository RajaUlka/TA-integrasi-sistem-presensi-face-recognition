import cv2
from deepface import DeepFace
import os

db_path = "db_wajah"
if not os.path.exists(db_path): os.makedirs(db_path)

cap = cv2.VideoCapture(0)
# Daftar instruksi sudut wajah agar mahasiswa tidak bingung (NFR-07)
instruksi = ["DEPAN", "MIRING KIRI", "MIRING KANAN", "MENUNDUK", "MENDONGAK"]

while True:
    ret, frame = cap.read()
    if not ret: break

    display_frame = frame.copy()
    cv2.putText(display_frame, "Tekan 's' untuk Mulai Registrasi", (10, 30), 
                cv2.FONT_HERSHEY_SIMPLEX, 0.6, (255, 255, 255), 2)
    cv2.imshow('Registrasi Mahasiswa', display_frame)

    key = cv2.waitKey(1) & 0xFF
    
    if key == ord('s'):
        nama = input("Masukkan Nama Mahasiswa: ")
        print(f"\n--- Memulai Registrasi untuk {nama} ---")
        
        count = 0
        while count < 5:
            ret, frame = cap.read()
            temp_frame = frame.copy()
            
            # Tampilkan instruksi di layar (Skenario Utama UC-02) [cite: 295]
            text = f"POSE: {instruksi[count]} (Tekan SPACE untuk ambil)"
            cv2.rectangle(temp_frame, (0,0), (640, 60), (0,0,0), -1)
            cv2.putText(temp_frame, text, (20, 40), 
                        cv2.FONT_HERSHEY_SIMPLEX, 0.7, (0, 255, 0), 2)
            
            cv2.imshow('Registrasi Mahasiswa', temp_frame)
            
            # Tunggu tombol SPACE (32) untuk ambil foto, atau 'q' batal
            sub_key = cv2.waitKey(1) & 0xFF
            if sub_key == 32: # Tombol Space
                file_name = f"{db_path}/{nama}_{count+1}.jpg"
                cv2.imwrite(file_name, frame)
                print(f"Berhasil simpan pose {instruksi[count]}")
                count += 1
            elif sub_key == ord('q'):
                print("Registrasi dibatalkan.")
                break
        
        print(f"Selesai! 5 foto {nama} tersimpan di {db_path}.")
        
    elif key == ord('q'):
        break

cap.release()
cv2.destroyAllWindows()