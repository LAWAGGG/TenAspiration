<?php

namespace App\Http\Controllers;

use App\Models\Aspiration;
use App\Models\AspirationEvent;
use App\Models\AspirationKeluhKesah;
use App\Models\Event;
use App\Models\FormQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $events = Event::where('is_hidden', false)->latest()->get();
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

    public function toggleVisibility(Event $event)
    {
        $event->update(['is_hidden' => !$event->is_hidden]);

        $msg = $event->is_hidden
            ? 'Event berhasil disembunyikan dari form publik'
            : 'Event sekarang terlihat di form publik';

        return redirect()->back()->with('success', $msg);
    }

    public function statistics(Request $request)
    {
        $days = 30;
        $type = $request->query('type', 'all');

        $dailyTotals = [];

        $aspDaily = collect();
        $eventDaily = collect();
        $keluhDaily = collect();

        $includeAsp = in_array($type, ['all', 'audiensi']);
        $includeEvt = in_array($type, ['all', 'event']);
        $includeKel = in_array($type, ['all', 'keluh_kesah']);

        $dateRange = now()->subDays($days);

        if ($includeAsp) {
            $aspDaily = Aspiration::selectRaw('DATE(created_at) as date, COUNT(*) as total')
                ->where('created_at', '>=', $dateRange)
                ->groupBy('date')
                ->pluck('total', 'date');
        }
        if ($includeEvt) {
            $eventDaily = AspirationEvent::selectRaw('DATE(created_at) as date, COUNT(*) as total')
                ->where('created_at', '>=', $dateRange)
                ->groupBy('date')
                ->pluck('total', 'date');
        }
        if ($includeKel) {
            $keluhDaily = AspirationKeluhKesah::selectRaw('DATE(created_at) as date, COUNT(*) as total')
                ->where('created_at', '>=', $dateRange)
                ->groupBy('date')
                ->pluck('total', 'date');
        }

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $dailyTotals[] = [
                'date' => $date,
                'aspirations' => (int) ($aspDaily[$date] ?? 0),
                'events' => (int) ($eventDaily[$date] ?? 0),
                'keluh_kesah' => (int) ($keluhDaily[$date] ?? 0),
            ];
        }

        $result = [
            'type' => $type,
            'daily_totals' => $dailyTotals,
        ];

        if ($includeAsp) {
            $result['by_department'] = Aspiration::selectRaw('`to`, COUNT(*) as total')
                ->groupBy('to')
                ->orderByDesc('total')
                ->get()
                ->map(fn($item) => ['department' => $item->to, 'total' => $item->total]);

            $result['by_class'] = Aspiration::selectRaw('kelas, COUNT(*) as total')
                ->groupBy('kelas')
                ->orderByRaw("FIELD(kelas, 'X','XI','XII')")
                ->get()
                ->map(fn($item) => ['class' => $item->kelas, 'total' => $item->total]);
        }

        if ($type === 'event') {
            $result['by_event'] = \App\Models\Event::selectRaw('events.name, COUNT(aspiration_events.id) as total')
                ->leftJoin('aspiration_events', 'events.id', '=', 'aspiration_events.event_id')
                ->groupBy('events.id', 'events.name')
                ->orderByDesc('total')
                ->get()
                ->map(fn($item) => ['event' => $item->name, 'total' => $item->total]);
        }

        if ($type === 'all') {
            $result['by_type'] = [
                'aspirations' => Aspiration::count(),
                'events' => AspirationEvent::count(),
                'keluh_kesah' => AspirationKeluhKesah::count(),
            ];
        }

        return response()->json($result);
    }
}
