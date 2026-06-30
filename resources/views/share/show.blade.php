<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aspirasi Dibagikan</title>
    @vite('resources/css/app.css')
    <script src="//unpkg.com/alpinejs" defer></script>
    <style>
        [x-cloak] { display: none !important; }
        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .card-fade-in { animation: fadeInUp 0.35s ease both; }
        /* Desktop filter sticky */
        .filter-bar-sticky-desktop {
            position: fixed;
            top: 8px; left: 50%; transform: translateX(-50%);
            z-index: 40;
            width: calc(100% - 2rem);
            max-width: 56rem;
        }
        /* Mobile filter sticky */
        .filter-bar-sticky-mobile {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 40;
            padding: 0.5rem 1rem;
            background: rgba(255,255,255,0.92);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid #fecaca;
            box-shadow: 0 2px 8px 0 rgba(0,0,0,0.07);
        }
    </style>
</head>

<body class="min-h-screen bg-gradient-to-br from-red-50 via-white to-red-100 py-10 px-4"
    x-data="shareApp()" @scroll.window="onWindowScroll()" x-init="init()">

    <!-- Header -->
    <div class="max-w-4xl mx-auto mb-8">
        <div class="text-center sm:text-left">
            <h1 class="text-2xl font-bold text-red-700 flex items-center justify-center sm:justify-start gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span x-text="title || 'Aspirasi Dibagikan'"></span>
            </h1>
            <p class="text-gray-500 text-sm mt-1 text-center sm:text-left"
                x-text="'Menampilkan ' + filteredData.length + ' dari ' + allData.length + ' aspirasi'"></p>
        </div>
    </div>

    <!-- FILTER SECTION — wrapper with spacers for sticky -->
    <div class="max-w-4xl mx-auto mb-6">

        <!-- Desktop spacer -->
        <div :style="filterSticky ? 'height:' + (filterBarHeight + 16) + 'px' : ''" class="transition-all duration-300 hidden md:block"></div>
        <!-- Mobile spacer -->
        <div :style="filterSticky ? 'height:' + filterBarMobileHeight + 'px' : ''" class="transition-all duration-300 md:hidden"></div>

        <!-- === DESKTOP FILTER (md+) === -->
        <div x-ref="filterBar"
            :class="filterSticky ? 'filter-bar-sticky-desktop bg-white/95 backdrop-blur-sm rounded-xl border border-red-200 shadow-lg p-3' : 'bg-white/70 backdrop-blur-sm rounded-xl border border-red-200 shadow-sm p-3 md:p-4'"
            class="hidden md:block">

            <!-- aspiration filters -->
            <template x-if="type === 'aspiration'">
                <div class="flex flex-col md:flex-row md:items-center gap-3">
                    <div class="relative md:min-w-[200px]">
                        <input type="text" x-model="searchQuery" @input.debounce.600ms="applyFilter()" placeholder="Cari aspirasi..."
                            class="w-full pl-3 pr-3 py-2 rounded-lg bg-white border border-red-200 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-red-400 placeholder:text-gray-400">
                    </div>
                    <div class="hidden md:block w-px h-7 bg-red-200 shrink-0"></div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-500 font-medium shrink-0">Bagian</span>
                        <select x-model="filterBagian" @change="applyFilter()"
                            class="px-3 py-2 rounded-lg bg-white border border-red-200 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-red-400">
                            <option value="">Semua</option>
                            <template x-for="opt in bagianOptions" :key="opt.value">
                                <option :value="opt.value" x-text="opt.label"></option>
                            </template>
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-500 font-medium shrink-0">Kelas</span>
                        <select x-model="filterKelas" @change="applyFilter()"
                            class="px-3 py-2 rounded-lg bg-white border border-red-200 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-red-400">
                            <option value="">Semua</option>
                            <option value="X">X</option>
                            <option value="XI">XI</option>
                            <option value="XII">XII</option>
                        </select>
                    </div>
                    <div class="hidden md:block w-px h-7 bg-red-200 shrink-0"></div>
                    <div class="flex items-center gap-1.5">
                        <input type="date" x-model="dateFrom" @change="applyFilter()"
                            class="px-2 py-2 rounded-lg bg-white border border-red-200 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 w-[115px]">
                        <span class="text-gray-400 text-xs">—</span>
                        <input type="date" x-model="dateTo" @change="applyFilter()"
                            class="px-2 py-2 rounded-lg bg-white border border-red-200 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 w-[115px]">
                    </div>
                    <button @click="resetFilter()" x-show="searchQuery || filterBagian || filterKelas || dateFrom || dateTo"
                        class="shrink-0 text-xs text-red-500 hover:text-red-700 font-medium transition px-2 py-1.5 rounded-lg hover:bg-red-50">↺ Reset</button>
                </div>
            </template>

            <!-- keluh_kesah + aspiration_event filters -->
            <template x-if="type === 'keluh_kesah' || type === 'aspiration_event'">
                <div class="flex flex-col md:flex-row md:items-center gap-3">
                    <div class="relative md:min-w-[220px]">
                        <input type="text" x-model="searchQuery" @input.debounce.600ms="applyFilter()"
                            :placeholder="type === 'keluh_kesah' ? 'Cari keluh kesah...' : 'Cari aspirasi...'"
                            class="w-full pl-3 pr-3 py-2 rounded-lg bg-white border border-red-200 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-red-400 placeholder:text-gray-400">
                    </div>
                    <div class="hidden md:block w-px h-7 bg-red-200 shrink-0"></div>
                    <div class="flex items-center gap-1.5">
                        <input type="date" x-model="dateFrom" @change="applyFilter()"
                            class="px-2 py-2 rounded-lg bg-white border border-red-200 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 w-[130px]">
                        <span class="text-gray-400 text-xs">—</span>
                        <input type="date" x-model="dateTo" @change="applyFilter()"
                            class="px-2 py-2 rounded-lg bg-white border border-red-200 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 w-[130px]">
                    </div>
                    <button @click="resetFilter()" x-show="searchQuery || dateFrom || dateTo"
                        class="shrink-0 text-xs text-red-500 hover:text-red-700 font-medium transition px-2 py-1.5 rounded-lg hover:bg-red-50">↺ Reset</button>
                </div>
            </template>
        </div>

        <!-- === MOBILE FILTER (< md) === -->
        <div :class="filterSticky ? 'filter-bar-sticky-mobile' : 'bg-white/70 backdrop-blur-sm rounded-xl border border-red-200 shadow-sm p-3'"
            class="md:hidden" x-ref="filterBarMobile">

            <!-- Collapsed bar saat sticky -->
            <div x-show="filterSticky" class="flex items-center justify-between gap-2">
                <div class="flex items-center gap-2 min-w-0 flex-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z" />
                    </svg>
                    <span class="text-sm font-medium text-gray-700">Filter
                        <template x-if="searchQuery || filterBagian || filterKelas || dateFrom || dateTo">
                            <span class="text-red-500 text-xs ml-1">(aktif)</span>
                        </template>
                    </span>
                </div>
                <button @click="filterExpanded = !filterExpanded"
                    class="flex items-center gap-1.5 px-3 py-2 text-xs font-medium text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 rounded-lg transition border border-red-200">
                    <span x-text="filterExpanded ? 'Tutup' : 'Buka'"></span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 transition-transform" :class="filterExpanded ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
            </div>

            <!-- Filter content mobile -->
            <div :class="filterSticky ? (filterExpanded ? 'mt-3 pt-3 border-t border-red-100' : 'hidden') : ''"
                class="flex flex-col gap-3">
                <div class="relative">
                    <input type="text" x-model="searchQuery" @input.debounce.600ms="applyFilter()"
                        :placeholder="type === 'keluh_kesah' ? 'Cari keluh kesah...' : 'Cari aspirasi...'"
                        class="w-full pl-3 pr-3 py-2 rounded-lg bg-white border border-red-200 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-red-400 placeholder:text-gray-400">
                </div>
                <!-- Bagian & Kelas (hanya untuk aspiration) -->
                <template x-if="type === 'aspiration'">
                    <div class="flex gap-3">
                        <div class="flex-1">
                            <label class="text-xs text-gray-500 font-medium block mb-1">Bagian</label>
                            <select x-model="filterBagian" @change="applyFilter()"
                                class="w-full px-3 py-2 rounded-lg bg-white border border-red-200 text-sm focus:outline-none focus:ring-2 focus:ring-red-400">
                                <option value="">Semua</option>
                                <template x-for="opt in bagianOptions" :key="opt.value">
                                    <option :value="opt.value" x-text="opt.label"></option>
                                </template>
                            </select>
                        </div>
                        <div class="flex-1">
                            <label class="text-xs text-gray-500 font-medium block mb-1">Kelas</label>
                            <select x-model="filterKelas" @change="applyFilter()"
                                class="w-full px-3 py-2 rounded-lg bg-white border border-red-200 text-sm focus:outline-none focus:ring-2 focus:ring-red-400">
                                <option value="">Semua</option>
                                <option value="X">Kelas X</option>
                                <option value="XI">Kelas XI</option>
                                <option value="XII">Kelas XII</option>
                            </select>
                        </div>
                    </div>
                </template>
                <!-- Date range -->
                <div class="flex items-center gap-2">
                    <label class="text-xs text-gray-500 font-medium shrink-0">Tanggal</label>
                    <div class="flex items-center gap-1.5 flex-1">
                        <input type="date" x-model="dateFrom" @change="applyFilter()"
                            class="flex-1 px-2 py-2 rounded-lg bg-white border border-red-200 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 min-w-0">
                        <span class="text-gray-400 text-xs">—</span>
                        <input type="date" x-model="dateTo" @change="applyFilter()"
                            class="flex-1 px-2 py-2 rounded-lg bg-white border border-red-200 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 min-w-0">
                    </div>
                </div>
                <button @click="resetFilter()" x-show="searchQuery || filterBagian || filterKelas || dateFrom || dateTo"
                    class="text-xs text-red-500 hover:text-red-700 font-medium transition px-2 py-1.5 rounded-lg hover:bg-red-50 text-left">↺ Reset Filter</button>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="flex flex-wrap gap-4 max-w-5xl mx-auto">
        <!-- Loading State -->
        <div x-show="loading" class="w-full bg-white rounded-xl shadow-md p-12 text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-red-600 mx-auto"></div>
            <p class="mt-4 text-gray-600">Memuat data aspirasi...</p>
        </div>

        <!-- Aspiration Cards (voxes) -->
        <template x-if="!loading && type === 'aspiration'">
            <template x-for="asp in pagedData" :key="asp.id">
                <div class="card-fade-in bg-white rounded-xl flex flex-col sm:flex-row justify-between shadow-md p-5 border-l-8 hover:shadow-lg transition w-full sm:w-[48%] lg:w-[31%]"
                    :class="asp.to === 'MPK' ? 'border-red-600' : asp.to === 'OSIS' ? 'border-blue-500' : 'border-gray-400'">
                    <div class="flex items-start gap-3 flex-1 min-w-0">
                        <div class="flex-1 min-w-0">
                            <h3 class="text-base font-semibold flex items-center gap-1.5"
                                :class="asp.to === 'MPK' ? 'text-red-700' : asp.to === 'OSIS' ? 'text-blue-700' : 'text-gray-800'">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                                </svg>
                                <span x-text="asp.to"></span>
                            </h3>
                            <p class="text-gray-700 mt-1.5 break-words text-sm" x-text="asp.message"></p>
                            <p class="text-gray-500 mt-1 text-xs" x-text="asp.kelas"></p>
                            <p class="text-xs text-gray-400 mt-2" x-text="formatDate(asp.created_at)"></p>
                        </div>
                    </div>
                </div>
            </template>
        </template>

        <!-- Keluh Kesah Cards -->
        <template x-if="!loading && type === 'keluh_kesah'">
            <template x-for="msg in pagedData" :key="msg.id">
                <div class="card-fade-in bg-white rounded-xl shadow-md hover:shadow-lg transition w-full sm:w-[48%] lg:w-[31%] flex flex-col cursor-pointer"
                    @click="showDetail(msg)" x-data="{
                        isContentOverflowing: false,
                        checkOverflow() {
                            this.\$nextTick(() => {
                                const el = this.\$refs['desc' + msg.id];
                                if (el) this.isContentOverflowing = el.scrollHeight > el.clientHeight;
                            });
                        }
                    }" x-init="checkOverflow()">
                    <div class="p-5 flex-1">
                        <div class="mb-4">
                            <template x-if="questions.length > 0">
                                <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full mb-3 bg-red-100 text-red-700"
                                    x-text="questions[0].question_label"></span>
                            </template>
                            <div class="relative">
                                <template x-if="questions.length > 0">
                                    <p class="text-gray-700 text-sm line-clamp-3" :ref="'desc' + msg.id"
                                        x-text="getAnswer(msg, questions[0].question_key)"></p>
                                </template>
                                <div x-show="isContentOverflowing"
                                    class="absolute bottom-0 left-0 right-0 h-6 bg-gradient-to-t from-white to-transparent"></div>
                            </div>
                        </div>
                    </div>
                    <div class="pt-4 border-t border-gray-100 p-5 mt-auto">
                        <div class="flex justify-between items-center">
                            <p class="text-xs text-gray-500" x-text="formatDate(msg.created_at)"></p>
                            <div class="text-xs text-gray-400">
                                <span x-text="questions.length > 1 ? '+' + (questions.length - 1) + ' lagi' : ''"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </template>

        <!-- Aspiration Event Cards -->
        <template x-if="!loading && type === 'aspiration_event'">
            <div class="w-full">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <template x-for="asp in pagedData" :key="asp.id">
                        <div x-data="{ open: false }"
                            class="card-fade-in bg-white rounded-xl shadow-md overflow-hidden border-l-4 border-red-500 hover:shadow-lg transition-all duration-300 flex flex-col">
                            <div class="p-6 flex-1 flex flex-col">
                                <div class="flex items-start justify-between mb-3">
                                    <h3 class="text-base font-semibold text-red-600" x-text="questions && questions.length > 0 ? questions[0].question_label : 'Aspirasi'"></h3>
                                </div>
                                <div class="mb-4 flex-1">
                                    <p class="text-gray-800 text-sm leading-relaxed" :class="open ? '' : 'line-clamp-3'"
                                        x-text="questions && questions.length > 0 ? getAnswer(asp, questions[0].question_key) : (asp.message || '')"></p>
                                </div>
                                <div x-show="open" x-transition class="space-y-3 mb-4">
                                    <template x-if="questions && questions.length > 1">
                                        <template x-for="(q, idx) in questions.slice(1)" :key="q.question_key">
                                            <div class="bg-red-50 rounded-lg p-3 border border-red-100">
                                                <h4 class="text-sm font-semibold text-red-700 mb-1.5" x-text="q.question_label"></h4>
                                                <p class="text-gray-700 text-sm leading-relaxed" x-text="getAnswer(asp, q.question_key)"></p>
                                            </div>
                                        </template>
                                    </template>
                                </div>
                                <div class="mt-auto pt-4 border-t border-gray-100">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-gray-500" x-text="formatDate(asp.created_at)"></span>
                                        <button @click="open = !open"
                                            class="text-xs font-medium text-red-600 hover:text-red-700 transition-colors flex items-center gap-1 px-3 py-1 rounded-full hover:bg-red-50 border border-red-200">
                                            <span x-text="open ? 'Sembunyikan' : 'Lihat Selengkapnya'"></span>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 transition-transform" :class="{'rotate-180': open}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </template>

        <!-- Loading More (infinite scroll) -->
        <div x-show="loadingMore" class="w-full text-center py-6">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-600 mx-auto"></div>
            <p class="mt-2 text-gray-500 text-sm">Memuat data lainnya...</p>
        </div>

        <!-- Sentinel for infinite scroll -->
        <div x-ref="sentinel" class="w-full h-4"></div>

        <!-- Empty State -->
        <div x-show="!loading && filteredData.length === 0"
            class="bg-white rounded-xl w-full shadow-md p-8 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">Tidak ada aspirasi</h3>
            <p class="text-gray-500">Tidak ada aspirasi yang sesuai dengan filter yang dipilih.</p>
        </div>
    </div>

    <!-- Detail Modal (keluh_kesah) -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50 backdrop-blur-sm"
        @click.self="showModal = false">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto flex flex-col" @click.stop>
            <div class="sticky top-0 bg-white p-6 border-b border-gray-200 rounded-t-xl flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Detail Keluh Kesah</h2>
                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full mt-2 bg-red-100 text-red-700"
                        x-text="questions.length > 0 ? questions[0].question_label : 'Keluh Kesah'"></span>
                </div>
                <button @click="showModal = false" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="p-6" x-show="currentMessage">
                <template x-for="q in questions" :key="q.question_key">
                    <div class="mb-6">
                        <div class="p-4 bg-red-50 rounded-lg">
                            <h3 class="font-semibold text-gray-700 mb-2" x-text="q.question_label"></h3>
                            <p class="text-gray-800 whitespace-pre-wrap" x-text="getAnswer(currentMessage, q.question_key)"></p>
                        </div>
                    </div>
                </template>
                <div class="text-sm text-gray-600">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span x-text="currentMessage ? formatDate(currentMessage.created_at) : ''"></span>
                    </div>
                </div>
            </div>
            <div class="sticky bottom-0 bg-white p-4 border-t border-gray-200 rounded-b-xl">
                <div class="flex justify-end">
                    <button @click="showModal = false"
                        class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition font-medium">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scroll to Top -->
    <div class="fixed right-5 bottom-5 z-30">
        <button x-show="showScrollTop" x-cloak @click="window.scrollTo({top:0,behavior:'smooth'})"
            class="bg-white border border-red-200 text-red-500 p-3 rounded-xl shadow-lg hover:bg-red-50 transition"
            title="Kembali ke atas">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
            </svg>
        </button>
    </div>

    <script>
        function shareApp() {
            return {
                allData: [],
                filteredData: [],
                pagedData: [],
                loading: true,
                loadingMore: false,
                currentPage: 1,
                pageSize: 18,
                type: '',
                title: '',
                questions: [],
                bagianOptions: [],
                searchQuery: '',
                filterBagian: '',
                filterKelas: '',
                dateFrom: '',
                dateTo: '',
                showModal: false,
                currentMessage: null,
                filterSticky: false,
                filterExpanded: false,
                filterBarHeight: 0,
                filterBarMobileHeight: 0,
                showScrollTop: false,

                async init() {
                    const token = '{{ $share->token }}';
                    try {
                        const res = await fetch(`/api/share/${token}/data`);
                        const result = await res.json();
                        this.allData = result.data;
                        this.filteredData = result.data;
                        this.type = result.type;
                        this.title = result.share.title;
                        this.questions = result.questions || [];
                        this.bagianOptions = result.bagianOptions || [];
                    } catch (error) {
                        console.error('Gagal memuat data:', error);
                    } finally {
                        this.loading = false;
                        this.currentPage = 1;
                        this.updatePagedData();
                        this.$nextTick(() => {
                            if (this.$refs.filterBar) this.filterBarHeight = this.$refs.filterBar.offsetHeight;
                            if (this.$refs.filterBarMobile) this.filterBarMobileHeight = this.$refs.filterBarMobile.offsetHeight;
                        });
                    }
                },

                updatePagedData() {
                    this.pagedData = this.filteredData.slice(0, this.currentPage * this.pageSize);
                },

                hasMore() {
                    return this.pagedData.length < this.filteredData.length;
                },

                loadMore() {
                    if (this.loadingMore || !this.hasMore()) return;
                    this.loadingMore = true;
                    setTimeout(() => {
                        this.currentPage++;
                        this.updatePagedData();
                        this.loadingMore = false;
                    }, 300);
                },

                applyFilter() {
                    let data = [...this.allData];

                    if (this.searchQuery) {
                        const q = this.searchQuery.toLowerCase();
                        data = data.filter(item => {
                            if (this.type === 'aspiration') {
                                return (item.message || '').toLowerCase().includes(q)
                                    || (item.to || '').toLowerCase().includes(q)
                                    || (item.kelas || '').toLowerCase().includes(q);
                            } else if (this.type === 'keluh_kesah') {
                                const match = (item.keluh_kesah || '').toLowerCase().includes(q)
                                    || (item.phone_number || '').toLowerCase().includes(q);
                                if (match) return true;
                                if (item.custom_answers) {
                                    const answers = typeof item.custom_answers === 'string' ? JSON.parse(item.custom_answers) : item.custom_answers;
                                    return Object.values(answers).some(v => (v || '').toLowerCase().includes(q));
                                }
                                return false;
                            } else if (this.type === 'aspiration_event') {
                                const match = (item.message || '').toLowerCase().includes(q)
                                    || (item.kesan_pesan || '').toLowerCase().includes(q)
                                    || (item.perubahan_dari_event || '').toLowerCase().includes(q);
                                if (match) return true;
                                if (item.custom_answers) {
                                    const answers = typeof item.custom_answers === 'string' ? JSON.parse(item.custom_answers) : item.custom_answers;
                                    return Object.values(answers).some(v => (v || '').toLowerCase().includes(q));
                                }
                                return false;
                            }
                            return true;
                        });
                    }

                    if (this.type === 'aspiration' && this.filterBagian) {
                        data = data.filter(item => item.to === this.filterBagian);
                    }
                    if (this.type === 'aspiration' && this.filterKelas) {
                        data = data.filter(item => item.kelas === this.filterKelas);
                    }
                    if (this.dateFrom) {
                        data = data.filter(item => new Date(item.created_at) >= new Date(this.dateFrom));
                    }
                    if (this.dateTo) {
                        const to = new Date(this.dateTo);
                        to.setHours(23, 59, 59, 999);
                        data = data.filter(item => new Date(item.created_at) <= to);
                    }

                    this.filteredData = data;
                    this.currentPage = 1;
                    this.updatePagedData();
                },

                resetFilter() {
                    this.searchQuery = '';
                    this.filterBagian = '';
                    this.filterKelas = '';
                    this.dateFrom = '';
                    this.dateTo = '';
                    this.filteredData = [...this.allData];
                    this.currentPage = 1;
                    this.updatePagedData();
                },

                onWindowScroll() {
                    const scrollY = window.scrollY;
                    this.showScrollTop = scrollY > 400;

                    const threshold = 100;
                    if (scrollY > threshold && !this.filterSticky) {
                        if (this.$refs.filterBar) this.filterBarHeight = this.$refs.filterBar.offsetHeight;
                        if (this.$refs.filterBarMobile) this.filterBarMobileHeight = this.$refs.filterBarMobile.offsetHeight;
                        this.filterSticky = true;
                        this.filterExpanded = false;
                    } else if (scrollY <= threshold && this.filterSticky) {
                        this.filterSticky = false;
                        this.filterExpanded = false;
                    }

                    // Infinite scroll
                    if (!this.loading && !this.loadingMore && this.hasMore()) {
                        if (scrollY + window.innerHeight >= document.documentElement.scrollHeight - 200) {
                            this.loadMore();
                        }
                    }
                },

                showDetail(msg) {
                    this.currentMessage = msg;
                    this.showModal = true;
                },

                getAnswer(msg, key) {
                    if (this.type === 'aspiration_event') {
                        const eventBuiltInKeys = ['message', 'kesan_pesan', 'bad_moment', 'perubahan_dari_event'];
                        if (eventBuiltInKeys.includes(key)) return msg[key] || '';
                    }
                    const builtInKeys = ['keluh_kesah', 'phone_number'];
                    if (builtInKeys.includes(key)) return msg[key] || '';
                    if (msg.custom_answers) {
                        const answers = typeof msg.custom_answers === 'string' ? JSON.parse(msg.custom_answers) : msg.custom_answers;
                        return answers[key] || '';
                    }
                    return '';
                },

                formatDate(date) {
                    return new Date(date).toLocaleString("id-ID", {
                        dateStyle: "long",
                        timeStyle: "short"
                    });
                },
            }
        }
    </script>

</body>
</html>
