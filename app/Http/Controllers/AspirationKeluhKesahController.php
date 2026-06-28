<?php

namespace App\Http\Controllers;

use App\Models\AspirationKeluhKesah;
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
        return view('aspiration_keluh_kesah.index');
    }

    public function fetchPaginated(Request $request)
    {
        $query = AspirationKeluhKesah::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('keluh_kesah', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");
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
        return view('aspiration_forms.keluh-kesah-form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "keluh_kesah" => "required|string",
            "phone_number" => "required|digits_between:8,12"
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

        foreach ($badWords as $word) {
            if (stripos($request->keluh_kesah, $word) !== false) {
                return back()->withErrors(['message' => "Pesan mengandung kata {$word}! tolong diubah"])->withInput();
            }
        }

        AspirationKeluhKesah::create([
            "keluh_kesah" => $request->keluh_kesah,
            "phone_number" => $request->phone_number,
        ]);

        $receivers = [
            'yunitakamali72@gmail.com',
            'mujahidrobbanisholahudin@gmail.com',
            'ahmadfagih.arrifai@gmail.com',
            'desita1412@gmail.com',
            'sayutiazwarmi67@gmail.com'
        ];

        foreach ($receivers as $to) {
            Notification::route('mail', $to)->notify(
                new KeluhKesahNotification(
                    $request->keluh_kesah,
                    $request->phone_number
                )
            );
        }

        return redirect()->back()->with('success', 'Aspirasi berhasil dikirim!');
    }


    public function show(AspirationKeluhKesah $aspirationKeluhKesah)
    {
        return response()->json($aspirationKeluhKesah);
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
