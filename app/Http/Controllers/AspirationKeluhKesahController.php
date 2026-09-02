<?php

namespace App\Http\Controllers;

use App\Models\AspirationKeluhKesah;
use App\Models\FormQuestion;
use App\Models\TargetEmail;
use App\Notifications\KeluhKesahNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;

class AspirationKeluhKesahController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $questions = FormQuestion::getForForm('keluh_kesah');
        $defaultQuestions = FormQuestion::getDefaults()['keluh_kesah'];
        return view('aspiration_keluh_kesah.index', compact('questions', 'defaultQuestions'));
    }

    public function fetchPaginated(Request $request)
    {
        $query = AspirationKeluhKesah::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('keluh_kesah', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('custom_answers', 'like', "%{$search}%");
            });
        }
        if ($request->filled('dateFrom')) {
            $query->whereDate('created_at', '>=', $request->dateFrom);
        }
        if ($request->filled('dateTo')) {
            $query->whereDate('created_at', '<=', $request->dateTo);
        }

        $perPage = $request->input('per_page', 12);
        $messages = $query->orderByDesc('created_at')->paginate($perPage);

        return response()->json($messages);
    }

    public function aspirationForm()
    {
        $questions = FormQuestion::getForForm('keluh_kesah');
        return view('aspiration_forms.keluh-kesah-form', compact('questions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $questions = FormQuestion::getForForm('keluh_kesah');

        $rules = [];
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
            if ($key === 'phone_number') {
                $rules[$key] = 'nullable|string|digits_between:8,15|max:15';
            } elseif ($type === 'checkbox') {
                if ($allowOther) {
                    $allowedWithOther = array_merge($allowed, ['__other__']);
                    $rules[$key] = $required ? 'required|array|min:1|max:10' : 'nullable|array|max:10';
                    $rules[$key . '.*'] = ['string', 'max:5000', Rule::in($allowedWithOther)];
                    $rules[$key . '_other'] = 'nullable|string|max:200';
                } else {
                    $rules[$key] = $required ? 'required|array|min:1|max:10' : 'nullable|array|max:10';
                    $rules[$key . '.*'] = $allowed ? ['string', 'max:5000', Rule::in($allowed)] : 'string|max:5000';
                }
            } elseif ($type === 'pilihan_ganda') {
                if ($allowOther) {
                    $allowedWithOther = array_merge($allowed, ['__other__']);
                    $base = $required ? 'required|string|max:5000' : 'nullable|string|max:5000';
                    $rules[$key] = [$base, Rule::in($allowedWithOther)];
                    $rules[$key . '_other'] = 'nullable|string|max:200';
                } else {
                    $base = $required ? 'required|string|max:5000' : 'nullable|string|max:5000';
                    $rules[$key] = $allowed ? [$base, Rule::in($allowed)] : $base;
                }
            } elseif ($required) {
                $rules[$key] = 'required|string|max:5000';
            } else {
                $rules[$key] = 'nullable|string|max:5000';
            }
        }

        $request->validate($rules);

        // ponytail: Lainnya transform
        $input = $request->all();
        foreach ($questions as $q) {
            $key = $q['question_key'];
            $type = $q['question_type'] ?? 'essay';
            $raw = $q['question_options'];
            if (is_string($raw)) $raw = json_decode($raw, true);
            if (empty($raw['allow_other'])) continue;
            if ($type === 'pilihan_ganda' && isset($input[$key]) && $input[$key] === '__other__') {
                $otherText = trim((string)($request->input($key . '_other') ?? ''));
                if ($otherText === '') {
                    return back()->withErrors([$key => 'Isi Lainnya untuk '.$q['question_label'].' wajib diisi'])->withInput();
                }
                $request->merge([$key => $otherText]);
                $input[$key] = $otherText;
            } elseif ($type === 'checkbox' && isset($input[$key]) && is_array($input[$key]) && in_array('__other__', $input[$key], true)) {
                $otherText = trim((string)($request->input($key . '_other') ?? ''));
                if ($otherText === '') {
                    return back()->withErrors([$key => 'Isi Lainnya untuk '.$q['question_label'].' wajib diisi'])->withInput();
                }
                $replaced = array_map(fn($v) => $v === '__other__' ? $otherText : $v, $input[$key]);
                $request->merge([$key => $replaced]);
                $input[$key] = $replaced;
            }
        }
        $inputFiltered = array_filter($input, fn($k) => !str_ends_with($k, '_other'), ARRAY_FILTER_USE_KEY);

        $extracted = FormQuestion::extractAnswers('keluh_kesah', $inputFiltered);
        $extracted['regular'] = FormQuestion::flattenAnswers($extracted['regular']);
        $extracted['custom'] = FormQuestion::flattenAnswers($extracted['custom']);
        $builtInKeys = FormQuestion::getBuiltInKeys()['keluh_kesah'];
        $data = array_merge(array_fill_keys($builtInKeys, null), $extracted['regular']);

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

        $allAnswers = array_merge($extracted['regular'], $extracted['custom']);
        foreach ($allAnswers as $key => $value) {
            if (!is_string($value)) continue;
            foreach ($badWords as $word) {
                if (stripos($value, $word) !== false) {
                    return back()->withErrors(['message' => "Pesan mengandung kata {$word}! tolong diubah"])->withInput();
                }
            }
        }
        $data['custom_answers'] = !empty($extracted['custom']) ? $extracted['custom'] : null;

        $record = AspirationKeluhKesah::create($data);

        $receivers = TargetEmail::where('is_active', true)->pluck('email')->toArray();
        $answers = $record->toArray();

        foreach ($receivers as $to) {
            Notification::route('mail', $to)->notify(
                new KeluhKesahNotification($answers, $questions)
            );
        }

        return redirect()->back()->with('success', 'Aspirasi berhasil dikirim!');
    }


    public function show(AspirationKeluhKesah $aspirationKeluhKesah)
    {
        return response()->json($aspirationKeluhKesah);
    }

    public function destroy($id)
    {
        $item = AspirationKeluhKesah::findOrFail($id);
        $item->delete();
        return response()->json(['message' => 'Berhasil dihapus']);
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:aspiration_keluh_kesah,id',
        ]);
        AspirationKeluhKesah::whereIn('id', $request->ids)->delete();
        return response()->json(['message' => 'Berhasil dihapus']);
    }

    public function exportCsv(Request $request)
    {
        $query = AspirationKeluhKesah::query();

        if ($request->filled('search')) {
            $query->where('keluh_kesah', 'like', '%' . $request->search . '%');
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
            "Content-Disposition" => "inline; filename=aspirasi_keluhkesah.csv",
        ];

        $columns = ["Timestamps", "Pesan keluh kesah", "nomor telepon"];

        return response()->stream(function () use ($aspirations, $columns) {
            $handle = fopen("php://output", "w");
            fputcsv($handle, $columns, ";");

            foreach ($aspirations as $asp) {
                fputcsv($handle, [
                    $asp->created_at->timezone('Asia/Jakarta')->format('Y-m-d H:i:s'),
                    $asp->keluh_kesah,
                    $asp->phone_number,
                ], ";");
            }

            fclose($handle);
        }, 200, $headers);
    }
}
