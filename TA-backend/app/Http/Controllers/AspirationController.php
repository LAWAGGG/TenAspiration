<?php

namespace App\Http\Controllers;

use App\Models\Aspiration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AspirationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $asp = Aspiration::get()->sortByDesc("created_at");

        return response()->json([
            "Aspiration" => collect($asp)->map(function ($asp) {
                return [
                    "id" => $asp->id,
                    "message" => $asp->message,
                    "to" => $asp->to,
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
        ]);

        if ($val->fails()) {
            return response()->json([
                "message" => "invalid fields"
            ], 422);
        }

        $message =  Aspiration::create([
            "message" => $request->message,
            "to" => $request->to
        ]);

        return response()->json([
            "message" => $message
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $aspiration = Aspiration::find($id);

        if (!$aspiration) {
            return response()->json([
                "message" => "aspiration not found"
            ], 404);
        }

        return response()->json([
            "Aspiration" => $aspiration
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Aspiration $aspiration)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Aspiration $aspiration)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Aspiration $aspiration)
    {
        if (!$aspiration) {
            return response()->json([
                "message" => "aspiration not found"
            ], 404);
        }

        $aspiration->delete();

        return response()->json([
            "message" => "aspiration deleted"
        ]);
    }

    public function StoreEvent(Request $request)
    {
        $val = Validator::make($request->all(), [
            "message" => "required",
            "to" => "required",
        ]);

        if ($val->fails()) {
            return response()->json([
                "message" => "invalid fields"
            ], 422);
        }

        $message =  Aspiration::create([
            "message" => $request->message,
            "to" => $request->to
        ]);

        return response()->json([
            "message" => $message
        ]);
    }

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
