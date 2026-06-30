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
            'questions.*.placeholder' => 'nullable|string',
            'questions.*.is_required' => 'nullable|boolean',
        ]);

        if ($formType === 'event') {
            if (!$entityId) {
                return back()->with('error', 'ID Event diperlukan');
            }
            
            FormQuestion::resetToDefault($formType, $entityId);
            
            foreach ($validated['questions'] as $index => $question) {
                FormQuestion::create([
                    'form_type' => $formType,
                    'entity_id' => $entityId,
                    'question_key' => $question['question_key'],
                    'question_label' => $question['question_label'],
                    'placeholder' => $question['placeholder'] ?? null,
                    'is_required' => $question['is_required'] ?? true,
                    'order' => $index,
                ]);
            }
            
            return Redirect::route('aspiration_events.by_event', $entityId)
                ->with('success', 'Pertanyaan event berhasil diperbarui');
        }
        
        if ($entityId) {
            return back()->with('error', 'Form type ini tidak mendukung entity_id');
        }

        FormQuestion::resetToDefault($formType, null);
        
        foreach ($validated['questions'] as $index => $question) {
            FormQuestion::create([
                'form_type' => $formType,
                'entity_id' => null,
                'question_key' => $question['question_key'],
                'question_label' => $question['question_label'],
                'placeholder' => $question['placeholder'] ?? null,
                'is_required' => $question['is_required'] ?? true,
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