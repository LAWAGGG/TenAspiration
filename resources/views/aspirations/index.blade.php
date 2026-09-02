<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Aspirasi</title>
    <link rel="icon" href="{{ asset('images/logo-mpk.jpg') }}" type="image/jpeg">
    @vite('resources/css/app.css')
    <script src="//unpkg.com/alpinejs" defer></script>
    <script>
        function notify() {
            return {
                show: false,
                message: '',
                type: 'error',
                timeout: null,
                showNotification(msg, type = 'error') {
                    this.message = msg;
                    this.type = type;
                    this.show = true;
                    clearTimeout(this.timeout);
                    this.timeout = setTimeout(() => { this.show = false; }, 4000);
                }
            };
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .card-fade-in { animation: fadeInUp 0.35s ease both; }
        
        /* Desktop filter backdrop */
        .filter-backdrop {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 39;
            height: 0;
            overflow: visible;
            pointer-events: none;
        }
        .filter-backdrop::after {
            content: '';
            display: block;
            background: rgba(255,255,255,0.5);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid #fecaca;
            box-shadow: 0 2px 8px 0 rgba(0,0,0,0.07);
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

        /* Hide scrollbar for mobile filter */
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body class="min-h-screen bg-gradient-to-br from-red-50 via-white to-red-100 py-10 px-4"
    x-data="aspirationApp()" @scroll.window="onWindowScroll()" x-init="init()">

    <div x-data="notify()" @notify.window="showNotification($event.detail.message, $event.detail.type)"
        class="fixed top-4 right-4 z-[9999] pointer-events-none">
        <div x-show="show" x-transition:enter="transform ease-out duration-300 transition" x-transition:enter-start="translate-x-full opacity-0" x-transition:enter-end="translate-x-0 opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            :class="type === 'error' ? 'bg-red-500' : 'bg-green-500'"
            class="text-white px-5 py-3 rounded-lg shadow-lg flex items-center gap-3 max-w-sm pointer-events-auto">
            <span x-text="message" class="flex-1 text-sm font-medium"></span>
            <button @click="show = false" class="text-white/80 hover:text-white font-bold text-lg leading-none">&times;</button>
        </div>
    </div>

    <!-- Header -->
    <div class="max-w-4xl mx-auto mb-8">
        <!-- Title + Actions -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 mb-5">
            <h1 class="text-2xl font-bold text-red-700 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Daftar Aspirasi
                <span class="text-gray-500 font-normal text-lg" x-text="'(' + total + ')'"></span>
            </h1>

            <div class="flex items-center gap-2 flex-wrap justify-center">
                <button @click="showQuestionModal = true"
                    class="px-3 py-1.5 bg-purple-600 text-white rounded-lg text-sm flex items-center gap-1.5 hover:bg-purple-700 transition font-medium shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Pertanyaan
                </button>

                <button @click="showShareModal = true"
                    class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-sm flex items-center gap-1.5 hover:bg-blue-700 transition font-medium shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                    </svg>
                    Share <span x-text="selectedIds.length > 0 ? '(' + selectedIds.length + ')' : ''" class="ml-0.5"></span>
                </button>

                <button @click="showBulkDeleteModal = true" x-show="selectedIds.length > 0"
                    class="px-3 py-1.5 bg-red-600 text-white rounded-lg text-sm flex items-center gap-1.5 hover:bg-red-700 transition font-medium shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Hapus <span x-text="selectedIds.length"></span>
                </button>

                <button @click="exportCsv" :disabled="total === 0"
                    :class="total === 0 ? 'bg-gray-300 cursor-not-allowed' : 'bg-green-500 hover:bg-green-600'"
                    class="px-3 py-1.5 text-white rounded-lg text-sm flex items-center gap-1.5 transition font-medium shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Export
                </button>
            </div>
        </div>

        <!-- Desktop: Backdrop blur (hanya muncul saat sticky) -->
        <div x-show="filterSticky" x-cloak class="filter-backdrop hidden md:block"
            :style="'& ::after { height: ' + (filterBarHeight + 16) + 'px }'">
        </div>

        <!-- Desktop: Spacer supaya konten tidak loncat saat sticky -->
        <div :style="filterSticky ? 'height:' + (filterBarHeight + 16) + 'px' : ''" class="transition-all duration-300 hidden md:block"></div>

        <!-- Mobile: Spacer untuk sticky filter -->
        <div :style="filterSticky ? 'height:' + filterBarHeight + 'px' : ''" class="transition-all duration-300 md:hidden"></div>

        <!-- FILTER SECTION -->
        <!-- Desktop Filter -->
        <div x-ref="filterBar"
            :style="filterSticky ? 'position:fixed; top:8px; left:0; right:0; z-index:40; width: calc(100% - 2rem); max-width: 56rem; margin: 0 auto; left: 50%; transform: translateX(-50%);' : ''"
            class="hidden md:block bg-white/70 backdrop-blur-sm rounded-xl border border-red-200 shadow-sm p-3 md:p-4">

            <div class="flex flex-col md:flex-row md:items-center gap-3">
                <!-- Search -->
                <div class="relative md:min-w-[220px]">
                    <input type="text" x-model="searchQuery" @input.debounce.800ms="onFilterChange()"
                        placeholder="Cari aspirasi..."
                        class="w-full pl-3 pr-3 py-2 rounded-lg bg-white border border-red-200 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-red-400 placeholder:text-gray-400">
                </div>

                <div class="hidden md:block w-px h-7 bg-red-200 shrink-0"></div>

                <!-- Bagian -->
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-500 font-medium shrink-0 w-12 md:w-auto">Bagian</span>
                    <select x-model="filterBagian" @change="onFilterChange()"
                        class="flex-1 md:flex-none px-3 py-2 rounded-lg bg-white border border-red-200 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-red-400">
                        <option value="">Semua</option>
                        <template x-for="q in bagianQuestions" :key="q.question_key">
                            <option :value="q.question_key" x-text="q.question_label"></option>
                        </template>
                    </select>
                </div>

                <!-- Kelas -->
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-500 font-medium shrink-0 w-12 md:w-auto">Kelas</span>
                    <select x-model="filterKelas" @change="onFilterChange()"
                        class="flex-1 md:flex-none px-3 py-2 rounded-lg bg-white border border-red-200 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-red-400">
                        <option value="">Semua</option>
                        <option value="X">Kelas X</option>
                        <option value="XI">Kelas XI</option>
                        <option value="XII">Kelas XII</option>
                    </select>
                </div>

                <div class="hidden md:block w-px h-7 bg-red-200 shrink-0"></div>

                <!-- Date Range -->
                <div class="flex items-center gap-2">
                    <div class="flex items-center gap-1.5">
                        <input type="date" x-model="dateFrom" @change="onFilterChange()" title="Dari tanggal"
                            class="px-2 py-2 rounded-lg bg-white border border-red-200 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-red-400 w-[115px]">
                        <span class="text-gray-400 text-xs">—</span>
                        <input type="date" x-model="dateTo" @change="onFilterChange()" title="Sampai tanggal"
                            class="px-2 py-2 rounded-lg bg-white border border-red-200 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-red-400 w-[115px]">
                    </div>
                </div>

                <!-- Reset -->
                <button @click="resetFilters()"
                    x-show="searchQuery || filterBagian || filterKelas || dateFrom || dateTo"
                    class="shrink-0 text-xs text-red-500 hover:text-red-700 font-medium transition px-2 py-1.5 rounded-lg hover:bg-red-50">
                    ↺ Reset
                </button>
            </div>
        </div>

        <!-- Mobile Filter -->
        <div :class="filterSticky ? 'filter-bar-sticky-mobile' : 'bg-white/70 backdrop-blur-sm rounded-xl border border-red-200 shadow-sm p-3'"
            class="md:hidden" x-ref="filterBarMobile">

            <!-- Collapsed bar (sticky) -->
            <div x-show="filterSticky" class="flex items-center justify-between gap-2">
                <div class="flex items-center gap-2 min-w-0 flex-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z" />
                    </svg>
                    <span class="text-sm font-medium text-gray-700">
                        Filter
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

            <!-- Expanded filter content (mobile) -->
            <div :class="filterSticky ? (filterExpanded ? 'mt-3 p-3 border-t border-red-100' : 'hidden') : ''"
                class="flex flex-col gap-3">
                <!-- Search -->
                <div class="relative">
                    <input type="text" x-model="searchQuery" @input.debounce.800ms="onFilterChange()"
                        placeholder="Cari aspirasi..."
                        class="w-full pl-3 pr-3 py-2 rounded-lg bg-white border border-red-200 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-red-400 placeholder:text-gray-400">
                </div>

                <!-- Bagian & Kelas row -->
                <div class="flex gap-3">
                    <div class="flex-1">
                        <label class="text-xs text-gray-500 font-medium block mb-1">Bagian</label>
                        <select x-model="filterBagian" @change="onFilterChange()"
                            class="w-full px-3 py-2 rounded-lg bg-white border border-red-200 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-red-400">
                            <option value="">Semua</option>
                            <template x-for="q in bagianQuestions" :key="q.question_key">
                                <option :value="q.question_key" x-text="q.question_label"></option>
                            </template>
                        </select>
                    </div>
                    <div class="flex-1">
                        <label class="text-xs text-gray-500 font-medium block mb-1">Kelas</label>
                        <select x-model="filterKelas" @change="onFilterChange()"
                            class="w-full px-3 py-2 rounded-lg bg-white border border-red-200 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-red-400">
                            <option value="">Semua</option>
                            <option value="X">Kelas X</option>
                            <option value="XI">Kelas XI</option>
                            <option value="XII">Kelas XII</option>
                        </select>
                    </div>
                </div>

                <!-- Date Range -->
                <div class="flex items-center gap-2">
                    <label class="text-xs text-gray-500 font-medium shrink-0">Tanggal</label>
                    <div class="flex items-center gap-1.5 flex-1">
                        <input type="date" x-model="dateFrom" @change="onFilterChange()" title="Dari tanggal"
                            class="flex-1 px-2 py-2 rounded-lg bg-white border border-red-200 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-red-400 min-w-0">
                        <span class="text-gray-400 text-xs">—</span>
                        <input type="date" x-model="dateTo" @change="onFilterChange()" title="Sampai tanggal"
                            class="flex-1 px-2 py-2 rounded-lg bg-white border border-red-200 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-red-400 min-w-0">
                    </div>
                </div>

                <!-- Reset -->
                <button @click="resetFilters()"
                    x-show="searchQuery || filterBagian || filterKelas || dateFrom || dateTo"
                    class="text-xs text-red-500 hover:text-red-700 font-medium transition px-2 py-1.5 rounded-lg hover:bg-red-50 text-left">
                    ↺ Reset Filter
                </button>
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

        <!-- Data Cards -->
        <template x-for="asp in aspirations" :key="asp.id">
            <div class="card-fade-in bg-white rounded-xl flex flex-col sm:flex-row justify-between shadow-md p-5 border-l-8 hover:shadow-lg transition w-full sm:w-[48%] lg:w-[31%]"
                :class="[asp.to === 'MPK' ? 'border-red-600' : asp.to === 'OSIS' ? 'border-blue-500' :
                    'border-gray-400', selectedIds.includes(asp.id) ? 'ring-2 ring-red-400' : ''
                ]">
                <div class="flex items-start gap-3 flex-1 min-w-0">
                    <!-- Checkbox -->
                    <input type="checkbox" :checked="selectedIds.includes(asp.id)" @change="toggleSelect(asp.id)"
                        class="mt-1 h-4 w-4 rounded border-gray-300 text-red-600 focus:ring-red-500 cursor-pointer shrink-0">

                    <div class="flex-1 min-w-0">
                        <h3 class="text-base font-semibold flex items-center gap-1.5"
                            :class="asp.to === 'MPK' ? 'text-red-700' : asp.to === 'OSIS' ? 'text-blue-700' : 'text-gray-800'">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                            </svg>
                            <span x-html="highlightText(getQuestionLabel(asp.to), searchQuery)"></span>
                        </h3>
                        <p class="text-gray-700 mt-1.5 break-words text-sm" x-html="highlightText(asp.message, searchQuery)"></p>
                        <p class="text-gray-500 mt-1 text-xs" x-text="asp.kelas"></p>
                        <p class="text-xs text-gray-400 mt-2" x-text="asp.created_at ? formatDate(asp.created_at) : ''"></p>
                    </div>
                </div>
            </div>
        </template>

        <!-- Loading More -->
        <div x-show="loadingMore" class="w-full text-center py-6">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-600 mx-auto"></div>
            <p class="mt-2 text-gray-500 text-sm">Memuat data lainnya...</p>
        </div>

        <!-- Sentinel for infinite scroll -->
        <div x-ref="sentinel" class="w-full h-4"></div>

        <!-- Empty State -->
        <div x-show="!loading && aspirations.length === 0"
            class="bg-white rounded-xl w-full shadow-md p-8 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="text-xl font-semibold text-gray-700 mb-2"
                x-text="total === 0 && !searchQuery && !filterBagian && !filterKelas && !dateFrom && !dateTo ? 'Belum ada aspirasi' : 'Tidak ada hasil filter'">
            </h3>
            <p class="text-gray-500"
                x-text="total === 0 && !searchQuery && !filterBagian && !filterKelas && !dateFrom && !dateTo ? 'Belum ada aspirasi yang dikirim.' : 'Tidak ada aspirasi yang sesuai dengan filter yang dipilih.'">
            </p>
        </div>
    </div>

    <!-- Delete Modal -->
    <div x-show="showDeleteModal"
        class="fixed inset-0 p-5 bg-opacity-50 flex justify-center items-center bg-black z-50 backdrop-blur-sm"
        x-cloak>
        <div class="bg-white rounded-xl shadow-lg p-6 w-96 text-center">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Konfirmasi Hapus</h2>
            <p class="text-gray-600 mb-6">Apakah kamu yakin ingin menghapus aspirasi ini?</p>
            <div class="flex justify-center gap-4">
                <button @click="showDeleteModal=false"
                    class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400"
                    :disabled="deleteLoading">Batal</button>

                <button @click="handleDelete"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 flex items-center justify-center gap-2"
                    :disabled="deleteLoading">
                    <template x-if="deleteLoading">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                            </path>
                        </svg>
                    </template>
                    <span x-text="deleteLoading ? 'Menghapus...' : 'Ya, Hapus'"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Bulk Delete Modal -->
    <div x-show="showBulkDeleteModal"
        class="fixed inset-0 p-5 bg-opacity-50 flex justify-center items-center bg-black z-50 backdrop-blur-sm"
        x-cloak>
        <div class="bg-white rounded-xl shadow-lg p-6 w-96 text-center">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Konfirmasi Hapus Massal</h2>
            <p class="text-gray-600 mb-6">Apakah kamu yakin ingin menghapus <span class="font-bold text-red-600"
                    x-text="selectedIds.length"></span> aspirasi?</p>
            <div class="flex justify-center gap-4">
                <button @click="showBulkDeleteModal=false"
                    class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400"
                    :disabled="bulkLoading">Batal</button>
                <button @click="handleBulkDelete"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 flex items-center justify-center gap-2"
                    :disabled="bulkLoading">
                    <template x-if="bulkLoading">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                            </path>
                        </svg>
                    </template>
                    <span x-text="bulkLoading ? 'Menghapus...' : 'Ya, Hapus Semua'"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Share Modal -->
    <div x-show="showShareModal" x-cloak
        class="fixed inset-0 p-5 bg-opacity-50 flex justify-center items-center bg-black z-50 backdrop-blur-sm"
        @click.self="showShareModal = false">
        <div class="bg-white rounded-xl shadow-lg p-6 w-96">
            <h2 class="text-lg font-semibold text-gray-800 mb-2 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                </svg>
                Share
            </h2>
            <p class="text-gray-500 text-sm mb-4">Buat link untuk membagikan <span class="font-bold text-blue-600"
                    x-text="selectedIds.length > 0 ? selectedIds.length + ' aspirasi yang dipilih' : 'semua aspirasi'"></span>.</p>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Judul (opsional)</label>
                <input type="text" x-model="shareTitle" placeholder="Contoh: Aspirasi OSIS 2026"
                    class="w-full px-3 py-2 rounded-lg bg-white border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 placeholder:text-gray-400">
            </div>

            <!-- Generated Link -->
            <div x-show="shareUrl" class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Link Share</label>
                <div class="flex items-center gap-2">
                    <input type="text" :value="shareUrl" readonly
                        class="flex-1 px-3 py-2 rounded-lg bg-gray-50 border border-gray-300 text-sm text-gray-600 focus:outline-none">
                    <button @click="copyShareLink()"
                        class="px-3 py-2 rounded-lg text-sm font-medium transition shadow-sm"
                        :class="copied ? 'bg-green-500 text-white' : 'bg-blue-600 text-white hover:bg-blue-700'">
                        <svg x-show="!copied" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        <svg x-show="copied" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <button @click="showShareModal = false; shareUrl = ''; shareTitle = ''; copied = false;"
                    class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 text-sm font-medium">
                    Tutup
                </button>
                <button @click="handleShare()" :disabled="shareLoading"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium flex items-center justify-center gap-2">
                    <template x-if="shareLoading">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                    </template>
                    <span x-text="shareLoading ? 'Membuat...' : (shareUrl ? 'Buat Link Baru' : 'Buat Link')"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Question Management Modal -->
    <div x-show="showQuestionModal" x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        @click.self="showQuestionModal = false">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showQuestionModal" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm transition-opacity"></div>

            <div
                class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 sm:px-6 pt-6 pb-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg sm:text-xl font-bold text-gray-900">Kelola Pertanyaan Audiensi</h3>
                        <button @click="showQuestionModal = false" class="text-gray-400 hover:text-black">
                            <svg class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <p class="text-sm text-gray-500 mb-4">Ubah pertanyaan untuk form audiensi. Perubahan akan diterapkan secara global.</p>

                    <form method="POST" action="{{ route('form_questions.update', 'audiensi') }}">
                        @csrf
                        @method('PUT')
                        <div class="space-y-4 mb-4 max-h-[60vh] overflow-y-auto">
                            <template x-for="(q, index) in editQuestions" :key="index">
                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-medium text-gray-500" x-text="'Pertanyaan ' + (index + 1)"></span>
                                        <button type="button" @click="removeQuestion(index)"
                                            class="text-red-500 hover:text-red-700 p-1" title="Hapus pertanyaan">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="space-y-2">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Label Pertanyaan</label>
                                            <input type="text" x-model="q.question_label" :name="'questions[' + index + '][question_label]'" required
                                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-purple-400">
                                        </div>
                                        <div>
                                            <input type="hidden" :name="'questions[' + index + '][question_key]'" :value="q.question_key">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Tipe Soal</label>
                                            <select x-model="q.question_type"
                                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-purple-400">
                                                <option value="essay">Essay (Uraian)</option>
                                                <option value="pilihan_ganda">Pilihan Ganda</option>
                                                <option value="checkbox">Checkbox (Multiple Choice)</option>
                                            </select>
                                        </div>
                                        <div x-show="q.question_type === 'pilihan_ganda'" class="space-y-2">
                                            <div class="flex items-center justify-between">
                                                <label class="block text-xs font-medium text-gray-600">Opsi Pilihan</label>
                                                <button type="button" @click="addOption(index)"
                                                    class="text-xs text-purple-600 hover:text-purple-800 font-medium">+ Tambah Opsi</button>
                                            </div>
                                            <template x-for="(opt, oIndex) in getOptions(q)" :key="oIndex">
                                                <div class="flex items-center gap-2">
                                                    <input type="radio" :name="'questions[' + index + '][correct_answer]'" :value="opt"
                                                        class="h-4 w-4 text-purple-600 focus:ring-purple-500" title="Tandai jawaban benar"
                                                        :disabled="opt === ''">
                                                    <input type="text" :value="opt"
                                                        @input="q.question_options.options[oIndex] = $event.target.value"
                                                        :placeholder="'Opsi ' + (oIndex + 1)"
                                                        class="flex-1 border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-purple-400">
                                                    <button type="button" @click="removeOption(index, oIndex)"
                                                        class="text-red-500 hover:text-red-700 p-1" title="Hapus opsi"
                                                        x-show="getOptions(q).length > 2">
                                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </template>
                                            <p class="text-xs text-gray-400 mt-1">Centang radio button untuk tandai jawaban benar</p>
                                        </div>
                                        <div x-show="q.question_type === 'checkbox'" class="space-y-2">
                                            <div class="flex items-center justify-between">
                                                <label class="block text-xs font-medium text-gray-600">Opsi Pilihan</label>
                                                <button type="button" @click="addOption(index)"
                                                    class="text-xs text-purple-600 hover:text-purple-800 font-medium">+ Tambah Opsi</button>
                                            </div>
                                            <template x-for="(opt, oIndex) in getOptions(q)" :key="oIndex">
                                                <div class="flex items-center gap-2">
                                                    <input type="checkbox" :name="'questions[' + index + '][correct_answers][]'" :value="opt"
                                                        class="h-4 w-4 text-purple-600 rounded focus:ring-purple-500" title="Tandai sebagai jawaban benar"
                                                        x-model="((q.question_options.correct_answers || []).includes(opt))"
                                                        :disabled="opt === ''">
                                                    <input type="text" :value="opt"
                                                        @input="q.question_options.options[oIndex] = $event.target.value; if(!q.question_options.correct_answers.includes($event.target.value)) q.question_options.correct_answers.push($event.target.value)"
                                                        :placeholder="'Opsi ' + (oIndex + 1)"
                                                        class="flex-1 border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-purple-400">
                                                    <button type="button" @click="removeOption(index, oIndex)"
                                                        class="text-red-500 hover:text-red-700 p-1" title="Hapus opsi"
                                                        x-show="getOptions(q).length > 2">
                                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </template>
                                            <p class="text-xs text-gray-400 mt-1">Centang checkbox untuk tandai jawaban benar (bisa lebih dari satu)</p>
                                        </div>
                                        <!-- Lainnya toggle -->
                                        <div x-show="q.question_type === 'pilihan_ganda' || q.question_type === 'checkbox'" class="flex items-center gap-2 p-2 rounded-lg bg-purple-50 border border-purple-200">
                                            <input type="checkbox" x-model="q.question_options.allow_other"
                                                class="h-4 w-4 rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                            <label class="text-xs font-medium text-gray-700">Izinkan Lainnya</label>
                                            <span class="text-xs text-gray-500">— tampilkan opsi Lainnya + input teks</span>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Placeholder</label>
                                            <input type="text" x-model="q.placeholder" :name="'questions[' + index + '][placeholder]'"
                                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-purple-400">
                                        </div>
<div class="flex items-center gap-2">
                                              <input type="hidden" :name="'questions[' + index + '][is_required]'" :value="q.is_required ? '1' : '0'">
                                              <input type="checkbox" x-model="q.is_required"
                                                  class="h-4 w-4 rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                              <label class="text-xs font-medium text-gray-600">Wajib diisi</label>
                                          </div>
                                        <input type="hidden" :name="'questions[' + index + '][question_type]'" :value="q.question_type">
                                        <input type="hidden" :name="'questions[' + index + '][question_options]'"
                                            :value="JSON.stringify(q.question_options)">
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div class="flex items-center gap-2 mb-4">
                            <button type="button" @click="addQuestion()"
                                class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200 transition font-medium border border-gray-300">
                                + Tambah Pertanyaan
                            </button>
                            <button type="button" @click="resetQuestions()"
                                class="px-3 py-1.5 bg-orange-50 text-orange-600 rounded-lg text-sm hover:bg-orange-100 transition font-medium border border-orange-200">
                                ↺ Reset ke Default
                            </button>
                        </div>

                        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                            <button type="button" @click="showQuestionModal = false"
                                class="px-3 py-2 sm:px-4 sm:py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition duration-200 font-medium text-sm sm:text-base">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-4 py-2 sm:px-6 sm:py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition duration-200 font-medium text-sm sm:text-base">
                                Simpan Pertanyaan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Back button & Scroll to Top -->
    <div class="fixed right-5 bottom-5 flex flex-col gap-2 z-30">
        <button x-show="showScrollTop" x-cloak @click="window.scrollTo({top:0,behavior:'smooth'})"
            class="bg-white border flex items-center gap-2 border-red-200 text-red-500 p-3 rounded-xl shadow-lg hover:bg-red-50 transition text-center"
            title="Kembali ke atas">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
            </svg>
            <span class="hidden sm:inline">Ke atas</span>
        </button>
        <a href="{{ route('dashboard') }}"
            class="bg-red-500 p-3 rounded-xl text-white shadow-lg hover:bg-red-600 transition flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span class="hidden sm:inline">Kembali</span>
        </a>
    </div>

    <script>
        const defaultQuestionsAudiensi = @json($defaultQuestions);

        function aspirationApp() {
            return {
                aspirations: [],
                bagianQuestions: @json($questions),
                currentPage: 1,
                lastPage: 1,
                total: 0,
                loading: true,
                loadingMore: false,
                deleteLoading: false,
                bulkLoading: false,
                searchQuery: '',
                filterBagian: '',
                filterKelas: '',
                dateFrom: '',
                dateTo: '',
                selectedIds: [],
                showDeleteModal: false,
                showBulkDeleteModal: false,
                deleteId: null,
                showShareModal: false,
                shareLoading: false,
                shareUrl: '',
                shareTitle: '',
                copied: false,
                showQuestionModal: false,
                filterSticky: false,
                filterExpanded: false,
                filterBarHeight: 0,
                showScrollTop: false,
                editQuestions: @json($questions).map(q => ({
                    question_key: q.question_key,
                    question_label: q.question_label,
                    question_type: q.question_type || 'essay',
                    question_options: q.question_options && typeof q.question_options === 'string' ? JSON.parse(q.question_options) : (q.question_options || {}),
                    placeholder: q.placeholder || '',
                    is_required: !!q.is_required
                })),

                addQuestion() {
                    this.editQuestions.push({
                        question_key: 'custom_' + Date.now(),
                        question_label: 'Pertanyaan Baru',
                        question_type: 'essay',
                        question_options: {},
                        placeholder: '',
                        is_required: true
                    });
                },

                removeQuestion(index) {
                    if (this.editQuestions.length > 1) {
                        this.editQuestions.splice(index, 1);
                    }
                },

                addOption(qIndex) {
                    const q = this.editQuestions[qIndex];
                    if (!q.question_options.options) q.question_options.options = [];
                    q.question_options.options.push('');
                },

                removeOption(qIndex, oIndex) {
                    const q = this.editQuestions[qIndex];
                    if (q.question_options.options && q.question_options.options.length > 2) {
                        q.question_options.options.splice(oIndex, 1);
                    }
                },

                getOptions(q) {
                    return q.question_options?.options || [''];
                },

                resetQuestions() {
                    if (confirm('Reset semua pertanyaan ke default?')) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '{{ route('form_questions.reset', 'audiensi') }}';
                        const csrf = document.createElement('input');
                        csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = '{{ csrf_token() }}';
                        form.appendChild(csrf);
                        document.body.appendChild(form);
                        form.submit();
                    }
                },

                highlightText(text, query) {
                    if (!query || !text) return text;
                    const escapedText = text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/'/g, '&#x27;').replace(/"/g, '&quot;');
                    const escapedQuery = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                    const regex = new RegExp(`(${escapedQuery})`, 'gi');
                    return escapedText.replace(regex, '<mark class="bg-yellow-200 px-0.5 rounded">$1</mark>');
                },

                getQuestionLabel(key) {
                    if (!key) return 'Aspirasi';
                    const questionsMap = @json($questions);
                    const found = questionsMap.find(q => q.question_key === key);
                    if (found) return found.question_label;
                    // Key tidak ditemukan di pertanyaan aktif — pertanyaan sudah dihapus
                    // Tampilkan key apa adanya (sudah human-readable) + label dihapus
                    return key + ' (dihapus)';
                },

                async fetchAspirations(reset = true) {
                    if (reset) {
                        this.loading = true;
                        this.currentPage = 1;
                        this.aspirations = [];
                    } else {
                        this.loadingMore = true;
                    }

                    const params = new URLSearchParams();
                    params.set('page', this.currentPage);
                    if (this.searchQuery) params.set('search', this.searchQuery);
                    if (this.filterBagian) params.set('filterBagian', this.filterBagian);
                    if (this.filterKelas) params.set('filterKelas', this.filterKelas);
                    if (this.dateFrom) params.set('dateFrom', this.dateFrom);
                    if (this.dateTo) params.set('dateTo', this.dateTo);

                    try {
                        const res = await fetch(`/api/aspirations?${params}`);
                        const data = await res.json();
                        if (reset) {
                            this.aspirations = data.data;
                        } else {
                            this.aspirations = [...this.aspirations, ...data.data];
                        }
                        this.currentPage = data.current_page;
                        this.lastPage = data.last_page;
                        this.total = data.total;
                    } catch (error) {
                        console.error('Gagal memuat data:', error);
                    } finally {
                        this.loading = false;
                        this.loadingMore = false;
                        this.$nextTick(() => this.checkAndLoadMore());
                    }
                },

                onFilterChange() {
                    if (this.loading || this.loadingMore) return;
                    this.fetchAspirations(true);
                },

                loadMore() {
                    if (this.loading || this.loadingMore || this.currentPage >= this.lastPage) return;
                    this.currentPage++;
                    this.fetchAspirations(false);
                },

                checkAndLoadMore() {
                    if (this.loading || this.loadingMore || this.currentPage >= this.lastPage) return;
                    if (window.innerHeight + window.scrollY >= document.documentElement.scrollHeight - 200) {
                        this.loadMore();
                    }
                },

                resetFilters() {
                    this.searchQuery = '';
                    this.filterBagian = '';
                    this.filterKelas = '';
                    this.dateFrom = '';
                    this.dateTo = '';
                    this.fetchAspirations(true);
                },

                toggleSelect(id) {
                    const idx = this.selectedIds.indexOf(id);
                    if (idx === -1) {
                        this.selectedIds.push(id);
                    } else {
                        this.selectedIds.splice(idx, 1);
                    }
                },

                toggleSelectAll() {
                    const ids = this.aspirations.map(a => a.id);
                    const allSelected = ids.every(id => this.selectedIds.includes(id));
                    if (allSelected) {
                        this.selectedIds = this.selectedIds.filter(id => !ids.includes(id));
                    } else {
                        ids.forEach(id => {
                            if (!this.selectedIds.includes(id)) this.selectedIds.push(id);
                        });
                    }
                },

                formatDate(date) {
                    return new Date(date).toLocaleString("id-ID", {
                        dateStyle: "long",
                        timeStyle: "short"
                    });
                },

                formatDateShort(date) {
                    const d = new Date(date);
                    return d.toISOString().split('T')[0];
                },

                confirmDelete(id) {
                    this.deleteId = id;
                    this.showDeleteModal = true;
                },

                async handleDelete() {
                    if (!this.deleteId) return;
                    this.deleteLoading = true;
                    try {
                        await fetch(`/aspirations/${this.deleteId}`, {
                            method: 'DELETE',
                            headers: {
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            }
                        });
                        this.selectedIds = this.selectedIds.filter(id => id !== this.deleteId);
                        await this.fetchAspirations(true);
                    } catch (error) {
                        console.error('Gagal menghapus:', error);
                    } finally {
                        this.deleteLoading = false;
                        this.deleteId = null;
                        this.showDeleteModal = false;
                    }
                },

                async handleBulkDelete() {
                    if (this.selectedIds.length === 0) return;
                    this.bulkLoading = true;
                    try {
                        const response = await fetch("{{ route('aspirations.bulk-destroy') }}", {
                            method: 'POST',
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            body: JSON.stringify({
                                ids: this.selectedIds
                            })
                        });
                        const result = await response.json();
                        if (response.ok) {
                            this.selectedIds = [];
                            await this.fetchAspirations(true);
                        } else {
                            window.dispatchEvent(new CustomEvent('notify', { detail: { message: result.message || 'Gagal menghapus aspirasi', type: 'error' } }));
                        }
                    } catch (error) {
                        console.error('Gagal menghapus massal:', error);
                        window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Terjadi kesalahan saat menghapus', type: 'error' } }));
                    } finally {
                        this.bulkLoading = false;
                        this.showBulkDeleteModal = false;
                    }
                },

                exportCsv() {
                    const params = new URLSearchParams();
                    if (this.filterBagian) params.set('to', this.filterBagian);
                    if (this.filterKelas) params.set('kelas', this.filterKelas);
                    if (this.searchQuery) params.set('search', this.searchQuery);
                    if (this.dateFrom) params.set('date_from', this.dateFrom);
                    if (this.dateTo) params.set('date_to', this.dateTo);
                    const qs = params.toString();
                    window.location.href = "{{ route('aspirations.export') }}" + (qs ? '?' + qs : '');
                },

                async handleShare() {
                    if (this.shareLoading) return;
                    this.shareLoading = true;
                    try {
                        const payload = {
                            type: 'aspiration',
                            selected_ids: this.selectedIds.length > 0 ? this.selectedIds : null,
                            title: this.shareTitle || null,
                        };
                        if (this.selectedIds.length === 0) {
                            payload.filters = {};
                            if (this.searchQuery) payload.filters.search = this.searchQuery;
                            if (this.filterBagian) payload.filters.filterBagian = this.filterBagian;
                            if (this.filterKelas) payload.filters.filterKelas = this.filterKelas;
                            if (this.dateFrom) payload.filters.dateFrom = this.dateFrom;
                            if (this.dateTo) payload.filters.dateTo = this.dateTo;
                        }
                        const res = await fetch("{{ route('api.share') }}", {
                            method: 'POST',
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            body: JSON.stringify(payload)
                        });
                        const result = await res.json();
                        if (res.ok) {
                            this.shareUrl = result.url;
                        } else {
                            window.dispatchEvent(new CustomEvent('notify', { detail: { message: result.message || 'Gagal membuat link share', type: 'error' } }));
                        }
                    } catch (error) {
                        console.error('Gagal share:', error);
                        window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Terjadi kesalahan saat membuat link share', type: 'error' } }));
                    } finally {
                        this.shareLoading = false;
                    }
                },

                copyShareLink() {
                    navigator.clipboard.writeText(this.shareUrl).then(() => {
                        this.copied = true;
                        setTimeout(() => { this.copied = false; }, 2000);
                    });
                },

                init() {
                    this.fetchAspirations(true);
                    this.$nextTick(() => {
                        const filterBar = this.$refs.filterBar;
                        const filterBarMobile = this.$refs.filterBarMobile;
                        if (filterBar) {
                            this.filterBarHeight = filterBar.offsetHeight;
                        } else if (filterBarMobile) {
                            this.filterBarHeight = filterBarMobile.offsetHeight;
                        }
                    });
                },

                onWindowScroll() {
                    const scrollY = window.scrollY;
                    this.showScrollTop = scrollY > 400;
                    const threshold = 100;
                    if (scrollY > threshold && !this.filterSticky) {
                        const filterBar = this.$refs.filterBar;
                        const filterBarMobile = this.$refs.filterBarMobile;
                        if (filterBar) {
                            this.filterBarHeight = filterBar.offsetHeight;
                        } else if (filterBarMobile) {
                            this.filterBarHeight = filterBarMobile.offsetHeight;
                        }
                        this.filterSticky = true;
                        this.filterExpanded = false;
                    } else if (scrollY <= threshold && this.filterSticky) {
                        this.filterSticky = false;
                        this.filterExpanded = false;
                    }
                    if (!this.loading && !this.loadingMore && this.currentPage < this.lastPage) {
                        if (scrollY + window.innerHeight >= document.documentElement.scrollHeight - 200) {
                            this.loadMore();
                        }
                    }
                },

                setupScroll() {
                    // handled via @scroll.window in template
                }
            }
        }
    </script>

</body>

</html>