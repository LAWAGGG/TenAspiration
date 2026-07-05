<?php

namespace App\Http\Controllers;

use App\Models\Aspiration;
use App\Models\AspirationEvent;
use App\Models\AspirationKeluhKesah;
use App\Models\Event;
use App\Models\FormQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::latest()->get();
        $totalAspirations = Aspiration::count();
        $todayAspirations = Aspiration::whereDate('created_at', today())->count()
            + AspirationEvent::whereDate('created_at', today())->count()
            + AspirationKeluhKesah::whereDate('created_at', today())->count();
        $totalKeluhKesah = AspirationKeluhKesah::count();
        $totalEvents = Event::count();

        return view('dashboard', compact(
            'events',
            'totalAspirations',
            'todayAspirations',
            'totalKeluhKesah',
            'totalEvents'
        ));
    }

    public function getEvent()
    {
        $events = Event::latest()->get();
        $defaultQuestions = FormQuestion::getForForm('event');
        $eventQuestions = [];
        foreach ($events as $event) {
            $eventQuestions[$event->id] = FormQuestion::getForForm('event', $event->id);
        }
        return view('aspiration_forms.event-form', compact('events', 'defaultQuestions', 'eventQuestions'));
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

        $event->update($request->only(['name', 'description', 'date']));

        return redirect()->back()->with('success', 'Event berhasil diperbarui!');
    }

    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->back()->with('success', 'Event berhasil dihapus!');
    }
}
