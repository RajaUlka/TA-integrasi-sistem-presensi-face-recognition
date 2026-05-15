@echo off
cd /d "C:\xampp\htdocs\File TA\engine-ai"

:: Kita panggil langsung EXE python yang ada di dalam venv
:: Ini cara paling 'brute force' tapi 100% akurat
".\venv\Scripts\python.exe" presensi.py

pause