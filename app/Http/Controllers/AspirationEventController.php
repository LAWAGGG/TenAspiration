<?php

namespace App\Http\Controllers;

use App\Models\AspirationEvent;
use App\Models\Event;
use App\Models\FormQuestion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AspirationEventController extends Controller
{
    public function store(Request $request)
    {
        $eventId = $request->event_id;
        $questions = FormQuestion::getForForm('event', $eventId);

        $rules = ['event_id' => 'required|exists:events,id'];
        foreach ($questions as $q) {
            $key = $q['question_key'];
            $type = $q['question_type'] ?? 'essay';
            $required = $q['is_required'] ?? true;
            $raw = $q['question_options'];
            if (is_string($raw)) $raw = json_decode($raw, true);
            $allowed = [];
            if (is_array($raw) && isset($raw['options']) && is_array($raw['options'])) {
                $allowed = array_values(array_filter($raw['options'], fn($v) => is_string($v) && trim($v) !== ''));
            }
            $allowOther = !empty($raw['allow_other']);
            if ($type === 'checkbox') {
                if ($allowOther) {
                    $allowedWithOther = array_merge($allowed, ['__other__']);
                    $rules[$key] = $required ? 'required|array|min:1|max:10' : 'nullable|array|max:10';
                    $rules[$key . '.*'] = ['string', 'max:5000', Rule::in($allowedWithOther)];
                    $rules[$key . '_other'] = 'nullable|string|max:200';
                } else {
                    $rules[$key] = $required ? 'required|array|min:1|max:10' : 'nullable|array|max:10';
                    $rules[$key . '.*'] = $allowed ? ['string', 'max:5000', Rule::in($allowed)] : 'string|max:5000';
                }
            } elseif ($type === 'pilihan_ganda') {
                if ($allowOther) {
                    $allowedWithOther = array_merge($allowed, ['__other__']);
                    $base = $required ? 'required|string|max:5000' : 'nullable|string|max:5000';
                    $rules[$key] = [$base, Rule::in($allowedWithOther)];
                    $rules[$key . '_other'] = 'nullable|string|max:200';
                } else {
                    $base = $required ? 'required|string|max:5000' : 'nullable|string|max:5000';
                    $rules[$key] = $allowed ? [$base, Rule::in($allowed)] : $base;
                }
            } elseif ($required) {
                $rules[$key] = 'required|string|max:5000';
            } else {
                $rules[$key] = 'nullable|string|max:5000';
            }
        }

        $request->validate($rules);

        // ponytail: Lainnya transform — ganti __other__ dengan teks bebas
        $input = $request->except(['event_id']);
        foreach ($questions as $q) {
            $key = $q['question_key'];
            $type = $q['question_type'] ?? 'essay';
            $raw = $q['question_options'];
            if (is_string($raw)) $raw = json_decode($raw, true);
            if (empty($raw['allow_other'])) continue;
            if ($type === 'pilihan_ganda' && isset($input[$key]) && $input[$key] === '__other__') {
                $otherText = trim((string)($request->input($key . '_other') ?? ''));
                if ($otherText === '') {
                    return back()->withErrors([$key => 'Isi Lainnya untuk '.$q['question_label'].' wajib diisi'])->withInput();
                }
                $request->merge([$key => $otherText]);
                $input[$key] = $otherText;
            } elseif ($type === 'checkbox' && isset($input[$key]) && is_array($input[$key]) && in_array('__other__', $input[$key], true)) {
                $otherText = trim((string)($request->input($key . '_other') ?? ''));
                if ($otherText === '') {
                    return back()->withErrors([$key => 'Isi Lainnya untuk '.$q['question_label'].' wajib diisi'])->withInput();
                }
                $replaced = array_map(fn($v) => $v === '__other__' ? $otherText : $v, $input[$key]);
                $request->merge([$key => $replaced]);
                $input[$key] = $replaced;
            }
        }
        // ponytail: jangan simpan _other fields ke DB
        $inputFiltered = array_filter($input, fn($k) => !str_ends_with($k, '_other'), ARRAY_FILTER_USE_KEY);

        $extracted = FormQuestion::extractAnswers('event', $inputFiltered);
        $extracted['regular'] = FormQuestion::flattenAnswers($extracted['regular']);
        $extracted['custom'] = FormQuestion::flattenAnswers($extracted['custom']);
        $builtInKeys = FormQuestion::getBuiltInKeys()['event'];
        $data = array_merge(array_fill_keys($builtInKeys, null), $extracted['regular']);
        $data['event_id'] = $eventId;
        $data['custom_answers'] = !empty($extracted['custom']) ? $extracted['custom'] : null;

        $badWords = [
            "anjing","bangsat","goblok","kontol","bego","jing","jir","qontol","puqi","anjay","anjir","a n j i n g",
            "tolol","monyet","babi","memek","pepek","puki","jancuk","jancok","kampret","kntl","kntol","kintil",
            "pantek","panteq","bajingan","badjingan","fuck","shit","asshole","anying","lonte","kontoI","4njing","babl","bacot",
        ];

        $allAnswers = array_merge($extracted['regular'], $extracted['custom']);
        foreach ($allAnswers as $key => $value) {
            if (!is_string($value)) continue;
            foreach ($badWords as $word) {
                if (stripos($value, $word) !== false) {
                    return back()->withErrors(['message' => "Pesan mengandung kata {$word}! tolong diubah"])->withInput();
                }
            }
        }

        AspirationEvent::create($data);

        return redirect()->back()->with('success', 'Aspirasi event berhasil dikirim!');
    }

    public function update(Request $request, $id)
    {
        $aspiration = AspirationEvent::findOrFail($id);
        $questions = FormQuestion::getForForm('event', $aspiration->event_id);

        $rules = [];
        foreach ($questions as $q) {
            $key = $q['question_key'];
            $type = $q['question_type'] ?? 'essay';
            $required = $q['is_required'] ?? true;
            $raw = $q['question_options'];
            if (is_string($raw)) $raw = json_decode($raw, true);
            $allowed = [];
            if (is_array($raw) && isset($raw['options']) && is_array($raw['options'])) {
                $allowed = array_values(array_filter($raw['options'], fn($v) => is_string($v) && trim($v) !== ''));
            }
            $allowOther = !empty($raw['allow_other']);
            if ($type === 'checkbox') {
                if ($allowOther) {
                    $allowedWithOther = array_merge($allowed, ['__other__']);
                    $rules[$key] = $required ? 'required|array|min:1|max:10' : 'nullable|array|max:10';
                    $rules[$key . '.*'] = ['string', 'max:5000', Rule::in($allowedWithOther)];
                    $rules[$key . '_other'] = 'nullable|string|max:200';
                } else {
                    $rules[$key] = $required ? 'required|array|min:1|max:10' : 'nullable|array|max:10';
                    $rules[$key . '.*'] = $allowed ? ['string', 'max:5000', Rule::in($allowed)] : 'string|max:5000';
                }
            } elseif ($type === 'pilihan_ganda') {
                if ($allowOther) {
                    $allowedWithOther = array_merge($allowed, ['__other__']);
                    $base = $required ? 'required|string|max:5000' : 'nullable|string|max:5000';
                    $rules[$key] = [$base, Rule::in($allowedWithOther)];
                    $rules[$key . '_other'] = 'nullable|string|max:200';
                } else {
                    $base = $required ? 'required|string|max:5000' : 'nullable|string|max:5000';
                    $rules[$key] = $allowed ? [$base, Rule::in($allowed)] : $base;
                }
            } elseif ($required) {
                $rules[$key] = 'required|string|max:5000';
            } else {
                $rules[$key] = 'nullable|string|max:5000';
            }
        }

        $request->validate($rules);

        // ponytail: Lainnya transform for update
        $input = $request->except(['event_id']);
        foreach ($questions as $q) {
            $key = $q['question_key'];
            $type = $q['question_type'] ?? 'essay';
            $raw = $q['question_options'];
            if (is_string($raw)) $raw = json_decode($raw, true);
            if (empty($raw['allow_other'])) continue;
            if ($type === 'pilihan_ganda' && isset($input[$key]) && $input[$key] === '__other__') {
                $otherText = trim((string)($request->input($key . '_other') ?? ''));
                if ($otherText === '') {
                    return back()->withErrors([$key => 'Isi Lainnya untuk '.$q['question_label'].' wajib diisi'])->withInput();
                }
                $request->merge([$key => $otherText]);
                $input[$key] = $otherText;
            } elseif ($type === 'checkbox' && isset($input[$key]) && is_array($input[$key]) && in_array('__other__', $input[$key], true)) {
                $otherText = trim((string)($request->input($key . '_other') ?? ''));
                if ($otherText === '') {
                    return back()->withErrors([$key => 'Isi Lainnya untuk '.$q['question_label'].' wajib diisi'])->withInput();
                }
                $replaced = array_map(fn($v) => $v === '__other__' ? $otherText : $v, $input[$key]);
                $request->merge([$key => $replaced]);
                $input[$key] = $replaced;
            }
        }
        $inputFiltered = array_filter($input, fn($k) => !str_ends_with($k, '_other'), ARRAY_FILTER_USE_KEY);

        $extracted = FormQuestion::extractAnswers('event', $inputFiltered);
        $extracted['regular'] = FormQuestion::flattenAnswers($extracted['regular']);
        $extracted['custom'] = FormQuestion::flattenAnswers($extracted['custom']);
        $builtInKeys = FormQuestion::getBuiltInKeys()['event'];
        $data = array_merge(array_fill_keys($builtInKeys, null), $extracted['regular']);
        $data['custom_answers'] = !empty($extracted['custom']) ? $extracted['custom'] : null;

        $aspiration->update($data);

        return redirect()->route('aspiration_events.index')->with('success', 'Aspirasi event berhasil diperbarui!');
    }


    public function destroy($id)
    {
        $aspiration = AspirationEvent::findOrFail($id);
        $aspiration->delete();

        return response()->json(['message' => 'Aspirasi event berhasil dihapus']);
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:aspiration_events,id',
        ]);
        AspirationEvent::whereIn('id', $request->ids)->delete();
        return response()->json(['message' => 'Aspirasi event berhasil dihapus']);
    }


    public function showAspirationByEvent($eventId)
    {
        $event = Event::where('id', $eventId)->first();
        $eventName = $event->name ?? 'Event Tidak Dikenal';
        $questions = FormQuestion::getForForm('event', $eventId);
        $defaultQuestions = FormQuestion::getDefaults()['event'];

        return view('aspiration_events.by_event', compact('eventId', 'eventName', 'questions', 'defaultQuestions'));
    }

    public function fetchPaginatedByEvent(Request $request, $eventId)
    {
        $query = AspirationEvent::where('event_id', $eventId)->with(['event']);
        $questions = FormQuestion::getForForm('event', $eventId);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search, $questions) {
                foreach ($questions as $question) {
                    $key = $question['question_key'];
                    $builtIn = FormQuestion::isBuiltInKey('event', $key);
                    if ($builtIn) {
                        $q->orWhere($key, 'like', "%{$search}%");
                    }
                }
                $q->orWhere('custom_answers', 'like', "%{$search}%");
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
        $questions = FormQuestion::getForForm('event', $eventId);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search, $questions) {
                foreach ($questions as $question) {
                    $key = $question['question_key'];
                    if (FormQuestion::isBuiltInKey('event', $key)) {
                        $q->orWhere($key, 'like', '%' . $search . '%');
                    }
                }
                $q->orWhere('custom_answers', 'like', '%' . $search . '%');
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

        $columns = ["timestamps"];
        foreach ($questions as $q) {
            $columns[] = $q['question_label'];
        }

        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "inline; filename=aspirations_event_{$eventName}.csv",
        ];

        return response()->stream(function () use ($aspirations, $columns, $questions) {
            $handle = fopen("php://output", "w");
            fputcsv($handle, $columns, ";");

            foreach ($aspirations as $asp) {
                $row = [$asp->created_at->timezone('Asia/Jakarta')->format('Y-m-d H:i:s')];
                $customAnswers = $asp->custom_answers ?? [];
                foreach ($questions as $q) {
                    $key = $q['question_key'];
                    if (FormQuestion::isBuiltInKey('event', $key)) {
                        $row[] = $asp->{$key} ?? '-';
                    } else {
                        $row[] = $customAnswers[$key] ?? '-';
                    }
                }
                fputcsv($handle, $row, ";");
            }

            fclose($handle);
        }, 200, $headers);
    }
}
