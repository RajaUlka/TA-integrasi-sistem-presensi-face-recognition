from locust import HttpUser, task, between

class PresensiUser(HttpUser):
    # Simulasi jeda waktu antar mahasiswa yang melakukan scan (1-3 detik)
    wait_time = between(1, 4)

    @task
    def test_absen_biometrik(self):
        # Data JSON yang dikirim (Payload)
        # Menggunakan base64 asli yang kamu berikan
        payload = {
            "image": "data:image/jpeg;base64,/9j/4AAQSkZJRgABAQ...", 
            "kode_mk": "IF111",
            "tipe_absen": "fleksibel"
        }
        
        # Header untuk memastikan server menerima format JSON
        headers = {'Content-Type': 'application/json'}

        # Menembak endpoint /verify pada server Flask
        with self.client.post("/verify", json=payload, headers=headers, catch_response=True) as response:
            if response.status_code == 200:
                response.success()
            else:
                response.failure(f"Gagal: {response.status_code}")