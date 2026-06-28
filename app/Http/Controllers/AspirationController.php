<?php

namespace App\Http\Controllers;

use App\Models\Aspiration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AspirationController extends Controller
{
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:aspirations,id',
        ]);

        Aspiration::whereIn('id', $request->ids)->delete();

        return response()->json(['message' => 'Aspirasi berhasil dihapus!']);
    }

    public function aspirationForm()
    {
        return view('aspiration_forms.voxes-form');
    }

    public function index()
    {
        return view('aspirations.index');
    }

    public function fetchPaginated(Request $request)
    {
        $query = Aspiration::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('message', 'like', "%{$search}%")
                  ->orWhere('to', 'like', "%{$search}%")
                  ->orWhere('kelas', 'like', "%{$search}%");
            });
        }
        if ($request->filled('filterBagian')) {
            $query->where('to', $request->filterBagian);
        }
        if ($request->filled('filterKelas')) {
            $query->where('kelas', $request->filterKelas);
        }
        if ($request->filled('dateFrom')) {
            $query->whereDate('created_at', '>=', $request->dateFrom);
        }
        if ($request->filled('dateTo')) {
            $query->whereDate('created_at', '<=', $request->dateTo);
        }

        $perPage = $request->input('per_page', 12);
        $aspirations = $query->orderByDesc('created_at')->paginate($perPage);

        return response()->json($aspirations);
    }

    public function store(Request $request)
    {
        $request->validate([
            "messages" => "required|array",
            "kelas" => "required|in:X,XI,XII",
        ]);

        $badWords = [
            "anjing",
            "bangsat",
            "goblok",
            "kontol",
            "bego",
            "jing",
            "jir",
            "qontol",
            "puqi",
            "anjay",
            "anjir",
            "a n j i n g",
            "tolol",
            "monyet",
            "babi",
            "memek",
            "pepek",
            "puki",
            "jancuk",
            "jancok",
            "kampret",
            "kntl",
            "kntol",
            "kintil",
            "pantek",
            "panteq",
            "bajingan",
            "badjingan",
            "fuck",
            "shit",
            "asshole",
            "anying",
            "lonte",
            "kontoI",
            "4njing",
            "babl",
            "bacot"
        ];

        foreach ($request->messages as $tujuan => $pesan) {
            if (trim($pesan) === "") continue;

            foreach ($badWords as $word) {
                if (stripos($pesan, $word) !== false) {
                    return back()->withErrors(['message' => "Pesan mengandung kata {$word}! tolong diubah"])->withInput();
                }
            }

            Aspiration::create([
                "to" => $tujuan,
                "message" => $pesan,
                "kelas" => $request->kelas,
            ]);
        }

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
    public function exportCsv(Request $request)
    {
        $query = Aspiration::query();

        if ($request->filled('to')) {
            $query->where('to', $request->to);
        }
        if ($request->filled('kelas')) {
            $query->where('kelas', $request->kelas);
        }
        if ($request->filled('search')) {
            $query->where('message', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $aspirations = $query->orderByDesc('created_at')->get();

        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "inline; filename=aspirasi_audiensi.csv",
        ];

        $columns = ["Timestamps", "Pesan kritik, saran, masukan", "Kepada", "Kelas"];

        return response()->stream(function () use ($aspirations, $columns) {
            $handle = fopen("php://output", "w");
            fputcsv($handle, $columns, ";");

            foreach ($aspirations as $asp) {
                fputcsv($handle, [
                    $asp->created_at->timezone('Asia/Jakarta')->format('Y-m-d H:i:s'),
                    $asp->message,
                    $asp->to,
                    $asp->kelas,
                ], ";");
            }

            fclose($handle);
        }, 200, $headers);
    }
}
