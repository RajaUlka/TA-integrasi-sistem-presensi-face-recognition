<nav class="bg-white border-b border-slate-200 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            
            <div class="flex items-center">
            <div class="flex-shrink-0 flex items-center gap-3 mr-8">
                <img src="https://www.polibatam.ac.id/wp-content/uploads/2022/01/Logo-Polibatam.png"
                    class="h-9 w-auto" alt="Logo Polibatam">
                <span class="text-base font-bold text-slate-800 tracking-tight">Admin Presensi AI</span>
            </div>

                <div class="hidden sm:flex sm:space-x-2 items-center">
                    <a href="{{ route('admin.dashboard') }}" 
                       class="{{ request()->routeIs('admin.dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600' }} px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200">
                        Dashboard Presensi
                    </a>
                    <a href="{{ route('admin.register_face') }}" 
                       class="{{ request()->routeIs('admin.register_face') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600' }} px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200">
                        Kelola Mahasiswa
                    </a>
                    <a href="{{ route('admin.matakuliah') }}" 
                       class="{{ request()->routeIs('admin.matakuliah') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600' }} px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200">
                        Mata Kuliah
                    </a>
                </div>
            </div>

            <div class="flex items-center">
                <a href="{{ route('logout') }}" onclick="return confirm('Yakin ingin keluar dari Admin Panel?');" 
                   class="flex items-center gap-2 px-4 py-2 bg-red-50 text-red-600 rounded-xl hover:bg-red-100 hover:text-red-700 text-sm font-bold transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Logout
                </a>
            </div>

        </div>
    </div>
</nav>