<?php

namespace App\Http\Controllers;

use App\Models\AspirationEvent;
use Illuminate\Http\Request;

class AspirationEventController extends Controller
{

    public function index()
    {
        $aspirations = AspirationEvent::orderByDesc("created_at")->get();
        return view('aspiration_events.index', compact('aspirations'));
    }

    public function create()
    {
        return view('aspiration_events.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            "message" => "required",
            "to" => "required",
            "event_id" => "required|exists:events,id",
            "other_to" => "nullable|string|max:255",
            "bad_moment" => "nullable|string|max:255",
        ]);

        $badWords = [
            "anjing",
            "bangsat",
            "goblok",
            "tai",
            "kontol",
            "bego",
            "anj",
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
            "kntl",
            "kntol",
            "kintil",
            "pantek",
            "panteq",
            "pantek",
            "panteq",
            "bajingan",
            "fuck",
            "shit",
            "asshole"
        ];
        foreach ($badWords as $word) {
            if (stripos($request->message, $word) !== false) {
                return back()->withErrors(['message' => 'Pesan mengandung kata tidak pantas!'])->withInput();
            }
            if (stripos($request->bad_moment, $word) !== false) {
                return back()->withErrors(['message' => 'Pesan mengandung kata tidak pantas!'])->withInput();
            }
        }

        AspirationEvent::create([
            "message" => $request->message,
            "to" => $request->to,
            "event_id" => $request->event_id,
            "other_to" => $request->other_to,
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
            "to" => "required",
            "event_id" => "required|exists:events,id",
            "other_to" => "nullable|string|max:255",
            "bad_moment" => "nullable|string|max:255",
        ]);

        $aspiration = AspirationEvent::findOrFail($id);
        $aspiration->update([
            "message" => $request->message,
            "to" => $request->to,
            "event_id" => $request->event_id,
            "other_to" => $request->other_to,
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
        $aspirations = AspirationEvent::where('event_id', $eventId)->orderByDesc('created_at')->get();

        return view('aspiration_events.by_event', compact('aspirations', 'eventId'));
    }


    public function exportCsv($eventId)
    {
        $aspirations = AspirationEvent::where("event_id", $eventId)->with(['event'])->get();
        $eventName = $aspirations->first()->event->name;

        if ($aspirations->isEmpty()) {
            return redirect()->back()->with('error', 'Belum ada aspirasi untuk event ini.');
        }

        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "inline; filename=aspirations_event_{$eventName}.csv",
        ];

        $columns = ["ID", "Message","Bad Moment", "To", "Other To", "Date"];

        return response()->stream(function () use ($aspirations, $columns) {
            $handle = fopen("php://output", "w");
            fputcsv($handle, $columns, ";");

            foreach ($aspirations as $asp) {
                fputcsv($handle, [
                    $asp->id,
                    $asp->message,
                    $asp->bad_moment ?? "-",
                    $asp->to,
                    $asp->other_to ?? "-",
                    $asp->created_at->timezone('Asia/Jakarta')->format('Y-m-d H:i:s'),
                ], ";");
            }

            fclose($handle);
        }, 200, $headers);
    }
}
