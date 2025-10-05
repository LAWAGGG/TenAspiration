<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::latest()->get();
        return view('dashboard', compact('events'));
    }

    public function store(Request $request)
    {
        $val = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'nullable|date',
        ]);

        if ($val->fails()) {
            return redirect()->back()->withErrors($val)->withInput();
        }

        Event::create([
            'name' => $request->name,
            'description' => $request->description,
            'date' => $request->date,
        ]);

        return redirect()->back()->with('success', 'Event berhasil ditambahkan!');
    }

    public function update(Request $request, Event $event)
    {
        $val = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'nullable|date',
        ]);

        if ($val->fails()) {
            return redirect()->back()->withErrors($val)->withInput();
        }

        $event->update($request->all());

        return redirect()->back()->with('success', 'Event berhasil diperbarui!');
    }

    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->back()->with('success', 'Event berhasil dihapus!');
    }
}
