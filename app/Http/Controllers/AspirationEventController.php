<?php

namespace App\Http\Controllers;

use App\Models\AspirationEvent;
use App\Models\Event;
use Illuminate\Http\Request;

class AspirationEventController extends Controller
{
     public function aspirationForm(){
        return view('aspiration_forms.event-form');
    }

    public function index()
    {
        $aspirations = AspirationEvent::orderByDesc("created_at")->with(['event'])->get();
        return view('aspiration_events.index', compact('aspirations'));
    }


    public function store(Request $request)
    {
        $request->validate([
            "message" => "required",
            "kesan_pesan" => "required",
            "perubahan_dari_event" => "required",
            "event_id" => "required|exists:events,id",
            "bad_moment" => "nullable|string",
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
            "bacot",
            "anj1ng",
            "m3m3k",
            "ngentod",
            "toloI",
        ];

        foreach ($badWords as $word) {
            if (stripos($request->message, $word) !== false) {
                return back()->withErrors(['message' => "Pesan mengandung kata {$word}! tolong diubah"])->withInput();
            }
            if (stripos($request->bad_moment, $word) !== false) {
                return back()->withErrors(['message' => "Pesan mengandung kata {$word}! tolong diubah"])->withInput();
            }
            if (stripos($request->kesan_pesan, $word) !== false) {
                return back()->withErrors(['message' => "Pesan mengandung kata {$word}! tolong diubah"])->withInput();
            }
            if (stripos($request->perubahan_dari_event, $word) !== false) {
                return back()->withErrors(['message' => "Pesan mengandung kata {$word}! tolong diubah"])->withInput();
            }
        }

        AspirationEvent::create([
            "message" => $request->message,
            "kesan_pesan" => $request->kesan_pesan,
            "perubahan_dari_event" => $request->perubahan_dari_event,
            "event_id" => $request->event_id,
            "bad_moment" => $request->bad_moment,
        ]);

        return redirect()->back()->with('success', 'Aspirasi event berhasil dikirim!');
    }


    public function show($id)
    {
        $aspiration = AspirationEvent::findOrFail($id);
        return view('aspiration_events.show', compact('aspiration'));
    }


    public function edit($id)
    {
        $aspiration = AspirationEvent::findOrFail($id);
        return view('aspiration_events.edit', compact('aspiration'));
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            "message" => "required",
            "kesan_pesan" => "required",
            "perubahan_dari_event" => "required",
            "event_id" => "required|exists:events,id",
            "bad_moment" => "nullable|string|max:255",
        ]);

        $aspiration = AspirationEvent::findOrFail($id);
        $aspiration->update([
            "message" => $request->message,
            "kesan_pesan" => $request->kesan_pesan,
            "perubahan_dari_event" => $request->perubahan_dari_event,
            "event_id" => $request->event_id,
            "bad_moment" => $request->bad_moment,
        ]);

        return redirect()->route('aspiration_events.index')->with('success', 'Aspirasi event berhasil diperbarui!');
    }


    public function destroy($id)
    {
        $aspiration = AspirationEvent::findOrFail($id);
        $aspiration->delete();

        return redirect()->route('aspiration_events.index')->with('success', 'Aspirasi event berhasil dihapus!');
    }


    public function showAspirationByEvent($eventId)
    {
        $event = Event::where('id', $eventId)->first();
        $eventName = $event->name ?? 'Event Tidak Dikenal';

        return view('aspiration_events.by_event', compact('eventId', 'eventName'));
    }

    public function fetchPaginatedByEvent(Request $request, $eventId)
    {
        $query = AspirationEvent::where('event_id', $eventId)->with(['event']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('message', 'like', "%{$search}%")
                  ->orWhere('kesan_pesan', 'like', "%{$search}%")
                  ->orWhere('perubahan_dari_event', 'like', "%{$search}%");
            });
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


    public function exportCsv(Request $request, $eventId)
    {
        $query = AspirationEvent::where("event_id", $eventId)->with(['event']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('message', 'like', '%' . $request->search . '%')
                  ->orWhere('kesan_pesan', 'like', '%' . $request->search . '%')
                  ->orWhere('perubahan_dari_event', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $aspirations = $query->orderByDesc('created_at')->get();
        $eventName = $aspirations->first()->event->name ?? 'Event';

        if ($aspirations->isEmpty()) {
            return redirect()->back()->with('error', 'Belum ada aspirasi untuk event ini.');
        }

        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "inline; filename=aspirations_event_{$eventName}.csv",
        ];

        $columns = ["timestamps", "Kritik, Saran, Masukan", "Kejadian buruk yang didapati", "Kesan dan Pesan", "Perubahan dari event sebelumnya"];

        return response()->stream(function () use ($aspirations, $columns) {
            $handle = fopen("php://output", "w");
            fputcsv($handle, $columns, ";");

            foreach ($aspirations as $asp) {
                fputcsv($handle, [
                    $asp->created_at->timezone('Asia/Jakarta')->format('Y-m-d H:i:s'),
                    $asp->message,
                    $asp->bad_moment ?? "-",
                    $asp->kesan_pesan,
                    $asp->perubahan_dari_event,
                ], ";");
            }

            fclose($handle);
        }, 200, $headers);
    }
}
