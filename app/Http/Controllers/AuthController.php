<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    // showpagelogin
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function showDataDiri()
    {
        $user = Auth::user();
        $role = $user->roles; // Ini mengambil peran pengguna

        return view('auth.dataDiri', compact('user', 'role'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ], [
            'username.required' => 'Nama pengguna wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $credentials = [
            'username' => $request->username,
            'password' => $request->password, // Ubah dari 'pass' ke 'password'
        ];

        // Tambahkan kondisi untuk memeriksa is_active pengguna
        $user = User::where('username', $request->username)->first();

        if ($user && $user->is_active == 1) {
            // Jika is_active adalah 1, artinya pengguna tidak dapat login
            return redirect()->route('login')->with('error', 'Akun Anda tidak aktif. Silakan hubungi Departemen IT.');
        }

        if (Auth::attempt($credentials)) {
            // Autentikasi berhasil, tampilkan data login di console
            Log::info('Pengguna berhasil masuk.', ['username' => $request->username]);

            return redirect()->route('forumSS');
        }

        // Autentikasi gagal, tampilkan pesan error
        return redirect()->route('login')->with('error', 'Nama Pengguna & Kata Sandi tidak valid.');
    }

    public function logout()
    {
        Auth::logout();

        return redirect()->route('login');
    }

    public function ubahPassword(Request $request)
    {
        $user = Auth::user();

        // Validasi input
        $request->validate([
            'currentPassword' => 'required',
            'newPassword' => 'required|string|min:3|confirmed',
        ]);

        // Pastikan password saat ini benar
        if (!Hash::check($request->currentPassword, $user->password)) {
            return redirect()->back()->with('error', 'Password saat ini salah.');
        }

        // Hash password baru dengan bcrypt
        $user->password = bcrypt($request->newPassword);
        $user->pass = $request->newPassword; // Simpan password terbaru
        $user->save();

        // Redirect ke halaman profil dengan pesan sukses
        return redirect()->route('showDataDiri')->with('success', 'Password berhasil diubah.');
    }

    public function ubahDataDiri(Request $request)
    {
        $user = Auth::user();

        // Perbarui data pengguna
        $user->name = $request->name;
        $user->npk = $request->npk;
        $user->telp = $request->telp;
        $user->email = $request->email;
        $user->save();

        // Redirect ke halaman profil dengan pesan sukses
        return redirect()->route('showDataDiri')->with('success', 'Password berhasil diubah.');
    }
}
