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
        ]);

        AspirationEvent::create([
            "message" => $request->message,
            "to" => $request->to,
            "event_id" => $request->event_id,
            "other_to" => $request->other_to,
        ]);

        return redirect()->route('aspiration_events.index')->with('success', 'Aspirasi event berhasil dibuat!');
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
        ]);

        $aspiration = AspirationEvent::findOrFail($id);
        $aspiration->update([
            "message" => $request->message,
            "to" => $request->to,
            "event_id" => $request->event_id,
            "other_to" => $request->other_to,
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
        $aspirations = AspirationEvent::where("event_id", $eventId)->get();

        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "inline; filename=aspirations_event_$eventId.csv",
        ];

        $columns = ["ID", "Message", "To", "Other To", "Date"];

        return response()->stream(function () use ($aspirations, $columns) {
            $handle = fopen("php://output", "w");
            fputcsv($handle, $columns, ";");

            foreach ($aspirations as $asp) {
                fputcsv($handle, [
                    $asp->id,
                    $asp->message,
                    $asp->to,
                    $asp->other_to ?? "-",
                    $asp->created_at,
                ], ";");
            }

            fclose($handle);
        }, 200, $headers);
    }
}
