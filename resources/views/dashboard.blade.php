<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin Dashboard</title>
    <link rel="icon" href="{{ asset('images/logo-mpk.jpg') }}" type="image/jpeg">
    @vite('resources/css/app.css')
    <script src="//unpkg.com/alpinejs" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
</head>

<body x-data="{
    showAddModal: false,
    showDeleteModal: false,
    eventToDelete: null,
    eventToDeleteName: '',
    showHidden: false,
    deleteUrl() {
        return '{{ route('events.destroy', ':id') }}'.replace(':id', this.eventToDelete);
    }
}" class="bg-gradient-to-br from-red-50 via-white to-red-50 min-h-screen">
    <div class="min-h-screen">
        <!-- Header -->
        <div class="bg-white shadow-sm border-b border-red-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4 md:py-6">
                    <div class="flex items-center space-x-3">
                        <img src="{{ asset('images/logo-mpk.jpg') }}" alt="Logo MPK" class="h-10 w-10 rounded-lg object-cover shadow-sm">
                        <div class="hidden sm:block">
                            <h1 class="text-xl md:text-2xl font-bold text-gray-900">TenAspiration</h1>
                            <p class="text-xs md:text-sm text-gray-500">Admin Dashboard</p>
                        </div>
                    </div>

                    <!-- Mobile Logo -->
                    <div class="sm:hidden">
                        <h1 class="text-lg font-bold text-gray-900">TenAspiration</h1>
                    </div>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="flex items-center space-x-2 bg-red-500 hover:bg-red-600 text-white p-2 md:px-4 md:py-2 rounded-lg transition duration-200">
                            <!-- Ikon untuk mobile -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 md:h-4 md:w-4" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            <!-- Teks untuk desktop -->
                            <span class="hidden md:inline">Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto py-6 md:py-8 px-4 sm:px-6 lg:px-8">
            <!-- Welcome Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-red-100 p-4 md:p-6 mb-6 md:mb-8">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <h2 class="text-xl md:text-2xl font-bold text-gray-900 mb-2">Selamat Datang, MPK! 👋</h2>
                        <p class="text-gray-600 text-sm md:text-base">Kelola seluruh aspirasi dengan
                            mudah dan efisien.</p>
                    </div>
                    <div class="hidden md:block bg-red-100 p-4 rounded-xl ml-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Stat Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 mb-6 md:mb-8">
                <div class="bg-white rounded-2xl shadow-sm border border-red-100 p-4 md:p-5">
                    <div class="flex items-center space-x-3">
                        <div class="bg-blue-500 p-2 md:p-3 rounded-xl">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 md:h-6 md:w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs md:text-sm text-gray-500">Total Aspirasi</p>
                            <p class="text-lg md:text-2xl font-bold text-gray-900">{{ $totalAspirations }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-red-100 p-4 md:p-5">
                    <div class="flex items-center space-x-3">
                        <div class="bg-green-500 p-2 md:p-3 rounded-xl">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 md:h-6 md:w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs md:text-sm text-gray-500">Aspirasi Hari Ini</p>
                            <p class="text-lg md:text-2xl font-bold text-gray-900">{{ $todayAspirations }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-red-100 p-4 md:p-5">
                    <div class="flex items-center space-x-3">
                        <div class="bg-purple-500 p-2 md:p-3 rounded-xl">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 md:h-6 md:w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs md:text-sm text-gray-500">Keluh Kesah</p>
                            <p class="text-lg md:text-2xl font-bold text-gray-900">{{ $totalKeluhKesah }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-red-100 p-4 md:p-5">
                    <div class="flex items-center space-x-3">
                        <div class="bg-orange-500 p-2 md:p-3 rounded-xl">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 md:h-6 md:w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs md:text-sm text-gray-500">Total Event</p>
                            <p class="text-lg md:text-2xl font-bold text-gray-900">{{ $totalEvents }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6 mb-6 md:mb-8">
                <a href="{{ route('aspirations.index') }}"
                    class="bg-white rounded-2xl shadow-sm border border-red-100 p-4 md:p-6 hover:shadow-md transition duration-200 group">
                    <div class="flex items-center space-x-3 md:space-x-4">
                        <div class="bg-red-500 p-2 md:p-3 rounded-xl group-hover:bg-red-600 transition duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 md:h-6 md:w-6 text-white"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 text-base md:text-lg">Aspirasi Audiensi</h3>
                            <p class="text-gray-600 text-xs md:text-sm">Kelola aspirasi Audiensi dari siswa</p>
                        </div>
                    </div>
                </a>

                <!-- Tombol Keluh Kesah -->
                <a href="{{ route('aspiration_keluhkesah.index') }}"
                    class="bg-white rounded-2xl shadow-sm border border-red-100 p-4 md:p-6 hover:shadow-md transition duration-200 group">
                    <div class="flex items-center space-x-3 md:space-x-4">
                        <div class="bg-red-500 p-2 md:p-3 rounded-xl group-hover:bg-red-600 transition duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 md:h-6 md:w-6 text-white"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 text-base md:text-lg">Keluh Kesah</h3>
                            <p class="text-gray-600 text-xs md:text-sm">Kelola keluh kesah dari siswa</p>
                        </div>
                    </div>
                </a>


                <button @click="showAddModal = true"
                    class="bg-white rounded-2xl shadow-sm border border-red-100 p-4 md:p-6 hover:shadow-md transition duration-200 group text-left w-full">
                    <div class="flex items-center space-x-3 md:space-x-4">
                        <div class="bg-red-500 p-2 md:p-3 rounded-xl group-hover:bg-red-600 transition duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 md:h-6 md:w-6 text-white"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 text-base md:text-lg">Tambah Event</h3>
                            <p class="text-gray-600 text-xs md:text-sm">Buat event baru untuk menerima aspirasi</p>
                        </div>
                    </div>
                </button>
            </div>

            <!-- Events Section -->
            <div class="bg-white rounded-2xl shadow-sm border border-red-100 p-4 md:p-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4 md:mb-6">
                    <h2 class="text-lg md:text-xl font-bold text-gray-900 flex items-center space-x-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 md:h-5 md:w-5 text-red-500"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>Daftar Event</span>
                    </h2>
                    <div class="flex items-center gap-2">
                        <button @click="showHidden = !showHidden"
                            :class="showHidden ? 'bg-gray-200 text-gray-700' : 'bg-gray-100 text-gray-500 hover:text-gray-700'"
                            class="px-3 py-1.5 rounded-lg text-xs font-medium transition flex items-center gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                            <span x-text="showHidden ? 'Semua Event' : 'Tersembunyi'"></span>
                        </button>
                        <span
                            class="bg-red-100 text-red-600 px-2 py-1 md:px-3 md:py-1 rounded-full text-xs md:text-sm font-medium">
                            {{ $events->count() }} Event
                        </span>
                    </div>
                </div>

                @php $visibleEvents = $events->where('is_hidden', false); $hiddenEvents = $events->where('is_hidden', true); @endphp

                @if ($visibleEvents->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
                        @foreach ($visibleEvents as $event)
                            @include('dashboard._event_card', ['event' => $event, 'hidden' => false])
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 md:py-12">
                        <div class="bg-red-50 p-4 md:p-6 rounded-2xl inline-block mb-3 md:mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 md:h-12 md:w-12 text-red-400 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-base md:text-lg font-semibold text-gray-900 mb-2">Belum ada event</h3>
                        <p class="text-gray-600 text-sm md:text-base mb-3 md:mb-4">Mulai dengan membuat event pertama Anda</p>
                        <button @click="showAddModal = true"
                            class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 md:px-6 md:py-2 rounded-lg transition duration-200 font-medium text-sm md:text-base">
                            Buat Event Pertama
                        </button>
                    </div>
                @endif

                {{-- Hidden events section --}}
                <div x-show="showHidden" x-cloak class="mt-6 pt-6 border-t border-gray-200">
                    <div class="flex items-center gap-2 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                        <h3 class="text-sm font-semibold text-gray-500">Event Tersembunyi</h3>
                        <span class="bg-gray-200 text-gray-500 px-2 py-0.5 rounded-full text-xs">{{ $hiddenEvents->count() }}</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
                        @foreach ($hiddenEvents as $event)
                            @include('dashboard._event_card', ['event' => $event, 'hidden' => true])
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Chart Section --}}
            <div class="mt-6 md:mt-8 bg-white rounded-2xl shadow-sm border border-red-100 p-4 md:p-6" x-data="chartApp()" x-init="initCharts()">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4 md:mb-6">
                    <h2 class="text-lg md:text-xl font-bold text-gray-900 flex items-center space-x-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 md:h-5 md:w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span>Statistik</span>
                    </h2>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-400">30 hari terakhir</span>
                        <button @click="refreshCharts()" class="text-gray-400 hover:text-red-500 transition p-1" title="Muat ulang">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Tabs --}}
                <div class="flex gap-1 mb-5 bg-gray-100 rounded-xl p-1 overflow-x-auto">
                    <template x-for="tab in tabs" :key="tab.key">
                        <button @click="switchTab(tab.key)"
                            :class="selectedType === tab.key ? 'bg-white text-red-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                            class="px-4 py-2 text-sm font-medium rounded-lg transition whitespace-nowrap">
                            <span x-text="tab.label"></span>
                        </button>
                    </template>
                </div>

                <div x-show="loading" class="flex items-center justify-center py-12">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-600"></div>
                </div>

                <template x-if="!loading && error">
                    <div class="text-center py-8 text-red-500 text-sm">
                        Gagal memuat statistik. <button @click="refreshCharts()" class="underline">Coba lagi</button>
                    </div>
                </template>

                <div x-show="!loading && !error" x-cloak>
                    {{-- Trend Chart --}}
                    <div class="mb-6 md:mb-8">
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Tren Harian</h3>
                        <div class="relative" style="height: 280px;">
                            <canvas x-ref="trendChart" class="w-full h-full"></canvas>
                        </div>
                    </div>

                    {{-- Secondary charts --}}
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        {{-- Bar: per dept (all/audiensi) or per event (event) --}}
                        <div x-show="selectedType !== 'keluh_kesah'">
                            <h3 class="text-sm font-semibold text-gray-700 mb-3" x-text="barTitle"></h3>
                            <div class="relative" style="height: 300px;">
                                <canvas x-ref="barChart" class="w-full h-full"></canvas>
                            </div>
                        </div>

                        {{-- Donut: only all --}}
                        <div x-show="selectedType === 'all'">
                            <h3 class="text-sm font-semibold text-gray-700 mb-3">Distribusi Tipe</h3>
                            <div class="relative" style="height: 300px;">
                                <canvas x-ref="donutChart" class="w-full h-full"></canvas>
                            </div>
                        </div>

                        {{-- Class: only audiensi --}}
                        <div x-show="selectedType === 'audiensi'">
                            <h3 class="text-sm font-semibold text-gray-700 mb-3">Per Kelas</h3>
                            <div class="relative" style="height: 300px;">
                                <canvas x-ref="classChart" class="w-full h-full"></canvas>
                            </div>
                        </div>

                        {{-- Keluh kesah placeholder --}}
                        <div x-show="selectedType === 'keluh_kesah'"
                            class="flex items-center justify-center h-full py-12">
                            <p class="text-gray-400 text-sm">Pilih tab lain untuk melihat breakdown detail.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Tambah Event -->
        <div x-show="showAddModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showAddModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm transition-opacity"></div>

                <div
                    class="inline-block w-full align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 sm:px-6 pt-6 pb-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg sm:text-xl font-bold text-gray-900">Tambah Event Baru</h3>
                            <button @click="showAddModal = false" class="text-gray-400 hover:text-black">
                                <svg class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <form action="{{ route('events.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Event</label>
                                <input type="text" name="name" value="{{ old('name') }}" placeholder="Masukkan nama event" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 sm:px-4 sm:py-3 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition duration-200 text-sm sm:text-base">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                                <textarea name="description" placeholder="Masukkan deskripsi event (opsional)" rows="3"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 sm:px-4 sm:py-3 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition duration-200 text-sm sm:text-base">{{ old('description') }}</textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Event</label>
                                <input type="date" name="date" value="{{ old('date') }}" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 sm:px-4 sm:py-3 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition duration-200 text-sm sm:text-base">
                            </div>

                            <div class="flex justify-end space-x-3 pt-4">
                                <button type="button" @click="showAddModal = false"
                                    class="px-3 py-2 sm:px-4 sm:py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition duration-200 font-medium text-sm sm:text-base">
                                    Batal
                                </button>
                                <button type="submit"
                                    class="px-4 py-2 sm:px-6 sm:py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition duration-200 font-medium text-sm sm:text-base">
                                    Simpan Event
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Delete Confirmation -->
        <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showDeleteModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm transition-opacity"></div>

                <div
                    class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 sm:px-6 pt-6 pb-4">
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="bg-red-100 p-2 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6 text-red-600"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                            </div>
                            <h3 class="text-lg sm:text-xl font-bold text-gray-900">Hapus Event</h3>
                        </div>

                        <p class="text-gray-700 text-sm sm:text-base mb-2">
                            Anda akan menghapus event: <span class="font-semibold text-red-600"
                                x-text="eventToDeleteName"></span>
                        </p>
                        <p class="text-xs sm:text-sm text-gray-500 mb-6">
                            Tindakan ini tidak dapat dibatalkan. Semua aspirasi yang terkait dengan event ini juga akan
                            dihapus.
                        </p>

                        <div class="flex justify-end space-x-3">
                            <button @click="showDeleteModal = false; eventToDelete = null; eventToDeleteName = ''"
                                class="px-3 py-2 sm:px-4 sm:py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition duration-200 font-medium text-sm sm:text-base">
                                Batal
                            </button>
                            <form :action="deleteUrl()" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="px-4 py-2 sm:px-6 sm:py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition duration-200 font-medium text-sm sm:text-base">
                                    Ya, Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('chartApp', () => ({
                loading: true,
                error: false,
                selectedType: 'all',
                chartInstances: [],
                tabs: [
                    { key: 'all', label: 'Semua' },
                    { key: 'audiensi', label: 'Audiensi' },
                    { key: 'event', label: 'Event' },
                    { key: 'keluh_kesah', label: 'Keluh Kesah' },
                ],

                get barTitle() {
                    return {
                        all: 'Per Departemen',
                        audiensi: 'Per Departemen',
                        event: 'Per Event',
                        keluh_kesah: '',
                    }[this.selectedType] || '';
                },

                async fetchStats() {
                    this.error = false;
                    try {
                        const res = await fetch('{{ route("api.statistics") }}?type=' + this.selectedType);
                        if (!res.ok) throw new Error('Failed');
                        return await res.json();
                    } catch (e) {
                        this.error = true;
                        return null;
                    }
                },

                switchTab(type) {
                    this.selectedType = type;
                    this.loading = true;
                    this.fetchStats().then(data => {
                        this.loading = false;
                        if (data) {
                            this.$nextTick(() => this.buildCharts(data));
                        }
                    });
                },

                initCharts() {
                    this.switchTab('all');
                },

                buildCharts(data) {
                    this.destroyCharts();

                    const colors = [
                        '#ef4444', '#f97316', '#eab308', '#22c55e', '#3b82f6',
                        '#8b5cf6', '#ec4899', '#14b8a6', '#6366f1', '#a855f7',
                    ];
                    const trendLabels = data.daily_totals.map(d => {
                        const p = d.date.split('-');
                        return p[2] + '/' + p[1];
                    });

                    const trendDs = [];
                    if (this.selectedType === 'all') {
                        trendDs.push({ label: 'Audiensi', data: data.daily_totals.map(d => d.aspirations), borderColor: '#3b82f6', backgroundColor: 'rgba(59,130,246,0.1)', fill: true, tension: 0.3, pointRadius: 2 });
                        trendDs.push({ label: 'Event', data: data.daily_totals.map(d => d.events), borderColor: '#f97316', backgroundColor: 'rgba(249,115,22,0.1)', fill: true, tension: 0.3, pointRadius: 2 });
                        trendDs.push({ label: 'Keluh Kesah', data: data.daily_totals.map(d => d.keluh_kesah), borderColor: '#22c55e', backgroundColor: 'rgba(34,197,94,0.1)', fill: true, tension: 0.3, pointRadius: 2 });
                    } else if (this.selectedType === 'audiensi') {
                        trendDs.push({ label: 'Audiensi', data: data.daily_totals.map(d => d.aspirations), borderColor: '#3b82f6', backgroundColor: 'rgba(59,130,246,0.15)', fill: true, tension: 0.3, pointRadius: 3 });
                    } else if (this.selectedType === 'event') {
                        trendDs.push({ label: 'Event', data: data.daily_totals.map(d => d.events), borderColor: '#f97316', backgroundColor: 'rgba(249,115,22,0.15)', fill: true, tension: 0.3, pointRadius: 3 });
                    } else {
                        trendDs.push({ label: 'Keluh Kesah', data: data.daily_totals.map(d => d.keluh_kesah), borderColor: '#22c55e', backgroundColor: 'rgba(34,197,94,0.15)', fill: true, tension: 0.3, pointRadius: 3 });
                    }

                    const trendCtx = this.$refs.trendChart.getContext('2d');
                    this.chartInstances.push(new Chart(trendCtx, {
                        type: 'line',
                        data: { labels: trendLabels, datasets: trendDs },
                        options: {
                            responsive: true, maintainAspectRatio: false,
                            interaction: { mode: 'index', intersect: false },
                            plugins: {
                                legend: { position: 'bottom', labels: { boxWidth: 12, padding: 12, font: { size: 11 } } }
                            },
                            scales: {
                                x: { grid: { display: false }, ticks: { font: { size: 10 }, maxTicksLimit: 15 } },
                                y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 10 } } }
                            }
                        }
                    }));

                    // Bar chart (dept for all/audiensi, event for event)
                    const barData = data.by_department || data.by_event;
                    const barLabelKey = data.by_department ? 'department' : 'event';
                    if (barData && barData.length) {
                        const ctx = this.$refs.barChart.getContext('2d');
                        this.chartInstances.push(new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: barData.map(d => d[barLabelKey]),
                                datasets: [{
                                    label: 'Jumlah',
                                    data: barData.map(d => d.total),
                                    backgroundColor: barData.map((_, i) => colors[i % colors.length]),
                                    borderRadius: 4,
                                }]
                            },
                            options: {
                                responsive: true, maintainAspectRatio: false, indexAxis: 'y',
                                plugins: { legend: { display: false } },
                                scales: {
                                    x: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 10 } } },
                                    y: { ticks: { font: { size: 10 } } }
                                }
                            }
                        }));
                    }

                    // Class chart (audiensi)
                    if (data.by_class && data.by_class.length) {
                        const ctx = this.$refs.classChart.getContext('2d');
                        this.chartInstances.push(new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: data.by_class.map(d => d.class),
                                datasets: [{
                                    label: 'Jumlah',
                                    data: data.by_class.map(d => d.total),
                                    backgroundColor: ['#3b82f6', '#f97316', '#22c55e'],
                                    borderRadius: 4,
                                }]
                            },
                            options: {
                                responsive: true, maintainAspectRatio: false,
                                plugins: { legend: { display: false } },
                                scales: {
                                    x: { grid: { display: false }, ticks: { font: { size: 12 } } },
                                    y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 10 } } }
                                }
                            }
                        }));
                    }

                    // Donut (only for 'all')
                    if (data.by_type) {
                        const ctx = this.$refs.donutChart.getContext('2d');
                        this.chartInstances.push(new Chart(ctx, {
                            type: 'doughnut',
                            data: {
                                labels: ['Audiensi', 'Event', 'Keluh Kesah'],
                                datasets: [{
                                    data: [data.by_type.aspirations, data.by_type.events, data.by_type.keluh_kesah],
                                    backgroundColor: ['#3b82f6', '#f97316', '#22c55e'],
                                    borderWidth: 2,
                                }]
                            },
                            options: {
                                responsive: true, maintainAspectRatio: false, cutout: '65%',
                                plugins: {
                                    legend: { position: 'bottom', labels: { boxWidth: 12, padding: 12, font: { size: 11 } } }
                                }
                            }
                        }));
                    }
                },

                destroyCharts() {
                    this.chartInstances.forEach(c => c.destroy());
                    this.chartInstances = [];
                },

                refreshCharts() {
                    this.switchTab(this.selectedType);
                }
            }));
        });
    </script>
</body>

</html>
