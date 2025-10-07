<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kirim Aspirasi</title>
    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

</head>

<body class="flex items-center justify-center min-h-screen bg-gradient-to-br from-red-50 via-white to-red-50 p-4">

    <div class="card border bg-white shadow-2xl rounded-3xl p-8 w-full max-w-md border-red-500 relative overflow-hidden"
        x-data="{ showModal: false, hint: false }">

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

            {{-- Alert sukses --}}
            @if (session('success'))
                <div class="bg-green-100 text-green-700 border border-green-300 p-3 rounded-lg mb-4">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Alert error --}}
            @if ($errors->any())
                <div class="bg-red-100 text-red-700 border border-red-300 p-3 rounded-lg mb-4">
                    ⚠️ {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('aspirations.store') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-2 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-red-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Tujuan
                    </label>
                    <select name="to" required
                        class="w-full border border-gray-300 rounded-lg p-3 bg-white focus:outline-none focus:ring-2 focus:ring-red-300 focus:border-red-300 transition appearance-none">
                        <option value="">-- Pilih Target --</option>
                        <optgroup label="Wakil">
                            <option value="wakil kesiswaan">wakil kesiswaan</option>
                            <option value="wakil sarpras">wakil sarpras</option>
                            <option value="wakil kurikulum">wakil kurikulum</option>
                            <option value="wakil humas">wakil humas</option>
                        </optgroup>
                        <optgroup label="Tata usaha">
                            <option value="tata usaha">tata usaha</option>
                        </optgroup>
                        <optgroup label="Organisasi">
                            <option value="OSIS">OSIS</option>
                            <option value="MPK">MPK</option>
                        </optgroup>
                        <optgroup label="Lainnya">
                            <option value="umum">umum</option>
                        </optgroup>
                    </select>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-700 mb-2 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-red-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                        Pesan Aspirasi (Kritik, Saran, & Masukan)
                    </label>
                    <textarea name="message" placeholder="Berikan Kritik, Saran, Dan Masukan Aspirasimu" rows="3"
                        class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-red-300 focus:border-red-300 transition"
                        required></textarea>
                </div>

                <button type="submit"
                    class="w-full py-3 rounded-lg font-semibold shadow-md bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white transition">
                    <div class="flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                        Kirim Aspirasi
                    </div>
                </button>

                <button type="button" @click="hint = true"
                    class="w-full py-3 rounded-lg font-semibold shadow-md bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white transition">
                    Panduan
                </button>
            </form>

            {{-- Modal Panduan --}}
            <div x-show="hint" x-cloak
                class="fixed inset-0 p-5 backdrop-blur-sm flex items-center justify-center bg-black bg-opacity-50 z-50">
                <div class="bg-white rounded-xl p-6 shadow-2xl text-center max-w-sm w-full border-t-4 border-blue-500">
                    <h2 class="text-xl font-bold text-gray-800 mb-2">Panduan</h2>
                    <p class="text-gray-600 mb-4 text-sm">
                        1. Pilih tujuan aspirasi (Divisi-Divisi Dalam Sekolah).<br>
                        2. Tulis pesan aspirasi kamu di kolom "Pesan aspirasi".<br>
                        3. Gunakan bahasa yang baik dan benar.<br>
                        4. Klik tombol "Kirim Aspirasi". <br>
                        <br>
                        Jika ingin melihat penjelasan lebih detail mengenai Divisi-Divisi Dalam Sekolah, lihat di <a
                            class="text-blue-700 font-bold underline" href="">Sini!</a>
                    </p>
                    <button @click="hint = false"
                        class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                        Mengerti
                    </button>
                </div>
            </div>

            <p class="mt-6 text-xs text-gray-500 text-center italic">
                "Suara Anda penting bagi kami. Setiap aspirasi akan ditinjau oleh MPK."
            </p>
        </div>
    </div>

</body>

</html>
