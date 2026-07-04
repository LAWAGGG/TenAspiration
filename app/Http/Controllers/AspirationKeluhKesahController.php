<?php

namespace App\Http\Controllers;

use App\Models\AspirationKeluhKesah;
use App\Models\FormQuestion;
use App\Models\TargetEmail;
use App\Notifications\KeluhKesahNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

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
            if ($key === 'phone_number') {
                $rules[$key] = 'nullable|string|digits_between:8,15';
            } elseif ($q['is_required']) {
                $rules[$key] = 'required|string';
            } else {
                $rules[$key] = 'nullable|string';
            }
        }

        $request->validate($rules);

        $extracted = FormQuestion::extractAnswers('keluh_kesah', $request->all());

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

        foreach ($extracted['regular'] as $key => $value) {
            foreach ($badWords as $word) {
                if (stripos($value, $word) !== false) {
                    return back()->withErrors(['message' => "Pesan mengandung kata {$word}! tolong diubah"])->withInput();
                }
            }
        }

        $data = $extracted['regular'];
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
