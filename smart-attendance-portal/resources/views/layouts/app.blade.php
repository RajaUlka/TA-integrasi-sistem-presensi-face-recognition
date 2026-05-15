<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Smart Presensi Wajah')</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    
    <style>
        /* Menghapus .blob lama karena dipindah ke welcome.blade.php */
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
        }
    </style>
    @yield('styles')
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen" style="position:relative; overflow-x:hidden;">

    @yield('content')

    <footer class="w-full text-center py-6 text-slate-400 text-xs">
        &copy; {{ date('Y') }} Smart Presensi. Built with Precision.
    </footer>

    <script>
    // Pastikan elemen video ada di halaman yang menggunakan layout ini
    const video = document.getElementById('video'); 
    const canvas = document.createElement('canvas');
    
    async function sendFrame() {
        if (!video) return; // Guard clause jika elemen video tidak ada

        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);
        
        canvas.toBlob(async (blob) => {
            const formData = new FormData();
            formData.append('file', blob);

            try {
                const response = await fetch('http://localhost:8000/scan', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                
                if(data.status === 'success') {
                    console.log("Terdeteksi: " + data.name);
                }
            } catch (err) {
                console.error("API AI tidak aktif");
            }
        }, 'image/jpeg', 0.5);
    }

    // Hanya jalankan interval jika video ditemukan di DOM
    if (video) {
        setInterval(sendFrame, 1000); 
    }
</script>

    @yield('scripts')
</body>
</html>