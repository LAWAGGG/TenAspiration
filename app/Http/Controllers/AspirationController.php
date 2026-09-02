<?php

namespace App\Http\Controllers;

use App\Models\Aspiration;
use App\Models\FormQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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
        $questions = FormQuestion::getForForm('audiensi');
        return view('aspiration_forms.voxes-form', compact('questions'));
    }

    public function index()
    {
        $questions = FormQuestion::getForForm('audiensi');
        $defaultQuestions = FormQuestion::getDefaults()['audiensi'];
        return view('aspirations.index', compact('questions', 'defaultQuestions'));
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
            "messages" => "required|array|max:20",
            "kelas" => "required|in:X,XI,XII",
        ]);

        $questions = FormQuestion::getForForm('audiensi');
        // ponytail: whitelist keys — cegah inject `to` sembarang via extra keys
        $knownKeys = array_column($questions, 'question_key');
        $messageRules = [];
        foreach ($questions as $q) {
            $key = $q['question_key'];
            $type = $q['question_type'] ?? 'essay';
            $required = $q['is_required'] ?? true;
            $raw = $q['question_options'];
            if (is_string($raw)) $raw = json_decode($raw, true);
            $allowed = [];
            if (is_array($raw) && isset($raw['options']) && is_array($raw['options'])) {
                $allowed = array_values(array_filter($raw['options'], fn($v) => is_string($v) && trim($v) !== ''));
            }
            $allowOther = !empty($raw['allow_other']);

            if ($type === 'checkbox') {
                if ($allowOther) {
                    $allowedWithOther = array_merge($allowed, ['__other__']);
                    $messageRules["messages.$key"] = $required ? 'required|array|min:1|max:10' : 'nullable|array|max:10';
                    $messageRules["messages.$key.*"] = ['string', 'max:5000', Rule::in($allowedWithOther)];
                    $messageRules["messages_other.$key"] = 'nullable|string|max:200';
                } else {
                    $messageRules["messages.$key"] = $required ? 'required|array|min:1|max:10' : 'nullable|array|max:10';
                    $messageRules["messages.$key.*"] = $allowed ? ['string', 'max:5000', Rule::in($allowed)] : 'string|max:5000';
                }
            } elseif ($type === 'pilihan_ganda') {
                if ($allowOther) {
                    $allowedWithOther = array_merge($allowed, ['__other__']);
                    $base = $required ? 'required|string|max:5000' : 'nullable|string|max:5000';
                    $messageRules["messages.$key"] = [$base, Rule::in($allowedWithOther)];
                    $messageRules["messages_other.$key"] = 'nullable|string|max:200';
                } else {
                    $base = $required ? 'required|string|max:5000' : 'nullable|string|max:5000';
                    $messageRules["messages.$key"] = $allowed ? [$base, Rule::in($allowed)] : $base;
                }
            } else {
                $messageRules["messages.$key"] = $required ? 'required|string|max:5000' : 'nullable|string|max:5000';
            }
        }
        $request->validate($messageRules);
        // ponytail: Lainnya transform — ganti sentinel __other__ dengan teks bebas
        $messagesOther = $request->input('messages_other', []);
        $rawMessages = $request->input('messages', []);
        foreach ($questions as $q) {
            $key = $q['question_key'];
            $type = $q['question_type'] ?? 'essay';
            $raw = $q['question_options'];
            if (is_string($raw)) $raw = json_decode($raw, true);
            if (empty($raw['allow_other'])) continue;
            if ($type === 'pilihan_ganda' && isset($rawMessages[$key]) && $rawMessages[$key] === '__other__') {
                $otherText = trim((string)($messagesOther[$key] ?? ''));
                if ($otherText === '') {
                    return back()->withErrors(["messages.$key" => 'Isi Lainnya untuk '.$q['question_label'].' wajib diisi'])->withInput();
                }
                $rawMessages[$key] = $otherText;
            } elseif ($type === 'checkbox' && isset($rawMessages[$key]) && is_array($rawMessages[$key]) && in_array('__other__', $rawMessages[$key], true)) {
                $otherText = trim((string)($messagesOther[$key] ?? ''));
                if ($otherText === '') {
                    return back()->withErrors(["messages.$key" => 'Isi Lainnya untuk '.$q['question_label'].' wajib diisi'])->withInput();
                }
                $rawMessages[$key] = array_values(array_map(fn($v) => $v === '__other__' ? $otherText : $v, $rawMessages[$key]));
            }
        }
        // filter to known keys only — extra keys diabaikan
        $filteredMessages = array_intersect_key($rawMessages ?? [], array_flip($knownKeys));

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

        foreach ($filteredMessages as $tujuan => $pesan) {
            if (is_array($pesan)) {
                $pesan = implode(', ', array_values(array_filter($pesan, fn ($v) => $v !== null && $v !== '')));
            }
            if (trim((string) $pesan) === "") continue;

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

    public function update(Request $request, $id)
    {
        $request->validate([
            "message" => "required|string|max:5000",
            "to" => "required|string|max:100",
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
