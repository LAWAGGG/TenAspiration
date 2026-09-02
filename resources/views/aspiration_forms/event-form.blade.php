<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kirim Aspirasi</title>
    <link rel="icon" href="{{ asset('images/logo-mpk.jpg') }}" type="image/jpeg">
    @vite('resources/css/app.css')
    <style>
        [x-cloak] {
            display: none !important;
        }

        .loading-spinner {
            border: 2px solid #f3f3f3;
            border-top: 2px solid #3498db;
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

        .custom-select {
            position: relative;
            width: 100%;
        }

        .select-trigger {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            background: white;
            text-align: left;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14px;
            transition: all 0.2s;
            min-height: 48px;
        }

        .select-trigger:hover {
            border-color: #9ca3af;
        }

        .select-trigger:focus,
        .select-trigger.open {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px #bfdbfe;
        }

        .select-arrow {
            transition: transform 0.2s;
            flex-shrink: 0;
            margin-left: 8px;
        }

        .select-arrow.open {
            transform: rotate(180deg);
        }

        .select-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            margin-top: 4px;
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
            z-index: 50;
            max-height: 250px;
            overflow-y: auto;
        }

        .select-option {
            padding: 12px 16px;
            cursor: pointer;
            border-bottom: 1px solid #f3f4f6;
            font-size: 14px;
            transition: background-color 0.2s;
        }

        .select-option:hover {
            background-color: #eff6ff;
        }

        .select-option:last-child {
            border-bottom: none;
        }

        .select-option.selected {
            background-color: #eff6ff;
            color: #2563eb;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .select-trigger {
                padding: 16px;
                font-size: 16px;
                min-height: 52px;
            }

            .select-option {
                padding: 16px;
                font-size: 16px;
            }
        }

        .custom-textarea {
            min-height: 80px;
            resize: vertical;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #1e40af, #1e3a8a);
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: linear-gradient(135deg, #1e3a8a, #172554);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3);
        }
    </style>
</head>

<body class="flex items-center justify-center min-h-screen bg-gradient-to-br from-blue-50 via-white to-blue-50 p-4">

    {{-- Kirim data PHP ke JS lewat tag <script> biasa, BUKAN inline x-data --}}
    <script>
        window.__ASPIRATION_DATA__ = {
            events: @json($events->map(fn($e) => ['id' => (string) $e->id, 'name' => $e->name])->values()),
            eventQuestions: @json($eventQuestions),
            hasSuccess: {{ session('success') ? 'true' : 'false' }},
            oldEventId: '{{ old('event_id') }}',
            oldInput: @json(!session('success') ? old() : (object)[]),
        };
    </script>

    <div class="card border bg-white shadow-2xl rounded-3xl p-6 sm:p-8 w-full max-w-md border-blue-500 relative overflow-hidden"
        x-data="aspirationForm" @click.outside="selectOpen = false">

        <div class="absolute -top-20 -right-20 w-40 h-40 rounded-full bg-blue-100 opacity-30"></div>
        <div class="absolute -bottom-16 -left-16 w-32 h-32 rounded-full bg-blue-100 opacity-30"></div>

        <div class="relative z-10">
            <div class="flex justify-center mb-4">
                <img class="w-20 h-20 sm:w-24 sm:h-24" src="{{ asset('images/logo-mpk.jpg') }}" alt="Logo MPK">
            </div>

            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 text-center mb-3">
                TenAspiration <span class="text-blue-600">Event</span>
            </h1>
            <p class="text-gray-600 text-center mb-6 text-sm px-2 sm:px-4">
                Sampaikan aspirasimu secara <span class="font-semibold text-blue-500">anonim</span> kepada event yang
                sedang diselenggarakan.
            </p>

            @if ($errors->any())
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('aspiration_events.store') }}" method="POST" class="space-y-4"
                x-on:submit="isLoading = true">
                @csrf

                <input type="hidden" name="event_id" x-model="selectedEventId">

                <!-- Pilih Event -->
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-2 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-blue-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Event
                    </label>

                    <div class="custom-select">
                        <button type="button" @click="if(events.length > 0) selectOpen = !selectOpen"
                            :class="selectOpen ? 'select-trigger open' : 'select-trigger'"
                            :disabled="events.length === 0">
                            <template x-if="events.length === 0">
                                <span class="text-gray-400">Tidak ada event yang tersedia</span>
                            </template>
                            <template x-if="events.length > 0">
                                <span x-text="selectedEventName"
                                    :class="selectedEventId ? 'text-gray-800' : 'text-gray-400'"></span>
                            </template>
                            <svg class="select-arrow w-4 h-4 text-gray-500" :class="selectOpen ? 'open' : ''"
                                x-show="events.length > 0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="selectOpen && events.length > 0" x-cloak class="select-dropdown">
                            <template x-for="event in events" :key="event.id">
                                <div @click="selectedEventId = event.id; selectedEventName = event.name; selectOpen = false;"
                                    :class="selectedEventId === event.id ? 'select-option selected' : 'select-option'">
                                    <span x-text="event.name"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
                <div x-show="!selectedEventId"
                    class="text-center py-8 px-4 border-2 border-dashed border-blue-200 rounded-xl bg-blue-50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-blue-300 mb-3" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                    <p class="text-blue-400 font-medium text-sm">Pilih event terlebih dahulu</p>
                    <p class="text-blue-300 text-xs mt-1">Pertanyaan akan muncul setelah kamu memilih event</p>
                </div>

                <!-- Pertanyaan Dinamis -->
                <template x-for="(q, index) in currentQuestions" :key="q.question_key">
                    <div>
                        <label class="text-sm font-medium text-gray-700 mb-2 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-blue-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                            </svg>
                            <span x-text="q.question_label"></span>
                            <span x-show="!q.is_required" class="text-gray-400 text-xs ml-1">(opsional)</span>
                        </label>
                        
                        <!-- Essay -->
                        <template x-if="!q.question_type || q.question_type === 'essay'">
                            <div>
                                <textarea :name="q.question_key" :placeholder="q.placeholder" rows="4" :required="q.is_required"
                                    x-init="$el.value = oldInput[q.question_key] ?? ''"
                                    class="custom-textarea w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-300 transition"></textarea>
                            </div>
                        </template>
                        
                        <!-- Pilihan Ganda -->
                        <template x-if="q.question_type === 'pilihan_ganda'">
                            <div class="space-y-2" x-init="pgOtherChecked[index] = isOtherChecked(q, index)">
                                <template x-for="(opt, optIdx) in getOptions(q)" :key="optIdx">
                                    <div class="flex items-center gap-2 p-2 rounded-lg border border-gray-100 hover:bg-blue-50 transition-colors">
                                        <input type="radio" :id="'pg_' + index + '_' + optIdx" :name="q.question_key" :value="opt"
                                            x-init="$el.checked = oldInput[q.question_key] === opt"
                                            @change="pgOtherChecked[index]=false"
                                            :required="q.is_required"
                                            class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                                        <label :for="'pg_' + index + '_' + optIdx" class="text-sm text-gray-700 cursor-pointer flex-1" x-text="opt"></label>
                                    </div>
                                </template>
                                <template x-if="allowOther(q)">
                                    <div class="p-2 rounded-lg border transition-colors" :class="pgOtherChecked[index] ? 'border-blue-300 bg-blue-50 shadow-sm' : 'border-gray-100 hover:bg-blue-50'" x-init="pgOtherChecked[index] = isOtherChecked(q, index)">
                                        <div class="flex items-center gap-2">
                                            <input type="radio" :id="'pg_other_' + index" :name="q.question_key" value="__other__"
                                                x-init="$el.checked = isOtherChecked(q, index)"
                                                @change="pgOtherChecked[index]=true; $nextTick(()=> $refs['pg_other_'+index]?.focus())"
                                                :required="q.is_required"
                                                class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                                            <label :for="'pg_other_' + index" class="text-sm font-medium text-gray-700 cursor-pointer">Lainnya</label>
                                            <span class="ml-auto text-xs text-gray-400">isi sendiri</span>
                                        </div>
                                        <div x-show="pgOtherChecked[index]" x-transition class="mt-2">
                                            <input type="text" :name="q.question_key + '_other'" :value="otherValue(q)"
                                                :ref="'pg_other_'+index"
                                                placeholder="Tulis jawaban lainnya..."
                                                class="w-full border border-blue-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-300 bg-white">
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                        
                        <!-- Checkbox -->
                        <template x-if="q.question_type === 'checkbox'">
                            <div class="space-y-2" x-init="chkOtherChecked[index] = isOtherChecked(q, index)">
                                <template x-for="(opt, optIdx) in getOptions(q)" :key="optIdx">
                                    <div class="flex items-center gap-2 p-2 rounded-lg border border-gray-100 hover:bg-blue-50 transition-colors">
                                        <input type="checkbox" :id="'chk_' + index + '_' + optIdx" :name="q.question_key + '[]'" :value="opt"
                                            x-init="checkEventCheckbox($el, oldInput[q.question_key], opt)"
                                            class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                                        <label :for="'chk_' + index + '_' + optIdx" class="text-sm text-gray-700 cursor-pointer flex-1" x-text="opt"></label>
                                    </div>
                                </template>
                                <template x-if="allowOther(q)">
                                    <div class="p-2 rounded-lg border transition-colors" :class="chkOtherChecked[index] ? 'border-blue-300 bg-blue-50 shadow-sm' : 'border-gray-100 hover:bg-blue-50'">
                                        <div class="flex items-center gap-2">
                                            <input type="checkbox" :id="'chk_other_' + index" :name="q.question_key + '[]'" value="__other__"
                                                x-init="$el.checked = isOtherChecked(q, index)"
                                                @change="chkOtherChecked[index] = $el.checked; if($el.checked) $nextTick(()=> $refs['chk_other_'+index]?.focus())"
                                                class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                                            <label :for="'chk_other_' + index" class="text-sm font-medium text-gray-700 cursor-pointer">Lainnya</label>
                                            <span class="ml-auto text-xs text-gray-400">isi sendiri</span>
                                        </div>
                                        <div x-show="chkOtherChecked[index]" x-transition class="mt-2">
                                            <input type="text" :name="q.question_key + '_other'" :value="otherValue(q)"
                                                :ref="'chk_other_'+index"
                                                placeholder="Tulis jawaban lainnya..."
                                                class="w-full border border-blue-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-300 bg-white">
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </template>

                <div x-show="selectedEventId && currentQuestions.length === 0"
                    class="text-center py-4 text-gray-500 text-sm">
                    Tidak ada pertanyaan untuk event ini.
                </div>

                <!-- Tombol Kirim -->
                <button type="submit"
                    class="w-full py-3 rounded-lg font-semibold shadow-md transition-all duration-200 btn-primary text-white flex items-center justify-center"
                    :disabled="isLoading">
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
                    <span x-text="isLoading ? 'Mengirim...' : 'Kirim Aspirasi'"></span>
                </button>

                <!-- Tombol Panduan -->
                <button type="button" @click="hint = true"
                    class="w-full py-3 rounded-lg font-semibold shadow-md transition-all duration-200 btn-secondary text-white">
                    Panduan
                </button>
            </form>

            <!-- Modal Panduan -->
            <div x-show="hint" x-cloak
                class="fixed inset-0 p-4 flex items-center justify-center bg-black bg-opacity-50 z-50 backdrop-blur-sm">
                <div class="bg-white rounded-xl p-6 shadow-2xl text-center max-w-sm w-full border-t-4 border-blue-500">
                    <h2 class="text-xl font-bold text-gray-800 mb-2">Panduan</h2>
                    <p class="text-gray-600 mb-4 text-sm">
                        1. Memilih event yang sedang diselenggarakan.<br>
                        2. Mengisi dari setiap kolom pertanyaan yang sudah diberikan.<br>
                        3. Gunakan bahasa yang baik dan sopan.<br>
                        4. Klik tombol "Kirim Aspirasi".
                    </p>
                    <button @click="hint = false"
                        class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                        Mengerti
                    </button>
                </div>
            </div>

            <!-- Modal Sukses -->
            <div x-show="successModal" x-cloak
                class="fixed inset-0 p-4 flex items-center justify-center bg-black bg-opacity-50 z-50 backdrop-blur-sm">
                <div
                    class="bg-white rounded-xl p-6 shadow-2xl text-center max-w-sm w-full border-t-4 border-green-500">
                    <div class="flex justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-green-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800 mb-2">Aspirasi Terkirim!</h2>
                    <p class="text-gray-600 mb-4 text-sm">
                        Terima kasih telah menyampaikan aspirasi!, suara Anda sangat berarti bagi kami.
                    </p>
                    <button @click="successModal = false"
                        class="px-6 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Alpine.js dimuat SETELAH komponen didefinisikan --}}
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <script>
        // Definisikan Alpine component SEBELUM Alpine init
        document.addEventListener('alpine:init', () => {
            Alpine.data('aspirationForm', () => {
                const d = window.__ASPIRATION_DATA__;

                return {
                    hint: false,
                    successModal: false,
                    isLoading: false,
                    selectOpen: false,
                    selectedEventId: '',
                    selectedEventName: '-- Pilih Event --',
                    events: d.events,
                    eventQuestions: {},

                    init() {
                        if (d.hasSuccess) {
                            this.successModal = true;
                        }
                        if (d.oldEventId) {
                            const evt = this.events.find(e => e.id === d.oldEventId);
                            if (evt) {
                                this.selectedEventId = evt.id;
                                this.selectedEventName = evt.name;
                            }
                        }
                        // Parse question_options from JSON string to object
                        for (const eventId in d.eventQuestions) {
                            this.eventQuestions[eventId] = d.eventQuestions[eventId].map(q => {
                                if (q.question_options && typeof q.question_options === 'string') {
                                    try { q = { ...q, question_options: JSON.parse(q.question_options) }; } catch(e) {}
                                }
                                return q;
                            });
                        }
                    },

                    get oldInput() {
                        return d.oldInput || {};
                    },

                    get currentQuestions() {
                        if (!this.selectedEventId) return [];
                        return this.eventQuestions[this.selectedEventId] || [];
                    },

                    checkEventCheckbox(el, savedValues, value) {
                        const vals = Array.isArray(savedValues) ? savedValues : (savedValues ? [savedValues] : []);
                        el.checked = vals.includes(value);
                    },

                    getOptions(q) {
                        const opts = (q.question_options?.options || []).filter(o => o && o.trim() !== '');
                        return opts;
                    },

                    allowOther(q) { return !!q.question_options?.allow_other; },
                    pgOtherChecked: {},
                    chkOtherChecked: {},
                    isOtherChecked(q, index) {
                        const val = this.oldInput[q.question_key];
                        const otherVal = this.oldInput[q.question_key + '_other'];
                        if (val === '__other__') return true;
                        if (Array.isArray(val) && val.includes('__other__')) return true;
                        if (otherVal) return true;
                        if (typeof val === 'string' && val && !this.getOptions(q).includes(val) && this.allowOther(q)) return true;
                        if (Array.isArray(val) && val.some(v => !this.getOptions(q).includes(v) && v)) return true;
                        return false;
                    },
                    otherValue(q) {
                        if (this.oldInput[q.question_key + '_other']) return this.oldInput[q.question_key + '_other'];
                        const val = this.oldInput[q.question_key];
                        if (typeof val === 'string' && val && !this.getOptions(q).includes(val)) return val;
                        if (Array.isArray(val)) {
                            const custom = val.find(v => !this.getOptions(q).includes(v));
                            if (custom && custom !== '__other__') return custom;
                        }
                        return '';
                    }
                };
            });
        });
    </script>

</body>

</html>
