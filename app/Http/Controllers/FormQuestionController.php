<?php

namespace App\Http\Controllers;

use App\Models\FormQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class FormQuestionController extends Controller
{
    public function update(Request $request, string $formType, ?int $entityId = null)
    {
        abort_unless(in_array($formType, ['event', 'audiensi', 'keluh_kesah']), 404);

        $validated = $request->validate([
            'questions' => 'required|array|max:20',
            'questions.*.question_key' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z0-9_]+$/'],
            'questions.*.question_label' => 'required|string|max:150',
            'questions.*.question_type' => 'required|in:essay,pilihan_ganda,checkbox',
            'questions.*.question_options' => 'nullable|json',
            'questions.*.placeholder' => 'nullable|string|max:255',
            'questions.*.is_required' => 'nullable|in:0,1,true,false',
        ]);

        // ponytail: structural guard for question_options — prevents DoS / stored junk
        foreach ($validated['questions'] as $idx => $q) {
            $type = $q['question_type'] ?? 'essay';
            if ($type !== 'essay') {
                $decoded = json_decode($q['question_options'] ?? '{}', true);
                if (!is_array($decoded) || !isset($decoded['options']) || !is_array($decoded['options'])) {
                    return back()->withErrors(['questions' => 'Opsi pertanyaan '.($idx+1).' tidak valid'])->withInput();
                }
                $filtered = array_values(array_filter($decoded['options'], fn($v) => is_string($v) && trim($v) !== ''));
                if (count($filtered) < 2 || count($filtered) > 10) {
                    return back()->withErrors(['questions' => 'Opsi pertanyaan '.($idx+1).' harus 2-10 opsi terisi'])->withInput();
                }
                foreach ($filtered as $opt) {
                    if (mb_strlen($opt) > 100) {
                        return back()->withErrors(['questions' => 'Opsi pertanyaan '.($idx+1).' maksimal 100 karakter'])->withInput();
                    }
                }
                if (count($filtered) !== count(array_unique($filtered))) {
                    return back()->withErrors(['questions' => 'Opsi pertanyaan '.($idx+1).' tidak boleh duplikat'])->withInput();
                }
                if (isset($decoded['allow_other']) && !is_bool($decoded['allow_other'])) {
                    return back()->withErrors(['questions' => 'Opsi Lainnya pertanyaan '.($idx+1).' tidak valid'])->withInput();
                }
                // reserve sentinel __other__ — tidak boleh dipakai sebagai opsi biasa
                if (in_array('__other__', $filtered, true)) {
                    return back()->withErrors(['questions' => 'Opsi tidak boleh bernama __other__'])->withInput();
                }
            }
        }

        if ($formType === 'event') {
            if (! $entityId) {
                return back()->with('error', 'ID Event diperlukan');
            }

            FormQuestion::resetToDefault($formType, $entityId);

            foreach ($validated['questions'] as $index => $question) {
                $key = self::resolveQuestionKey($question['question_key'], $question['question_label']);
                FormQuestion::create([
                    'form_type' => $formType,
                    'entity_id' => $entityId,
                    'question_key' => $key,
                    'question_label' => $question['question_label'],
                    'question_type' => $question['question_type'] ?? 'essay',
                    'question_options' => $question['question_options'] ?? null,
                    'placeholder' => $question['placeholder'] ?? null,
                    'is_required' => filter_var($question['is_required'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'order' => $index,
                ]);
            }

            return back()->with('success', 'Pertanyaan event berhasil diperbarui');
        }

        FormQuestion::resetToDefault($formType, null);

        foreach ($validated['questions'] as $index => $question) {
            $key = self::resolveQuestionKey($question['question_key'], $question['question_label']);
            FormQuestion::create([
                'form_type' => $formType,
                'entity_id' => null,
                'question_key' => $key,
                'question_label' => $question['question_label'],
                'question_type' => $question['question_type'] ?? 'essay',
                'question_options' => $question['question_options'] ?? null,
                'placeholder' => $question['placeholder'] ?? null,
                'is_required' => filter_var($question['is_required'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'order' => $index,
            ]);
        }

        $redirectRoutes = [
            'audiensi' => 'aspirations.index',
            'keluh_kesah' => 'aspiration_keluhkesah.index',
        ];

        return Redirect::route($redirectRoutes[$formType] ?? 'dashboard')
            ->with('success', 'Pertanyaan berhasil diperbarui');
    }

    /**
     * Jika question_key masih berupa timestamp random (custom_XXXXXXXXXX),
     * ganti dengan label yang di-lowercase dan di-slug-kan agar human-readable
     * dan tersimpan permanen di kolom `to` pada aspirations.
     */
    private static function resolveQuestionKey(string $currentKey, string $label): string
    {
        // Jika key masih dalam format custom_<timestamp> (13 digit angka), generate dari label
        if (preg_match('/^custom_\d{10,}$/', $currentKey)) {
            return self::labelToKey($label);
        }

        // Key sudah bermakna (built-in atau sudah di-set manual), pertahankan
        return $currentKey;
    }

    /**
     * Konversi label menjadi key: lowercase, spasi → underscore, strip karakter non-alphanumeric.
     * Contoh: "Wakil Kesiswaan" → "wakil_kesiswaan"
     */
    private static function labelToKey(string $label): string
    {
        $key = mb_strtolower(trim($label));
        $key = preg_replace('/\s+/', '_', $key);
        $key = preg_replace('/[^\w]/', '', $key);

        return $key ?: 'pertanyaan';
    }

    public function reset(string $formType, ?int $entityId = null)
    {
        abort_unless(in_array($formType, ['event', 'audiensi', 'keluh_kesah']), 404);
        FormQuestion::resetToDefault($formType, $entityId);

        if ($formType === 'event' && $entityId) {
            return Redirect::route('aspiration_events.by_event', $entityId)
                ->with('success', 'Pertanyaan event direset ke default');
        }

        $redirectRoutes = [
            'audiensi' => 'aspirations.index',
            'keluh_kesah' => 'aspiration_keluhkesah.index',
        ];

        return Redirect::route($redirectRoutes[$formType] ?? 'dashboard')
            ->with('success', 'Pertanyaan direset ke default');
    }
}
