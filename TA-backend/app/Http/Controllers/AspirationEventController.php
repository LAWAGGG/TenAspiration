<?php

namespace App\Http\Controllers;

use App\Models\Aspiration;
use App\Models\AspirationEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AspirationEventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $asp = AspirationEvent::get()->sortByDesc("created_at");

        return response()->json([
            "AspirationEvent" => collect($asp)->map(function ($asp) {
                return [
                    "id" => $asp->id,
                    "message" => $asp->message,
                    "event_id" => $asp->event_id,
                    "to" => $asp->to,
                    "other_to" => $asp->other_to,
                    "date" => $asp->created_at
                ];
            })
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $val = Validator::make($request->all(), [
            "message" => "required",
            "to" => "required",
            "event_id" => "required|exists:events,id",
            "other_to" => "nullable|string|max:255"
        ]);

        if ($val->fails()) {
            return response()->json([
                "message" => "invalid fields"
            ], 422);
        }

        $asp = AspirationEvent::create([
            "message" => $request->message,
            "to" => $request->to,
            "event_id" => $request->event_id,
            "other_to" => $request->other_to
        ]);

        return response()->json([
            "message" => "Aspiration event created successfully",
            "aspiration_event" => $asp
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(AspirationEvent $aspirationEvent)
    {
        if (!$aspirationEvent) {
            return response()->json([
                "message" => "Aspiration event not found"
            ], 404);
        }

        return response()->json([
            "AspirationEvent" => $aspirationEvent
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AspirationEvent $aspirationEvent)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AspirationEvent $aspirationEvent)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AspirationEvent $aspirationEvent)
    {
        if (!$aspirationEvent) {
            return response()->json([
                "message" => "Aspiration event not found"
            ], 404);
        }

        $aspirationEvent->delete();

        return response()->json([
            "message" => "Aspiration event deleted successfully"
        ]);
    }

    public function showAspirationByEvent($eventId)
    {
        $aspirations = AspirationEvent::where('event_id', $eventId)->get()->sortByDesc("created_at");

        if (!$aspirations) {
            return response()->json([
                "message" => "No aspirations found for this event"
            ], 404);
        }

        return response()->json([
            "Aspirations" => collect($aspirations)->map(function ($asp) {
                return [
                    "id" => $asp->id,
                    "message" => $asp->message,
                    "event_id" => $asp->event_id,
                    "to" => $asp->to,
                    "other_to" => $asp->other_to,
                    "date" => $asp->created_at
                ];
            })
        ]);
    }

    public function exportCsv($eventId)
    {
        $aspirations = AspirationEvent::where("event_id", $eventId)->get();

        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "inline; filename=aspirations_event_$eventId.csv",
        ];

        $columns = ["ID", "Message", "To","Other To", "Date"];

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
