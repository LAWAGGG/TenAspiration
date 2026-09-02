<?php

namespace App\Http\Controllers;

use App\Models\FormQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class FormQuestionController extends Controller
{
    public function update(Request $request, string $formType, ?int $entityId = null)
    {
        $validated = $request->validate([
            'questions' => 'required|array',
            'questions.*.question_key' => 'required|string',
            'questions.*.question_label' => 'required|string',
            'questions.*.question_type' => 'required|in:essay,pilihan_ganda,checkbox',
            'questions.*.question_options' => 'nullable|json',
            'questions.*.placeholder' => 'nullable|string',
            'questions.*.is_required' => 'nullable|in:0,1,true,false',
        ]);

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
