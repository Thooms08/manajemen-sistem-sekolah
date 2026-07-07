<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class AuthController extends Controller
{
    public function index()
    {
        // Generate dua angka acak antara 1 sampai 10
        $angka1 = rand(1, 10);
        $angka2 = rand(1, 10);
        
        // Simpan hasil pertambahannya ke dalam session
        session(['captcha_answer' => $angka1 + $angka2]);

        // Kirim angka tersebut ke view login
        return view('index.login', compact('angka1', 'angka2'));
    }

    public function login(Request $request): RedirectResponse
    {
        // Validasi input termasuk input captcha
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required'],
            'captcha'  => ['required', 'numeric'],
        ]);

        // Cek apakah jawaban captcha dari user sesuai dengan di session
        if ((int) $request->captcha !== session('captcha_answer')) {
            return back()->withErrors([
                'captcha' => 'chapta salah.',
            ])->onlyInput('username');
        }

        // Hapus session captcha setelah tervalidasi agar tidak disalahgunakan ulang
        $request->session()->forget('captcha_answer');

        // Ambil data untuk autentikasi
        $credentials = $request->only('username', 'password');
        $remember = $request->boolean('remember');

        // Cari user berdasarkan username
        $user = \App\Models\User::where('username', $request->username)->first();

        if ($user) {
            // Jika user ditemukan, cek password
            if (!\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
                return back()->withErrors([
                    'username' => 'password salah.',
                ])->onlyInput('username');
            }
        } else {
            // Jika user tidak ditemukan, cek apakah password yang dimasukkan cocok dengan user lain
            $allUsers = \App\Models\User::all();
            $passwordCorrectForSomeUser = false;
            foreach ($allUsers as $u) {
                if (\Illuminate\Support\Facades\Hash::check($request->password, $u->password)) {
                    $passwordCorrectForSomeUser = true;
                    break;
                }
            }

            if ($passwordCorrectForSomeUser) {
                return back()->withErrors([
                    'username' => 'username salah.',
                ])->onlyInput('username');
            } else {
                return back()->withErrors([
                    'username' => 'username dan password salah.',
                ])->onlyInput('username');
            }
        }

        // Proses autentikasi
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Logika Redirection berdasarkan Role
            $role = $user->role ?? $user->rules ?? null;

            return match ($role) {
                'admin'      => redirect()->intended('admin'),
                default      => redirect()->intended('/dashboard'),
            };
        }

        // Jika login gagal secara umum
        return back()->withErrors([
            'username' => 'username dan password salah.',
        ])->onlyInput('username');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}