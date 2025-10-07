<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Aspirasi Event</title>
    @vite('resources/css/app.css')
    <script src="//unpkg.com/alpinejs" defer></script>
</head>

<body class="min-h-screen bg-gradient-to-br from-red-50 via-white to-red-100 p-4 md:p-8" x-data="aspirationEventApp()">

    <div class="max-w-6xl mx-auto">
        {{-- Header --}}
        <div class="mb-8 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="text-center md:text-left">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">
                    Aspirasi <span class="text-red-600">Event</span>
                </h1>
                <p class="text-gray-600">Daftar aspirasi yang telah dikumpulkan  <span class="text-gray-500"
          x-text="'(' + filteredAspirations.length + ' Aspirasi' + ')'"></span></p>
            </div>

            <div class="flex items-center gap-3">
                <!-- Filter Dropdown -->
                <select x-model="filterTarget"
                    class="px-3 py-2 rounded-lg bg-red-100 text-sm border border-red-200 focus:outline-none focus:ring-2 focus:ring-red-400 transition">
                    <option value="">Semua Divisi</option>
                    <option value="Divisi Keamanan">Divisi Keamanan</option>
                    <option value="Divisi Kebersihan">Divisi Kebersihan</option>
                    <option value="Divisi Acara">Divisi Acara</option>
                    <option value="Divisi Dokumentasi">Divisi Dokumentasi</option>
                    <option value="Divisi Humas">Divisi Humas</option>
                    <option value="Divisi Perlengkapan">Divisi Perlengkapan</option>
                    <option value="lainnya">Lainnya</option>
                </select>

                <button @click="exportCsv" :disabled="aspirations.length === 0"
                    :class="aspirations.length === 0 ? 'bg-gray-400 cursor-not-allowed' : 'bg-green-500 hover:bg-green-600'"
                    class="px-4 py-2 text-white rounded-lg shadow transition text-sm flex items-center gap-2">
                    📥 Export CSV
                </button>
            </div>
        </div>

        {{-- Content --}}
        <template x-if="filteredAspirations.length === 0">
            <div class="bg-white rounded-xl shadow-md p-8 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="text-xl font-semibold text-gray-700 mb-2"
                    x-text="aspirations.length === 0 ? 'Belum ada aspirasi' : 'Tidak ada hasil filter'"></h3>
                <p class="text-gray-500"
                    x-text="aspirations.length === 0 ? 'Belum ada aspirasi yang dikirim untuk event ini.' : 'Tidak ada aspirasi yang sesuai dengan filter yang dipilih.'">
                </p>
            </div>
        </template>

        <template x-if="filteredAspirations.length > 0">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <template x-for="asp in filteredAspirations" :key="asp.id">
                    <div
                        class="bg-white rounded-xl shadow-md overflow-hidden border-l-4 border-red-500 hover:shadow-lg transition-all duration-300">
                        <div class="p-6">
                            {{-- Pesan --}}
                            <div class="flex items-start mb-4">
                                <div class="bg-red-100 p-2 rounded-full mr-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-gray-800" x-text="asp.message"></p>
                                </div>
                            </div>

                            {{-- Footer: To dan Tanggal --}}
                            <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100">
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-500 mr-1"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <span class="text-sm font-medium text-gray-700"
                                        x-text="asp.to === 'lainnya' ? asp.other_to : asp.to"></span>
                                </div>

                                <div class="flex items-center text-xs text-gray-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span x-text="formatDate(asp.created_at)"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </template>
    </div>

    {{-- Back button --}}
    <div class="fixed right-5 bottom-5 bg-red-500 p-3 rounded-xl text-white shadow-lg hover:bg-red-600 transition">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
    </div>

    @if (session('error'))
        <div class="mt-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative text-center">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <script>
        function aspirationEventApp() {
            return {
                aspirations: @json($aspirations),
                filterTarget: '',

                get filteredAspirations() {
                    if (this.filterTarget === '') {
                        return this.aspirations;
                    }

                    if (this.filterTarget === 'lainnya') {
                        return this.aspirations.filter(a => a.to === 'lainnya');
                    }

                    return this.aspirations.filter(a => a.to === this.filterTarget);
                },

                formatDate(dateString) {
                    const date = new Date(dateString);
                    return date.toLocaleString("id-ID", {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                },

                exportCsv() {
                    const eventId = {{ $eventId }};
                    window.location.href = `/aspiration-events/export/${eventId}`;
                }
            }
        }
    </script>

</body>

</html>
