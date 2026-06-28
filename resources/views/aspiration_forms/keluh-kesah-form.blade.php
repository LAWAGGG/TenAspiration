<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kirim Aspirasi | Keluhan</title>
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

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Keluh / Kesah</label>
                    <textarea name="keluh_kesah" rows="5" required
                        class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-red-300 focus:border-red-300 transition"
                        placeholder="Ceritakan keluh kesahmu di sini...">{{ old('keluh_kesah') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon</label>
                    <input type="text" name="phone_number" value="{{ old('phone_number') }}"
                        class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-red-300 focus:border-red-300 transition"
                        placeholder="08xxxxxxxxxx" />
                </div>

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
