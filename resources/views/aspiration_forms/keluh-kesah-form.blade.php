<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kirim Aspirasi | Keluhan</title>
    <link rel="icon" href="{{ asset('images/logo-mpk.jpg') }}" type="image/jpeg">
    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        [x-cloak] {
            display: none !important;
        }

        .loading-spinner {
            border: 2px solid #f3f3f3;
            border-top: 2px solid #ffffff;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
            display: inline-block;
            margin-right: 8px;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body class="flex items-center justify-center min-h-screen bg-gradient-to-br from-red-50 via-white to-red-50 p-4">

    <div class="card border bg-white shadow-xl rounded-2xl p-6 w-full max-w-md border-red-400 relative overflow-hidden"
        x-data="{ isLoading: false, hint: false, showModal: @if (session('success')) true @else false @endif }">

        <!-- Lingkaran latar belakang merah -->
        <div class="absolute -top-20 -right-20 w-40 h-40 rounded-full bg-red-100 opacity-30"></div>
        <div class="absolute -bottom-16 -left-16 w-32 h-32 rounded-full bg-red-100 opacity-30"></div>

        <div class="relative z-10">
            <div class="flex justify-center mb-4">
                <img class="w-24 h-24" src="{{ asset('images/logo-mpk.jpg') }}" alt="Logo MPK">
            </div>

            <!-- Judul dengan warna merah -->
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 text-center mb-3">
                TenAspiration <span class="text-red-600">Keluhan</span>
            </h1>
            <p class="text-gray-600 text-center mb-6 text-sm px-4">Sampaikan keluh-kesah atau masalahmu disini.</p>

            @if ($errors->any())
                <div class="bg-red-100 text-red-700 border border-red-300 p-3 rounded-lg mb-4">⚠️ {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('aspiration_keluhkesah.store') }}" class="space-y-6"
                x-on:submit="isLoading = true">
                @csrf

                @php
                    $keluhKesahQuestions = $questions ?? FormQuestion::getForForm('keluh_kesah');
                @endphp

                @foreach ($keluhKesahQuestions as $question)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ $question['question_label'] }}</label>
                    
                    @php
                        $qType = $question['question_type'] ?? 'essay';
                        $rawOptions = $question['question_options'];
                        $opts = [];
                        $allowOther = false;
                        if (is_string($rawOptions)) {
                            $decoded = json_decode($rawOptions, true);
                            $opts = $decoded['options'] ?? [];
                            $allowOther = !empty($decoded['allow_other']);
                        } elseif (is_array($rawOptions)) {
                            $opts = $rawOptions['options'] ?? [];
                            $allowOther = !empty($rawOptions['allow_other']);
                        }
                        $opts = array_values(array_filter($opts, fn($v) => is_string($v) && trim($v) !== ''));
                    @endphp
                    
                    @if ($qType === 'essay')
                    @if ($question['question_key'] === 'keluh_kesah')
                    <textarea name="{{ $question['question_key'] }}" rows="5" {{ $question['is_required'] ? 'required' : '' }}
                        class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-red-300 focus:border-red-300 transition"
                        placeholder="{{ $question['placeholder'] ?? '' }}">{{ old($question['question_key']) }}</textarea>
                    @else
                    <input type="text" name="{{ $question['question_key'] }}" value="{{ old($question['question_key']) }}" {{ $question['is_required'] ? 'required' : '' }}
                        class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-red-300 focus:border-red-300 transition"
                        placeholder="{{ $question['placeholder'] ?? '' }}" />
                    @endif
                    
                    @elseif ($qType === 'pilihan_ganda')
                    @php
                        $oldValPG = old($question['question_key']);
                        $oldOtherPG = old($question['question_key'].'_other');
                        $initialOtherPG = ($oldValPG === '__other__' || !empty($oldOtherPG) || ($oldValPG && !in_array($oldValPG, $opts) && $allowOther));
                    @endphp
                    <div class="space-y-2" x-data="{ otherOn: {{ $initialOtherPG ? 'true' : 'false' }} }">
                        @foreach ($opts as $optIdx => $opt)
                        <div class="flex items-center gap-2 p-2 rounded-lg border border-gray-100 hover:bg-red-50 transition-colors">
                            <input type="radio" name="{{ $question['question_key'] }}" value="{{ $opt }}"
                                {{ (old($question['question_key']) === $opt) ? 'checked' : '' }}
                                @change="otherOn = false"
                                {{ $question['is_required'] ? 'required' : '' }}
                                class="h-4 w-4 border-gray-300 text-red-600 focus:ring-red-500 cursor-pointer">
                            <span class="text-sm text-gray-700">{{ $opt }}</span>
                        </div>
                        @endforeach
                        @if($allowOther)
                        <div class="p-2 rounded-lg border transition-colors" :class="otherOn ? 'border-red-300 bg-red-50 shadow-sm' : 'border-gray-100 hover:bg-red-50'">
                            <div class="flex items-center gap-2">
                                <input type="radio" name="{{ $question['question_key'] }}" value="__other__"
                                    {{ $initialOtherPG ? 'checked' : '' }}
                                    @change="otherOn = true"
                                    {{ $question['is_required'] ? 'required' : '' }}
                                    class="h-4 w-4 border-gray-300 text-red-600 focus:ring-red-500 cursor-pointer">
                                <span class="text-sm font-medium text-gray-700">Lainnya</span>
                                <span class="ml-auto text-xs text-gray-400">isi sendiri</span>
                            </div>
                            <div x-show="otherOn" x-transition class="mt-2">
                                <input type="text" name="{{ $question['question_key'] }}_other" value="{{ $oldOtherPG ?? ($initialOtherPG && $oldValPG && !in_array($oldValPG, $opts) ? $oldValPG : '') }}"
                                    placeholder="Tulis jawaban lainnya..."
                                    class="w-full border border-red-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300 focus:border-red-300 bg-white">
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    @elseif ($qType === 'checkbox')
                    @php
                        $oldVals = is_array(old($question['question_key'])) ? old($question['question_key']) : [];
                        $oldOtherChk = old($question['question_key'].'_other');
                        $initialOtherChk = in_array('__other__', $oldVals) || !empty($oldOtherChk);
                        if (!$initialOtherChk && $allowOther) {
                            foreach ($oldVals as $ov) { if (!in_array($ov, $opts) && $ov !== '__other__') { $initialOtherChk = true; break; } }
                        }
                    @endphp
                    <div class="space-y-2" x-data="{ otherOn: {{ $initialOtherChk ? 'true' : 'false' }} }">
                        @foreach ($opts as $optIdx => $opt)
                        <div class="flex items-center gap-2 p-2 rounded-lg border border-gray-100 hover:bg-red-50 transition-colors">
                            <input type="checkbox" name="{{ $question['question_key'] }}[]" value="{{ $opt }}"
                                {{ in_array($opt, $oldVals) ? 'checked' : '' }}
                                class="h-4 w-4 rounded border-gray-300 text-red-600 focus:ring-red-500 cursor-pointer">
                            <span class="text-sm text-gray-700">{{ $opt }}</span>
                        </div>
                        @endforeach
                        @if($allowOther)
                        <div class="p-2 rounded-lg border transition-colors" :class="otherOn ? 'border-red-300 bg-red-50 shadow-sm' : 'border-gray-100 hover:bg-red-50'">
                            <div class="flex items-center gap-2">
                                <input type="checkbox" name="{{ $question['question_key'] }}[]" value="__other__"
                                    {{ $initialOtherChk ? 'checked' : '' }}
                                    @change="otherOn = $el.checked"
                                    class="h-4 w-4 rounded border-gray-300 text-red-600 focus:ring-red-500 cursor-pointer">
                                <span class="text-sm font-medium text-gray-700">Lainnya</span>
                                <span class="ml-auto text-xs text-gray-400">isi sendiri</span>
                            </div>
                            <div x-show="otherOn" x-transition class="mt-2">
                                @php
                                    $otherValChk = $oldOtherChk;
                                    if (!$otherValChk) {
                                        foreach ($oldVals as $ov) { if (!in_array($ov, $opts) && $ov !== '__other__') { $otherValChk = $ov; break; } }
                                    }
                                @endphp
                                <input type="text" name="{{ $question['question_key'] }}_other" value="{{ $otherValChk }}"
                                    placeholder="Tulis jawaban lainnya..."
                                    class="w-full border border-red-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300 focus:border-red-300 bg-white">
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
                @endforeach

                <!-- Tombol dengan gradien merah -->
                <button type="submit"
                    class="w-full py-3 rounded-lg font-semibold shadow-md text-white transition flex items-center justify-center"
                    :disabled="isLoading" style="background: linear-gradient(90deg, #ef4444, #dc2626); border: none;">
                    <template x-if="isLoading">
                        <div class="loading-spinner"></div>
                    </template>
                    <template x-if="!isLoading">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                    </template>
                    <span x-text="isLoading ? 'Mengirim...' : 'Kirim Keluh Kesah'"></span>
                </button>

                <!-- Tombol Panduan -->
                <button type="button" @click="hint = true"
                    class="w-full py-3 rounded-lg font-semibold shadow-md transition-all duration-200 bg-blue-700 text-white hover:bg-blue-800">
                    Panduan
                </button>
            </form>

            <!-- Modal Panduan -->
            <div x-show="hint" x-cloak
                class="fixed inset-0 p-4 flex items-center justify-center bg-black bg-opacity-50 z-50 backdrop-blur-sm">
                <div class="bg-white rounded-xl p-6 shadow-2xl text-center max-w-sm w-full border-t-4 border-red-500">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Panduan</h2>
                    <div class="text-left space-y-3 mb-6">
                        <div class="flex items-start">
                            <span
                                class="bg-red-100 text-red-700 rounded-full w-6 h-6 flex items-center justify-center text-sm font-medium mr-3 flex-shrink-0 mt-0.5">1</span>
                            <p class="text-gray-600 text-sm">Mengisi kolom "Keluh/Kesah" dengan keluhan yang ingin
                                disampaikan kepada pihak sekolah mengenai: perundungan, curhat, atau masalah lainnya.
                            </p>
                        </div>
                        <div class="flex items-start">
                            <span
                                class="bg-red-100 text-red-700 rounded-full w-6 h-6 flex items-center justify-center text-sm font-medium mr-3 flex-shrink-0 mt-0.5">2</span>
                            <p class="text-gray-600 text-sm">Mengisi kolom "Nomor Telepon" agar keluh kesah dapat
                                ditindaklanjuti.</p>
                        </div>
                        <div class="flex items-start">
                            <span
                                class="bg-red-100 text-red-700 rounded-full w-6 h-6 flex items-center justify-center text-sm font-medium mr-3 flex-shrink-0 mt-0.5">3</span>
                            <p class="text-gray-600 text-sm">Klik tombol "Kirim Keluh Kesah" untuk mengirimkan.</p>
                        </div>
                    </div>
                    <button @click="hint = false"
                        class="px-6 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition focus:outline-none focus:ring-2 focus:ring-red-300 focus:ring-offset-2">
                        Mengerti
                    </button>
                </div>
            </div>

            {{-- Modal sukses --}}
            <div x-show="showModal" x-cloak
                class="fixed inset-0 p-5 backdrop-blur-sm flex items-center justify-center bg-black bg-opacity-50 z-50">
                <div class="bg-white rounded-xl p-6 shadow-2xl text-center max-w-sm w-full border-t-4 border-red-500">
                    <div class="flex justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-red-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800 mb-2">Pesan Terkirim!</h2>
                    <p class="text-gray-600 mb-4 text-sm">
                        {{ session('success') ?? 'Terima kasih, pesan Anda telah terkirim.' }}</p>
                    <button @click="showModal=false"
                        class="px-6 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">Tutup</button>
                </div>
            </div>

        </div>
    </div>

</body>

</html>
