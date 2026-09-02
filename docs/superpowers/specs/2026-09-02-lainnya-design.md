# Lainnya (Other) for PG/Checkbox — Design Spec

**Date:** 2026-09-02
**Status:** Approved

## Overview
Tambah opsi "Lainnya" pada soal tipe `pilihan_ganda` dan `checkbox`. User bisa ketik bebas jika opsi tidak sesuai. Toggle per-soal (admin). Tanpa migrasi DB.

## Data Model
- Reuse `form_questions.question_options` JSON.
- Shape: `{ options: string[2..10], allow_other: boolean, correct_answer?: string, correct_answers?: string[] }`
- `allow_other` default false. Essay abaikan.

## Backend
### FormQuestionController::update
- Validate `question_options` json + structural: 2-10 options, max 100, no dup, plus `allow_other` boolean if type PG/checkbox.
- Save `allow_other` inside JSON. Keep `order` fix.

### Store (Aspiration/AspirationEvent/AspirationKeluhKesah)
- Extract `allowed` + `allow_other` per question.
- Input tambahan: `messages[key]_other` (PG: string, checkbox: string). Frontend kirim `__other__` sebagai sentinel.
- Transform before validate/store:
  - PG: if `messages[key] === '__other__'` then `messages[key] = trim(messages[key]_other)`; require `_other` non-empty when sentinel selected.
  - Checkbox: if `messages[key]` array contains `'__other__'` then replace that entry with `trim(messages[key]_other)`, require `_other` when contains.
- Validation:
  - if `allow_other` true: `messages[key]` => `string|max:5000` (skip Rule::in), `messages[key]_other` => `nullable|string|max:5000`
  - else: `Rule::in(allowed)` as before.

## Frontend — User Form
- 3 views: `voxes-form.blade.php`, `event-form.blade.php`, `keluh-kesah-form.blade.php`
- After options list, render Lainnya row if `allow_other`:
  - PG: `<input type=radio value="__other__"> Lainnya` + `<input type=text name="messages[key]_other" x-show="selected === '__other__'">`
  - Checkbox: `<input type=checkbox value="__other__"> Lainnya` + text input show when checked.
- Alpine state: `otherValue`, `isOtherSelected`, auto-focus, required when selected.
- Style: same card, purple accent, below options, `mt-2`, disabled input until selected.

## Admin — Kelola Pertanyaan
- 3 index: `aspirations/index`, `by_event`, `keluh_kesah/index`
- Di modal per pertanyaan, tampil toggle `☐ Izinkan Lainnya (tampilkan opsi Lainnya + input teks)` hanya ketika `question_type` PG/checkbox. Bind `q.question_options.allow_other`.
- Hidden `question_options` JSON.stringify tetapkan.

## Security
- `_other` max 5000, validated, escaped via `{{ }}` and `highlightText`.
- Throttle 20/min already on store routes.
- No new tables, minimal diff.

## Testing
- PG allow_other: submit predefined pass, sentinel+text pass, sentinel empty fail, arbitrary without sentinel fail when allow_other false.
- Checkbox: multiple + Lainnya combine correctly, implode stored.
