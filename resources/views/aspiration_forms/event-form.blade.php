<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kirim Aspirasi</title>
    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

</head>

<body class="flex items-center justify-center min-h-screen bg-gradient-to-br from-blue-50 via-white to-blue-50 p-4">

    <div class="card border bg-white shadow-2xl rounded-3xl p-8 w-full max-w-md border-blue-500 relative overflow-hidden"
        x-data="{ hint: false }">

        <div class="absolute -top-20 -right-20 w-40 h-40 rounded-full bg-blue-100 opacity-30"></div>
        <div class="absolute -bottom-16 -left-16 w-32 h-32 rounded-full bg-blue-100 opacity-30"></div>

        <div class="relative z-10">
            <div class="flex justify-center mb-4">
                <img class="w-24 h-24" src="{{ asset('images/logo-mpk.jpg') }}" alt="Logo MPK">
            </div>

            <h1 class="text-3xl font-bold text-gray-800 text-center mb-3">
                TenAspiration <span class="text-blue-600">Event</span>
            </h1>
            <p class="text-gray-600 text-center mb-6 text-sm px-4">
                Sampaikan aspirasimu secara <span class="font-semibold text-blue-500">anonim</span> kepada event yang
                sedang diselenggarakan.
            </p>

            <!-- Pesan sukses -->
            @if (session('success'))
                <div
                    class="mt-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative text-center">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Error -->
            @if ($errors->any())
                <div class="mt-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative text-sm">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form aspirasi -->
            <form action="{{ route('aspiration_events.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="text-sm font-medium text-gray-700 mb-2 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-blue-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Event
                    </label>
                    <select name="event_id"
                        class="w-full border border-gray-300 rounded-lg p-3 bg-white focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-300 transition appearance-none"
                        required>
                        <option value="">-- Pilih Event --</option>
                        @foreach ($events as $event)
                            <option value="{{ $event->id }}" {{ old('event_id') == $event->id ? 'selected' : '' }}>
                                {{ $event->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Tujuan -->
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-2 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-blue-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Tujuan (ke)
                    </label>
                    <select name="to"
                        class="w-full border border-gray-300 rounded-lg p-3 bg-white focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-300 transition appearance-none"
                        required
                        x-on:change="document.getElementById('otherToDiv').style.display = $event.target.value === 'lainnya' ? 'block' : 'none'">
                        <option value="">-- Pilih Tujuan --</option>
                        <option value="Divisi Keamanan">Divisi Keamanan</option>
                        <option value="Divisi Kebersihan">Divisi Kebersihan</option>
                        <option value="Divisi Acara">Divisi Acara</option>
                        <option value="Divisi Dokumentasi">Divisi Dokumentasi</option>
                        <option value="Divisi Humas">Divisi Humas</option>
                        <option value="Divisi Perlengkapan">Divisi Perlengkapan</option>
                        <option value="lainnya">Lainnya</option>
                    </select>

                    <div id="otherToDiv" class="mt-2 hidden">
                        <input type="text" name="other_to" placeholder="Tuliskan tujuan (ke) lain..."
                            class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-300 transition">
                    </div>
                </div>

                <!-- Pesan -->
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-2 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-blue-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                        Pesan Aspirasi (Kritik, Saran, & Masukan)
                    </label>
                    <textarea name="message" rows="1" placeholder="Berikan Kritik, Saran, Dan Masukan Aspirasimu"
                        class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-300 transition"
                        required></textarea>
                </div>

                <!-- Event -->

                <!-- Tombol kirim -->
                <button type="submit"
                    class="w-full py-3 rounded-lg font-semibold shadow-md transition-all duration-200 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                    Kirim Aspirasi
                </button>

                <!-- Tombol Panduan -->
                <button type="button" @click="hint = true"
                    class="w-full py-3 rounded-lg font-semibold shadow-md transition-all duration-200 bg-gradient-to-r from-blue-700 to-blue-800 hover:from-blue-800 hover:to-blue-900 text-white">
                    Panduan
                </button>
            </form>

            <!-- Modal Panduan -->
            <div x-show="hint" x-cloak
                class="fixed inset-0 p-5 flex items-center justify-center bg-black bg-opacity-75 z-50 backdrop-blur-sm">
                <div class="bg-white rounded-xl p-6 shadow-2xl text-center max-w-sm w-full border-t-4 border-blue-500">
                    <h2 class="text-xl font-bold text-gray-800 mb-2">Panduan</h2>
                    <p class="text-gray-600 mb-4 text-sm">
                        1. Pilih tujuan aspirasi (divisi jobdesk).<br>
                        2. Tulis pesan aspirasi kamu di kolom "Pesan Aspirasi".<br>
                        3. Gunakan bahasa yang baik dan sopan.<br>
                        4. Klik tombol "Kirim Aspirasi".
                    </p>
                    <button @click="hint = false"
                        class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition focus:outline-none focus:ring-2 focus:ring-blue-300 focus:ring-offset-2">
                        Mengerti
                    </button>
                </div>
            </div>

            <p class="mt-6 text-xs text-gray-500 text-center italic">
                "Suara Anda penting bagi kami. Setiap aspirasi akan ditinjau oleh MPK."
            </p>
        </div>
    </div>

    <script>
        // Show success modal if there's a success message
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('success'))
                setTimeout(() => {
                    Alpine.data().showModal = true;
                }, 100);
            @endif
        });
    </script>

</body>

</html>
