<?php

namespace App\Http\Controllers;

use App\Models\Aspiration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AspirationController extends Controller
{

    public function index()
    {

        $aspirations = Aspiration::orderByDesc("created_at")->get();


        return view('aspirations.index', compact('aspirations'));
    }

    public function create()
    {
        return view('aspirations.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            "message" => "required",
            "to" => "required",
        ]);

        $badWords = ["anjing", "bangsat", "goblok", "tai", "kontol", "bego", "anj", "anjay", "anjir", "a n j i n g", "tolol", "monyet", "babi", "memek", "pepek", "puki", "jancuk", "jancok", "kampret", "kntl", "kntol", "kntl", "kntol", "pantek", "panteq", "pantek", "panteq", "bajingan", "fuck", "shit", "asshole"
    ];
        foreach ($badWords as $word) {
            if (stripos($request->message, $word) !== false) {
                return back()->withErrors(['message' => 'Pesan mengandung kata tidak pantas!'])->withInput();
            }
        }

        Aspiration::create([
            "message" => $request->message,
            "to" => $request->to,
        ]);

        return redirect()->back()->with('success', 'Aspirasi berhasil dikirim!');
    }

    public function show($id)
    {
        $aspiration = Aspiration::findOrFail($id);
        return view('aspirations.show', compact('aspiration'));
    }

    public function edit($id)
    {
        $aspiration = Aspiration::findOrFail($id);
        return view('aspirations.edit', compact('aspiration'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            "message" => "required",
            "to" => "required",
        ]);

        $aspiration = Aspiration::findOrFail($id);
        $aspiration->update([
            "message" => $request->message,
            "to" => $request->to,
        ]);

        return redirect()->route('aspirations.index')->with('success', 'Aspirasi berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $aspiration = Aspiration::findOrFail($id);
        $aspiration->delete();

        return redirect()->route('aspirations.index')->with('success', 'Aspirasi berhasil dihapus!');
    }

    /**
     * Export CSV
     */
    public function exportCsv()
    {
        $aspirations = Aspiration::get();

        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "inline; filename=aspirations.csv",
        ];

        $columns = ["ID", "Message", "To", "Date"];

        return response()->stream(function () use ($aspirations, $columns) {
            $handle = fopen("php://output", "w");
            fputcsv($handle, $columns, ";");

            foreach ($aspirations as $asp) {
                fputcsv($handle, [
                    $asp->id,
                    $asp->message,
                    $asp->to,
                    $asp->created_at,
                ], ";");
            }

            fclose($handle);
        }, 200, $headers);
    }
}
