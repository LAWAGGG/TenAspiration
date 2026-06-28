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

    <div class="card border bg-white shadow-xl rounded-2xl p-6 w-full max-w-md md:max-w-lg border-red-400 relative overflow-hidden"
        x-data="{
            oldMessages: {{ json_encode(old('messages', [])) }},
            showModal: @if (session('success')) true @else false @endif,
            hint: false,
            isLoading: false,
            options: [{
                    group: 'Wakil',
                    items: [
                        { value: 'wakil kesiswaan', label: 'Wakil Kesiswaan' },
                        { value: 'wakil sarpras', label: 'Wakil Sarana Prasarana' },
                        { value: 'wakil kurikulum', label: 'Wakil Kurikulum' },
                        { value: 'wakil humas', label: 'Wakil Humas' }
                    ]
                },
                {
                    group: 'Tata Usaha',
                    items: [
                        { value: 'tata usaha', label: 'Tata Usaha' }
                    ]
                },
                {
                    group: 'Organisasi',
                    items: [
                        { value: 'OSIS', label: 'OSIS' },
                        { value: 'MPK', label: 'MPK' },
                        { value: 'Ekskul', label: 'Ekskul' }
                    ]
                },
                {
                    group: 'Umum',
                    items: [
                        { value: 'umum', label: 'Umum' }
                    ]
                }
            ]
        }">


        <div class="absolute -top-20 -right-20 w-40 h-40 rounded-full bg-red-100 opacity-30"></div>
        <div class="absolute -bottom-16 -left-16 w-32 h-32 rounded-full bg-red-100 opacity-30"></div>

        <div class="relative z-10">
            <div class="flex justify-center mb-4">
                <img class="w-24 h-24" src="{{ asset('images/logo-mpk.jpg') }}" alt="Logo MPK">
            </div>

            <h1 class="text-3xl font-bold text-gray-800 text-center mb-3">
                <span class="text-red-600">Ten</span>Aspiration
            </h1>

            <p class="text-gray-600 text-center mb-6 text-sm px-4">
                Sampaikan aspirasimu secara <span class="font-semibold text-red-500">anonim</span> melalui MPK.
            </p>

            <div class="flex flex-col gap-8">
                <button type="button" @click="hint = true"
                    class="w-full py-3 rounded-lg font-semibold shadow-md bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white transition">
                    Panduan
                </button>


                {{-- Alert error --}}
                @if ($errors->any())
                    <div class="bg-red-100 text-red-700 border border-red-300 p-3 rounded-lg mb-3">
                        ⚠️ {{ $errors->first() }}
                    </div>
                @endif
            </div>


            <form method="POST" action="{{ route('aspirations.store') }}" class="space-y-6"
                x-on:submit="isLoading = true">
                @csrf

                <!-- Pilih Kelas -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kelas</label>
                    <select name="kelas" required
                        class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-red-300 focus:border-red-300 transition">
                        <option value="">-- Pilih Kelas --</option>
                        <option value="X" {{ old('kelas') == 'X' ? 'selected' : '' }}>X</option>
                        <option value="XI" {{ old('kelas') == 'XI' ? 'selected' : '' }}>XI</option>
                        <option value="XII" {{ old('kelas') == 'XII' ? 'selected' : '' }}>XII</option>
                    </select>
                </div>

                {{-- Looping semua tujuan + textarea --}}
                <template x-for="group in options" :key="group.group">
                    <div>
                        <h2 class="font-bold text-red-600 text-lg mb-2" x-text="group.group"></h2>

                        <template x-for="item in group.items" :key="item.value">
                            <div class="mb-4">
                                <label class="text-sm font-medium text-gray-700 mb-2 block" x-text="item.label"></label>

                                <textarea :name="'messages[' + item.value + ']'" placeholder="Tulis kritik, saran, dan masukan kepada bidang ini.."
                                    rows="3" x-init="$el.value = oldMessages[item.value] ?? ''"
                                    class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-red-300 focus:border-red-300 transition"
                                    required></textarea>
                            </div>
                        </template>
                    </div>
                </template>

                <button type="submit"
                    class="w-full py-3 rounded-lg font-semibold shadow-md bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white transition flex items-center justify-center"
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

                    <span x-text="isLoading ? 'Mengirim...' : 'Kirim Semua Aspirasi'"></span>
                </button>


            </form>

            {{-- Modal Sukses --}}
            <div x-show="showModal" x-cloak
                class="fixed inset-0 p-5 backdrop-blur-sm flex items-center justify-center bg-black bg-opacity-50 z-50">
                <div class="bg-white rounded-xl p-6 shadow-2xl text-center max-w-sm w-full border-t-4 border-green-500">
                    <div class="flex justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-green-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800 mb-2">Aspirasi Terkirim!</h2>
                    <p class="text-gray-600 mb-4 text-sm">
                        {{ session('success') ?? 'Terima kasih! Semua aspirasi berhasil dikirim.' }}
                    </p>
                    <button @click="showModal = false"
                        class="px-6 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">
                        Tutup
                    </button>
                </div>
            </div>

            {{-- Modal Panduan --}}
            <div x-show="hint" x-cloak
                class="fixed inset-0 p-5 backdrop-blur-sm flex items-center justify-center bg-black bg-opacity-50 z-50">
                <div class="bg-white rounded-xl p-6 shadow-2xl text-center max-w-sm w-full border-t-4 border-blue-500">
                    <h2 class="text-xl font-bold text-gray-800 mb-2">Panduan</h2>
                    <p class="text-gray-600 mb-4 text-sm">
                        1. Isi seluruh kolom kritik & saran yang tersedia.<br>
                        2. Gunakan bahasa yang sopan.<br>
                        3. Klik "Kirim Semua Aspirasi".<br><br>
                        Ingin tahu detail tiap perangkat sekolah? Lihat di
                        <a class="text-blue-700 font-bold underline" href="/detail">Sini!</a>
                    </p>
                    <button @click="hint = false"
                        class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                        Mengerti
                    </button>
                </div>
            </div>

        </div>
    </div>

</body>

</html>
