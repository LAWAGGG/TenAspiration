<?php

namespace App\Http\Controllers;

use App\Models\TargetEmail;
use Illuminate\Http\Request;

class TargetEmailController extends Controller
{
    public function index()
    {
        $emails = TargetEmail::orderByDesc('created_at')->get();
        return response()->json($emails);
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = TargetEmail::create([
            'email' => $request->email,
        ]);

        return response()->json(['message' => 'Email berhasil ditambahkan', 'data' => $email]);
    }

    public function destroy($id)
    {
        $email = TargetEmail::findOrFail($id);
        $email->delete();
        return response()->json(['message' => 'Email berhasil dihapus']);
    }

    public function toggle($id)
    {
        $email = TargetEmail::findOrFail($id);
        $email->update(['is_active' => !$email->is_active]);
        return response()->json(['message' => 'Status berhasil diubah', 'data' => $email]);
    }
}
