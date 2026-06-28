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

        if (Auth::user()->role == "admin") {
            return redirect()->route('dashboard')->with('success', 'Login berhasil!');
        } elseif (Auth::user()->role == "wakil") {
            return redirect()->route('aspiration_keluhkesah.index')->with('success', 'Login berhasil!');
        }
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
