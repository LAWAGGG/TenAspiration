<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $events = Event::get();

        return response()->json([
            "Events"=>$events
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
        $val = Validator::make($request->all(),[
            "name"=>"required|string|max:255",
            "description"=>"nullable|string",
            "date"=>"nullable|date"
        ]);

        if($val->fails()){
            return response()->json([
                "message"=>"invalid fields"
            ],422);
        }

        $event = Event::create([
            "name"=>$request->name,
            "description"=>$request->description,
            "date"=>$request->date
        ]);

        return response()->json([
            "message"=>"Event created successfully",
            "event"=>$event
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        if(!$event){
            return response()->json([
                "message"=>"Event not found"
            ],404);
        }

        return response()->json([
            "Event"=>$event
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        if(!$event){
            return response()->json([
                "message"=>"Event not found"
            ],404);
        }

        $val = Validator::make($request->all(),[
            "name"=>"sometimes|required|string|max:255",
            "description"=>"sometimes|nullable|string",
            "date"=>"sometimes|nullable|date"
        ]);

        if($val->fails()){
            return response()->json([
                "message"=>"invalid fields"
            ],422);
        }

        $event->update($request->all());

        return response()->json([
            "message"=>"Event updated successfully",
            "event"=>$event
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        if(!$event){
            return response()->json([
                "message"=>"Event not found"
            ],404);
        }

        $event->delete();

        return response()->json([
            "message"=>"Event deleted successfully"
        ]);
    }
}
