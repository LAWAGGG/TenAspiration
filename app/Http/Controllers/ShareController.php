<?php

namespace App\Http\Controllers;

use App\Models\Aspiration;
use App\Models\AspirationEvent;
use App\Models\AspirationKeluhKesah;
use App\Models\FormQuestion;
use App\Models\SharedLink;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ShareController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:aspiration,aspiration_event,keluh_kesah',
            'selected_ids' => 'nullable|array',
            'filters' => 'nullable|array',
            'title' => 'nullable|string|max:255',
        ]);

        $token = Str::random(8);

        $sharedLink = SharedLink::create([
            'token' => $token,
            'type' => $request->type,
            'selected_ids' => $request->selected_ids,
            'filters' => $request->filters,
            'title' => $request->title,
            'created_by' => auth()->id(),
        ]);

        return response()->json([
            'url' => url("/share/{$token}"),
            'token' => $token,
        ]);
    }

    public function show($token)
    {
        $share = SharedLink::where('token', $token)->firstOrFail();
        return view('share.show', compact('share'));
    }

    public function fetchShared($token, Request $request)
    {
        $share = SharedLink::where('token', $token)->firstOrFail();

        $query = match ($share->type) {
            'aspiration' => Aspiration::query(),
            'aspiration_event' => AspirationEvent::with(['event']),
            'keluh_kesah' => AspirationKeluhKesah::query(),
        };

        if (!empty($share->selected_ids)) {
            $query->whereIn('id', $share->selected_ids);
        }

        if (!empty($share->filters)) {
            $filters = $share->filters;

            if ($share->type === 'aspiration') {
                if (!empty($filters['search'])) {
                    $search = $filters['search'];
                    $query->where(function ($q) use ($search) {
                        $q->where('message', 'like', "%{$search}%")
                          ->orWhere('to', 'like', "%{$search}%")
                          ->orWhere('kelas', 'like', "%{$search}%");
                    });
                }
                if (!empty($filters['filterBagian'])) {
                    $query->where('to', $filters['filterBagian']);
                }
                if (!empty($filters['filterKelas'])) {
                    $query->where('kelas', $filters['filterKelas']);
                }
                if (!empty($filters['dateFrom'])) {
                    $query->whereDate('created_at', '>=', $filters['dateFrom']);
                }
                if (!empty($filters['dateTo'])) {
                    $query->whereDate('created_at', '<=', $filters['dateTo']);
                }
            } elseif ($share->type === 'aspiration_event') {
                if (!empty($filters['search'])) {
                    $search = $filters['search'];
                    $query->where(function ($q) use ($search) {
                        $q->where('message', 'like', "%{$search}%")
                          ->orWhere('kesan_pesan', 'like', "%{$search}%")
                          ->orWhere('perubahan_dari_event', 'like', "%{$search}%");
                    });
                }
                if (!empty($filters['eventId'])) {
                    $query->where('event_id', $filters['eventId']);
                }
                if (!empty($filters['dateFrom'])) {
                    $query->whereDate('created_at', '>=', $filters['dateFrom']);
                }
                if (!empty($filters['dateTo'])) {
                    $query->whereDate('created_at', '<=', $filters['dateTo']);
                }
            } elseif ($share->type === 'keluh_kesah') {
                if (!empty($filters['search'])) {
                    $search = $filters['search'];
                    $query->where(function ($q) use ($search) {
                        $q->where('keluh_kesah', 'like', "%{$search}%")
                          ->orWhere('phone_number', 'like', "%{$search}%")
                          ->orWhere('custom_answers', 'like', "%{$search}%");
                    });
                }
                if (!empty($filters['dateFrom'])) {
                    $query->whereDate('created_at', '>=', $filters['dateFrom']);
                }
                if (!empty($filters['dateTo'])) {
                    $query->whereDate('created_at', '<=', $filters['dateTo']);
                }
            }
        }

        $data = $query->orderByDesc('created_at')->get();

        $questions = [];
        if ($share->type === 'keluh_kesah') {
            $questions = FormQuestion::getForForm('keluh_kesah');
        } elseif ($share->type === 'aspiration') {
            $questions = FormQuestion::getForForm('audiensi');
        } elseif ($share->type === 'aspiration_event') {
            $first = $data->first();
            $eventId = $first ? $first->event_id : null;
            if ($eventId) {
                $questions = FormQuestion::getForForm('event', $eventId);
            }
        }

        // Get available bagian/department options for audiensi filtering
        $bagianOptions = [];
        if ($share->type === 'aspiration') {
            $bagianOptions = FormQuestion::where('form_type', 'audiensi')
                ->whereNull('entity_id')
                ->orderBy('order')
                ->get(['question_key', 'question_label'])
                ->map(function($q) {
                    return [
                        'value' => $q->question_key,
                        'label' => $q->question_label,
                    ];
                })
                ->toArray();
        }

        return response()->json([
            'share' => $share,
            'data' => $data,
            'type' => $share->type,
            'questions' => $questions,
            'bagianOptions' => $bagianOptions,
        ]);
    }
}
