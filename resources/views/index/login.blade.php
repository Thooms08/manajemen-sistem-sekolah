<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Sistem Informasi Sekolah</title>
    @if(isset($sekolah->logo))
    <link rel="icon" type="image/png" href="{{ asset($sekolah->logo) }}">
    @else
    <link rel="icon" type="image/png" href="{{ asset('assets/img/default-favicon.png') }}">
    @endif
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-emerald-50 via-white to-green-100 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md relative">
        <div class="mb-6">
            <a href="{{ route('home') }}" class="inline-flex items-center text-sm font-semibold text-emerald-700 hover:text-emerald-800 transition-colors group">
                <div class="p-2 bg-white rounded-lg shadow-sm mr-3 group-hover:shadow-md transition-all border border-emerald-100">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                </div>
                Kembali ke Beranda
            </a>
        </div>

        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-emerald-600 text-white rounded-2xl shadow-lg mb-4">
                <i data-lucide="shield-check" class="w-10 h-10"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">Selamat Datang</h1>
            <p class="text-gray-500 text-sm">Silakan masuk ke akun Anda</p>
        </div>

        <div class="glass-effect p-8 rounded-3xl shadow-xl border border-white/50">
            
            @if ($errors->any())
                <div class="mb-6 flex items-center p-4 text-sm text-red-800 border border-red-200 rounded-xl bg-red-50" role="alert">
                    <i data-lucide="alert-circle" class="w-4 h-4 mr-2"></i>
                    <div>
                        {{ $errors->first() }}
                    </div>
                </div>
            @endif

            <form action="{{ route('login.process') }}" method="POST" class="space-y-5">
                @csrf
                
                <div>
                    <label for="username" class="block mb-2 text-sm font-semibold text-gray-700">Username</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i data-lucide="user" class="w-5 h-5"></i>
                        </div>
                        <input type="text" name="username" id="username" 
                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl bg-gray-50 text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all" 
                            placeholder="Masukkan username" 
                            value="{{ old('username') }}" required>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between mb-2">
                        <label for="password" class="text-sm font-semibold text-gray-700">Password</label>
                        <a href="#" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium">Lupa Password?</a>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i data-lucide="lock" class="w-5 h-5"></i>
                        </div>
                        <input type="password" name="password" id="password" 
                            class="block w-full pl-10 pr-12 py-2.5 border border-gray-200 rounded-xl bg-gray-50 text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all" 
                            placeholder="••••••••" required>
                        
                        <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-emerald-600 transition-colors">
                            <i data-lucide="eye" class="w-5 h-5" id="eyeIcon"></i>
                            <i data-lucide="eye-off" class="w-5 h-5 hidden" id="eyeOffIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center">
                    <input id="remember" type="checkbox" class="w-4 h-4 text-emerald-600 bg-gray-100 border-gray-300 rounded focus:ring-emerald-500 cursor-pointer">
                    <label for="remember" class="ml-2 text-sm text-gray-500 cursor-pointer">Ingat saya di perangkat ini</label>
                </div>

                <button type="submit" 
                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all transform active:scale-[0.98]">
                    Masuk Sekarang
                </button>
            </form>
        </div>

        <p class="text-center mt-8 text-sm text-gray-500">
            &copy; 2026 {{ $sekolah->nama_sekolah ?? 'Sistem Informasi Sekolah' }}. All rights reserved.
        </p>
    </div>

    <script>
        // Inisialisasi Ikon Lucide
        lucide.createIcons();

        // Logika Toggle Password
        const passwordInput = document.getElementById('password');
        const toggleButton = document.getElementById('togglePassword');
        const eyeIcon = document.getElementById('eyeIcon');
        const eyeOffIcon = document.getElementById('eyeOffIcon');

        toggleButton.addEventListener('click', () => {
            const isPassword = passwordInput.type === 'password';
            
            // Ubah tipe input
            passwordInput.type = isPassword ? 'text' : 'password';
            
            // Toggle visibilitas ikon
            if (isPassword) {
                eyeIcon.classList.add('hidden');
                eyeOffIcon.classList.remove('hidden');
            } else {
                eyeIcon.classList.remove('hidden');
                eyeOffIcon.classList.add('hidden');
            }
        });
    </script>
</body>
</html>