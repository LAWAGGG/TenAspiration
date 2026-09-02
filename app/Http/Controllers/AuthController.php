<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // Menampilkan form login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Proses login
    public function login(Request $request)
    {
        $val = Validator::make($request->all(), [
            "name" => "required",
            "password" => "required",
        ]);

        if ($val->fails()) {
            return back()->with('error', 'Semua field wajib diisi.');
        }

        if (!Auth::attempt(['name' => $request->name, 'password' => $request->password])) {
            return back()->with('error', 'Nama atau password salah.');
        }

        $role = Auth::user()->role;

        if ($role === 'admin') {
            return redirect()->route('dashboard')->with('success', 'Login berhasil!');
        }

        if ($role === 'wakil') {
            // wakil tetap boleh kelola keluh kesah, tapi tidak boleh dashboard — lempar ke keluh kesah
            // jika ingin wakil juga fallback, ganti ke redirect('/fallback')
            return redirect()->route('aspiration_keluhkesah.index')->with('success', 'Login berhasil!');
        }

        // ponytail: role lain langsung lempar ke fallback — tidak boleh masuk dashboard
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('fallback')->with('error', 'Akses ditolak — hanya admin yang bisa login ke dashboard.');
    }

    // Proses logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('aspirations.create')->with('success', 'Berhasil logout!');
    }
}
