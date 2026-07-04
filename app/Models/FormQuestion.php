<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormQuestion extends Model
{
    protected $guarded = [];

    protected $casts = ['is_required' => 'boolean'];

    protected static function getBuiltInKeys(): array
    {
        return [
            'event' => ['message', 'kesan_pesan', 'bad_moment', 'perubahan_dari_event'],
            'audiensi' => ['wakil kesiswaan', 'wakil sarpras', 'wakil kurikulum', 'wakil humas', 'tata usaha', 'OSIS', 'MPK', 'Ekskul', 'umum'],
            'keluh_kesah' => ['keluh_kesah', 'phone_number'],
        ];
    }

    public static function getDefaults(): array
    {
        return [
            'event' => [
                ['key' => 'message', 'label' => 'Kritik, Saran & Masukkan', 'placeholder' => 'Berikan kritik, saran dan masukan dalam event ini', 'required' => true],
                ['key' => 'kesan_pesan', 'label' => 'Kesan Pesan', 'placeholder' => 'Berikan kesan dan pesan setelah dilaksanakannya event ini', 'required' => true],
                ['key' => 'bad_moment', 'label' => 'Kejadian Buruk', 'placeholder' => 'jika tidak ada, berikan (-)', 'required' => true],
                ['key' => 'perubahan_dari_event', 'label' => 'Perubahan dari Event Sebelumnya', 'placeholder' => 'Apa yang paling kalian rasakan perubahan dari event sebelumnya?', 'required' => true],
            ],
            'audiensi' => [
                ['key' => 'wakil kesiswaan', 'label' => 'Wakil Kesiswaan', 'placeholder' => 'Tulis kritik, saran, dan masukan kepada bidang ini..', 'required' => true],
                ['key' => 'wakil sarpras', 'label' => 'Wakil Sarana Prasarana', 'placeholder' => 'Tulis kritik, saran, dan masukan kepada bidang ini..', 'required' => true],
                ['key' => 'wakil kurikulum', 'label' => 'Wakil Kurikulum', 'placeholder' => 'Tulis kritik, saran, dan masukan kepada bidang ini..', 'required' => true],
                ['key' => 'wakil humas', 'label' => 'Wakil Humas', 'placeholder' => 'Tulis kritik, saran, dan masukan kepada bidang ini..', 'required' => true],
                ['key' => 'tata usaha', 'label' => 'Tata Usaha', 'placeholder' => 'Tulis kritik, saran, dan masukan kepada bidang ini..', 'required' => true],
                ['key' => 'OSIS', 'label' => 'OSIS', 'placeholder' => 'Tulis kritik, saran, dan masukan kepada bidang ini..', 'required' => true],
                ['key' => 'MPK', 'label' => 'MPK', 'placeholder' => 'Tulis kritik, saran, dan masukan kepada bidang ini..', 'required' => true],
                ['key' => 'Ekskul', 'label' => 'Ekskul', 'placeholder' => 'Tulis kritik, saran, dan masukan kepada bidang ini..', 'required' => true],
                ['key' => 'umum', 'label' => 'Umum', 'placeholder' => 'Tulis kritik, saran, dan masukan kepada bidang ini..', 'required' => true],
            ],
            'keluh_kesah' => [
                ['key' => 'keluh_kesah', 'label' => 'Keluh / Kesah', 'placeholder' => 'Ceritakan keluh kesahmu di sini...', 'required' => true],
                ['key' => 'phone_number', 'label' => 'Nomor Telepon', 'placeholder' => '08xxxxxxxxxx', 'required' => false],
            ],
        ];
    }

    public static function getForForm(string $formType, ?int $entityId = null): array
    {
        if ($formType === 'event' && $entityId) {
            $custom = self::where('form_type', $formType)
                ->where('entity_id', $entityId)
                ->orderBy('order')
                ->get();

            if ($custom->isNotEmpty()) {
                return $custom->toArray();
            }
        }

        if ($formType !== 'event') {
            $saved = self::where('form_type', $formType)
                ->whereNull('entity_id')
                ->orderBy('order')
                ->get();

            if ($saved->isNotEmpty()) {
                return $saved->toArray();
            }
        }

        $defaults = self::getDefaults()[$formType] ?? [];

        return array_map(function ($q, $index) {
            return [
                'id' => null,
                'question_key' => $q['key'],
                'question_label' => $q['label'],
                'placeholder' => $q['placeholder'],
                'is_required' => $q['required'],
                'order' => $index,
            ];
        }, $defaults, array_keys($defaults));
    }

    public static function resetToDefault(string $formType, ?int $entityId = null): void
    {
        self::where('form_type', $formType)
            ->where('entity_id', $entityId)
            ->delete();
    }

    public static function isBuiltInKey(string $formType, string $key): bool
    {
        $keys = self::getBuiltInKeys()[$formType] ?? [];

        return in_array($key, $keys);
    }

    public static function extractAnswers(string $formType, array $input): array
    {
        $builtInKeys = self::getBuiltInKeys()[$formType] ?? [];
        $regular = [];
        $custom = [];

        $excludeKeys = ['_token', '_method'];

        foreach ($input as $key => $value) {
            if (in_array($key, $excludeKeys)) {
                continue;
            }
            if (in_array($key, $builtInKeys)) {
                $regular[$key] = $value;
            } else {
                $custom[$key] = $value;
            }
        }

        return ['regular' => $regular, 'custom' => $custom];
    }
}
