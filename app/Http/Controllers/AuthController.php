<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class AuthController extends Controller
{
    public function index()
    {
        return view('index.login');
    }

    public function login(Request $request): RedirectResponse
    {
        // Validasi input
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required'],
        ]);

        // Proses autentikasi (Auth::attempt otomatis mengecek Hash::check)
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Logika Redirection berdasarkan Rules
            return match ($user->rules) {
                'admin'      => redirect()->intended('dashboard_admin'),
                'guru'       => redirect()->intended('dashboard_guru'),
                'wali_murid' => redirect()->intended('dashboard_wali'),
                default      => redirect('/login'),
            };
        }

        // Jika login gagal
        return back()->withErrors([
            'username' => 'Username atau password salah.',
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