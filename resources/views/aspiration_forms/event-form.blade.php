<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kirim Aspirasi</title>
    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
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

        /* Custom Select Styles */
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

        .select-trigger:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px #bfdbfe;
        }

        .select-trigger.open {
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

        /* Untuk mobile optimization */
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

        /* Textarea styling improvement */
        .custom-textarea {
            min-height: 80px;
            resize: vertical;
        }

        /* Button improvements */
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

    <div class="card border bg-white shadow-2xl rounded-3xl p-6 sm:p-8 w-full max-w-md border-blue-500 relative overflow-hidden"
        x-data="{
            hint: false,
            successModal: @if (session('success')) true @else false @endif,
            isLoading: false,
            selectOpen: false,
            selectedEventId: '',
            selectedEventName: '-- Pilih Event --',
            events: [
                @if ($events->count() > 0) @foreach ($events as $event)
            {
                id: '{{ $event->id }}',
                name: '{{ $event->name }}'
            },
            @endforeach @endif
            ]
        }" x-init="@if (session('success')) setTimeout(() => { successModal = true }, 100); @endif
        @if (old('event_id')) const event = events.find(e => e.id == '{{ old('event_id') }}');
                if (event) {
                    selectedEventId = event.id;
                    selectedEventName = event.name;
                } @endif" @click.outside="selectOpen = false">

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

            <!-- Error -->
            @if ($errors->any())
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form aspirasi -->
            <form action="{{ route('aspiration_events.store') }}" method="POST" class="space-y-4"
                x-on:submit="isLoading = true">
                @csrf

                <!-- Hidden input untuk event_id -->
                <input type="hidden" name="event_id" x-model="selectedEventId" required>

                <div>
                    <label class="text-sm font-medium text-gray-700 mb-2 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-blue-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Event
                    </label>

                    <!-- Custom Select -->
                    <div class="custom-select">
                        <button type="button" @click="if(events.length > 0) selectOpen = !selectOpen"
                            :class="selectOpen ? 'select-trigger open' : 'select-trigger'" class="select-trigger"
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
                                    :class="selectedEventId === event.id ? 'select-option selected' : 'select-option'"
                                    class="select-option">
                                    <span x-text="event.name"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- kesan dan pesan -->
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-2 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-blue-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                        Kesan Pesan
                    </label>
                    <textarea name="kesan_pesan" rows="4" placeholder="Berikan kesan dan pesan setelah dilaksanakannya event ini"
                        class="custom-textarea w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-300 transition"
                        required>{{ old('kesan_pesan') }}</textarea>
                </div>

                <!-- kritik saran masukan -->
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-2 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-blue-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                        Berikan Kritik, Saran, & Masukan
                    </label>
                    <textarea name="message" rows="4" placeholder="Berikan Kritik, Saran, Dan Masukan dalam event ini"
                        class="custom-textarea w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-300 transition"
                        required>{{ old('message') }}</textarea>
                </div>

                <!-- Kejadian buruk -->
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-2 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-blue-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                        Kejadian buruk yang dialami selama event
                    </label>
                    <textarea name="bad_moment" rows="4" placeholder="jika tidak ada, berikan (-)"
                        class="custom-textarea w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-300 transition"
                        required>{{ old('bad_moment') }}</textarea>
                </div>

                <!-- Perubahan dari event sebelumnya -->
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-2 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-blue-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                        Perubahan dari event sebelumnya
                    </label>
                    <textarea name="perubahan_dari_event" rows="4"
                        placeholder="Apa yang paling kalian rasakan perubahan dari event sebelumnya?"
                        class="custom-textarea w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-300 transition"
                        required>{{ old('perubahan_dari_event') }}</textarea>
                </div>

                <!-- Tombol kirim -->
                <button type="submit"
                    class="w-full py-3 rounded-lg font-semibold shadow-md transition-all duration-200 btn-primary text-white flex items-center justify-center"
                    :disabled="isLoading">
                    <template x-if="isLoading">
                        <div class="loading-spinner"></div>
                    </template>
                    <template x-if="!isLoading">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
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
                        1. Memilih event yang sedang diselenggarakan. <br>
                        2. Mengisi dari setiap kolom pertanyaan yang sudah diberikan.<br>
                        3. Gunakan bahasa yang baik dan sopan.<br>
                        4. Klik tombol "Kirim Aspirasi". <br>
                    </p>
                    <button @click="hint = false"
                        class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition focus:outline-none focus:ring-2 focus:ring-blue-300 focus:ring-offset-2">
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
                        {{'Terima kasih telah menyampaikan aspirasi!, suara Anda sangat berarti bagi kami.' }}
                    </p>
                    <button @click="successModal = false"
                        class="px-6 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition focus:outline-none focus:ring-2 focus:ring-green-300 focus:ring-offset-2">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
