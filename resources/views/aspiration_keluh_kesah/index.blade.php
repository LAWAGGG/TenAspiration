<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Keluh Kesah</title>
    <link rel="icon" href="{{ asset('images/logo-mpk.jpg') }}" type="image/jpeg">
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
        .modal-overlay { background-color: rgba(0,0,0,0.5); }
        .modal-content { max-height: 90vh; overflow-y: auto; }
        .card-container { display: flex; flex-direction: column; height: 100%; }
        .card-content { flex: 1; }
        .card-footer { margin-top: auto; }
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
    x-data="keluhKesahApp()" @scroll.window="onWindowScroll()" x-init="init()">

    <!-- Header -->
    <div class="max-w-4xl mx-auto mb-8">
        <!-- Title + Actions -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 mb-5">
            <h1 class="text-2xl font-bold text-red-700 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Daftar Keluh Kesah
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
                <button @click="showTargetEmailModal = true; fetchTargetEmails()"
                    class="px-3 py-1.5 bg-teal-600 text-white rounded-lg text-sm flex items-center gap-1.5 hover:bg-teal-700 transition font-medium shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    Email Tujuan
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
                        placeholder="Cari keluh kesah..."
                        class="w-full pl-3 pr-3 py-2 rounded-lg bg-white border border-red-200 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-red-400 placeholder:text-gray-400">
                </div>

                <div class="hidden md:block w-px h-7 bg-red-200 shrink-0"></div>

                <!-- Date Range -->
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-500 font-medium shrink-0">Tanggal</span>
                    <div class="flex items-center gap-1.5">
                        <input type="date" x-model="dateFrom" @change="onFilterChange()" title="Dari tanggal"
                            class="px-2 py-2 rounded-lg bg-white border border-red-200 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-red-400 w-[130px]">
                        <span class="text-gray-400 text-xs">—</span>
                        <input type="date" x-model="dateTo" @change="onFilterChange()" title="Sampai tanggal"
                            class="px-2 py-2 rounded-lg bg-white border border-red-200 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-red-400 w-[130px]">
                    </div>
                </div>

                <!-- Reset -->
                <button @click="resetFilters()"
                    x-show="searchQuery || dateFrom || dateTo"
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
                        <template x-if="searchQuery || dateFrom || dateTo">
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
                        placeholder="Cari keluh kesah..."
                        class="w-full pl-3 pr-3 py-2 rounded-lg bg-white border border-red-200 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-red-400 placeholder:text-gray-400">
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
                    x-show="searchQuery || dateFrom || dateTo"
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
            <p class="mt-4 text-gray-600">Memuat data keluh kesah...</p>
        </div>

        <!-- Data Cards -->
        <template x-for="msg in messages" :key="msg.id">
            <div class="card-fade-in bg-white rounded-xl shadow-md hover:shadow-lg transition w-full sm:w-[48%] lg:w-[31%] card-container cursor-pointer"
                @click="showDetail(msg.id)" x-data="{
                    isContentOverflowing: false,
                    checkOverflow() {
                        this.$nextTick(() => {
                            const element = this.$refs['desc' + msg.id];
                            if (element) {
                                this.isContentOverflowing = element.scrollHeight > element.clientHeight;
                            }
                        });
                    }
                }" x-init="checkOverflow()">
                <div class="p-5 card-content">
                    <div class="flex items-start gap-3 mb-4">
                        <!-- Checkbox -->
                        <input type="checkbox" :checked="selectedIds.includes(msg.id)" @change="toggleSelect(msg.id)"
                            @click.stop
                            class="mt-1 h-4 w-4 rounded border-gray-300 text-red-600 focus:ring-red-500 cursor-pointer shrink-0">

                        <div class="flex-1 min-w-0">
                            <!-- Question Label Badge -->
                            <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full mb-3 bg-red-100 text-red-700"
                                x-text="editQuestions.length > 0 ? editQuestions[0].question_label : 'Keluh Kesah'">
                            </span>

                            <!-- Main Content Preview -->
                            <template x-for="(q, idx) in editQuestions.slice(0, 2)" :key="q.question_key">
                                <div class="mb-2">
                                    <div x-show="idx > 0" class="text-xs text-gray-400 font-medium mb-1" x-text="q.question_label"></div>
                                    <div class="relative" :class="idx > 0 ? '' : ''">
                                        <p class="text-gray-700 text-sm line-clamp-3"
                                            :ref="idx === 0 ? 'desc' + msg.id : ''"
                                            x-html="highlightText(getAnswer(msg, q.question_key), searchQuery)">
                                        </p>
                                    </div>
                                </div>
                            </template>

                            <!-- More questions indicator -->
                            <div x-show="editQuestions.length > 2" class="text-xs text-gray-400 mt-1">
                                <span x-text="'+' + (editQuestions.length - 2) + ' pertanyaan lagi'"></span>
                            </div>

                            <!-- Gradient overlay untuk menunjukkan ada konten lebih lanjut -->
                            <template x-if="editQuestions.length > 0">
                                <div x-show="isContentOverflowing"
                                    class="relative h-0">
                                    <div class="absolute bottom-0 left-0 right-0 h-6 bg-gradient-to-t from-white to-transparent"></div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Read More Button (hanya muncul jika konten dipotong) -->
                    <div x-show="isContentOverflowing" class="mt-2">
                        <button @click.stop="showDetail(msg.id)"
                            class="text-red-600 text-sm font-medium hover:text-red-800 transition flex items-center gap-1">
                            <span>Baca selengkapnya</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Footer yang selalu di bawah -->
                <div class="pt-4 border-t border-gray-100 card-footer p-5">
                    <div class="flex justify-between items-center">
                        <p class="text-xs text-gray-500" x-text="formatDate(msg.created_at)"></p>
                        <!-- Topic indicator -->
                        <div class="text-xs text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                        </div>
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
        <div x-show="!loading && messages.length === 0" class="bg-white rounded-xl w-full shadow-md p-8 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="text-xl font-semibold text-gray-700 mb-2"
                x-text="total === 0 && !searchQuery && !dateFrom && !dateTo ? 'Belum ada keluh kesah' : 'Tidak ada hasil pencarian'"></h3>
            <p class="text-gray-500"
                x-text="total === 0 && !searchQuery && !dateFrom && !dateTo ? 'Belum ada keluh kesah yang dikirim.' : 'Tidak ada keluh kesah yang sesuai dengan pencarian yang dipilih.'">
            </p>
        </div>
    </div>

    <!-- Detail Modal -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 modal-overlay"
        @click.self="showModal = false">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl modal-content flex flex-col" @click.stop>
            <!-- Modal Header -->
            <div
                class="sticky top-0 bg-white p-6 border-b border-gray-200 rounded-t-xl flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Detail Keluh Kesah</h2>
                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full mt-2 bg-red-100 text-red-700"
                        x-text="editQuestions.length > 0 ? editQuestions[0].question_label : 'Keluh Kesah'">
                    </span>
                </div>
                <button @click="showModal = false" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6" x-show="!detailLoading && currentMessage">
                <template x-for="(q, idx) in editQuestions" :key="q.question_key">
                    <div class="mb-6">
                        <div class="p-4 bg-red-50 rounded-lg">
                            <h3 class="font-semibold text-gray-700 mb-2" x-text="q.question_label"></h3>
                            <p class="text-gray-800 whitespace-pre-wrap" x-html="highlightText(getAnswer(currentMessage, q.question_key), searchQuery)"></p>
                        </div>
                    </div>
                </template>

                <!-- Metadata -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span x-text="currentMessage ? formatDate(currentMessage.created_at) : ''"></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span x-text="currentMessage ? formatTime(currentMessage.created_at) : ''"></span>
                    </div>
                </div>
            </div>

            <!-- Loading State Modal -->
            <div x-show="detailLoading" class="flex-1 flex flex-col items-center justify-center p-12 text-center">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-red-600 mx-auto"></div>
                <p class="mt-4 text-gray-600">Memuat detail keluh kesah...</p>
            </div>

            <!-- Modal Footer -->
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

    <!-- Bulk Delete Modal -->
    <div x-show="showBulkDeleteModal" x-cloak
        class="fixed inset-0 p-5 bg-opacity-50 flex justify-center items-center bg-black z-50 backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-lg p-6 w-96 text-center">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Konfirmasi Hapus Massal</h2>
            <p class="text-gray-600 mb-6">Apakah kamu yakin ingin menghapus <span class="font-bold text-red-600"
                    x-text="selectedIds.length"></span> keluh kesah?</p>
            <div class="flex justify-center gap-4">
                <button @click="showBulkDeleteModal = false"
                    class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400"
                    :disabled="bulkLoading">Batal</button>
                <button @click="handleBulkDelete()"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 flex items-center justify-center gap-2"
                    :disabled="bulkLoading">
                    <template x-if="bulkLoading">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
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
                Bagikan Keluh Kesah
            </h2>
            <p class="text-gray-500 text-sm mb-4">Buat link untuk membagikan <span class="font-bold text-blue-600"
                    x-text="selectedIds.length > 0 ? selectedIds.length + ' keluh kesah yang dipilih' : 'semua keluh kesah yang cocok dengan filter'"></span>.</p>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Judul (opsional)</label>
                <input type="text" x-model="shareTitle" placeholder="Contoh: Keluh Kesah Desember 2026"
                    class="w-full px-3 py-2 rounded-lg bg-white border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 placeholder:text-gray-400">
            </div>

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

    <!-- Target Email Management Modal -->
    <div x-show="showTargetEmailModal" x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        @click.self="showTargetEmailModal = false">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showTargetEmailModal" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm transition-opacity"></div>

            <div
                class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 sm:px-6 pt-6 pb-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg sm:text-xl font-bold text-gray-900">Kelola Email Tujuan</h3>
                        <button @click="showTargetEmailModal = false" class="text-gray-400 hover:text-black">
                            <svg class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <p class="text-sm text-gray-500 mb-4">Email tujuan untuk notifikasi keluh kesah. Hanya email dengan status aktif yang akan menerima notifikasi.</p>

                    <!-- Add Email Form -->
                    <div class="flex gap-2 mb-4">
                        <input type="email" x-model="newTargetEmail"
                            placeholder="email@example.com"
                            class="flex-1 px-3 py-2 rounded-lg border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 placeholder:text-gray-400">
                        <button @click="addTargetEmail()" :disabled="targetEmailLoading"
                            class="px-4 py-2 bg-teal-600 text-white rounded-lg text-sm hover:bg-teal-700 transition font-medium shadow-sm flex items-center gap-1.5 disabled:opacity-50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Tambah
                        </button>
                    </div>

                    <div x-show="targetEmailError" class="bg-red-100 text-red-700 p-2 rounded-lg mb-3 text-sm" x-text="targetEmailError"></div>

                    <!-- Email List -->
                    <div class="space-y-2 max-h-72 overflow-y-auto">
                        <template x-for="email in targetEmails" :key="email.id">
                            <div class="flex items-center justify-between bg-gray-50 rounded-lg p-3 border border-gray-200">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-gray-800 truncate" x-text="email.email"></p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 shrink-0 ml-2">
                                    <button @click="toggleTargetEmail(email.id)"
                                        class="px-2 py-1 rounded-lg text-xs font-medium transition"
                                        :class="email.is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-200 text-gray-500 hover:bg-gray-300'"
                                        x-text="email.is_active ? 'Aktif' : 'Nonaktif'">
                                    </button>
                                    <button @click="deleteTargetEmail(email.id)"
                                        class="text-red-500 hover:text-red-700 p-1" title="Hapus email">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </template>

                        <div x-show="!targetEmailLoading && targetEmails.length === 0"
                            class="text-center py-8 text-gray-400 text-sm">
                            Belum ada email tujuan. Tambahkan email di atas.
                        </div>

                        <div x-show="targetEmailLoading" class="text-center py-4">
                            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-teal-600 mx-auto"></div>
                        </div>
                    </div>
                </div>
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
                        <h3 class="text-lg sm:text-xl font-bold text-gray-900">Kelola Pertanyaan Keluh Kesah</h3>
                        <button @click="showQuestionModal = false" class="text-gray-400 hover:text-black">
                            <svg class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <p class="text-sm text-gray-500 mb-4">Ubah pertanyaan untuk form keluh kesah. Perubahan akan diterapkan secara global.</p>

                    <form method="POST" action="{{ route('form_questions.update', 'keluh_kesah') }}">
                        @csrf
                        @method('PUT')
                        <div class="space-y-4 mb-4 max-h-96 overflow-y-auto">
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
            class="bg-white border border-red-200 text-red-500 p-3 rounded-xl shadow-lg hover:bg-red-50 transition"
            title="Kembali ke atas">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
            </svg>
        </button>
        @if (Auth::user()->role == 'admin')
            <a href="{{ route('dashboard') }}"
                class="bg-red-500 p-3 rounded-xl text-white shadow-lg hover:bg-red-600 transition flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span class="hidden sm:inline">Kembali</span>
            </a>
        @elseif (Auth::user()->role == 'wakil')
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="bg-red-500 p-3 rounded-xl text-white shadow-lg hover:bg-red-600 transition flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span class="hidden sm:inline">Logout</span>
                </button>
            </form>
        @endif
    </div>

    <script>
        function keluhKesahApp() {
            return {
                messages: [],
                currentPage: 1,
                lastPage: 1,
                total: 0,
                loading: true,
                loadingMore: false,
                detailLoading: false,
                searchQuery: '',
                dateFrom: '',
                dateTo: '',
                showModal: false,
                currentMessage: null,
                selectedIds: [],
                showBulkDeleteModal: false,
                bulkLoading: false,
                showShareModal: false,
                shareLoading: false,
                shareUrl: '',
                shareTitle: '',
                copied: false,
                showQuestionModal: false,
                showTargetEmailModal: false,
                targetEmails: [],
                newTargetEmail: '',
                targetEmailLoading: false,
                targetEmailError: '',
                filterSticky: false,
                filterExpanded: false,
                filterBarHeight: 0,
                showScrollTop: false,
                editQuestions: @json($questions).map(q => ({
                    question_key: q.question_key,
                    question_label: q.question_label,
                    placeholder: q.placeholder || '',
                    is_required: !!q.is_required
                })),

                addQuestion() {
                    this.editQuestions.push({
                        question_key: 'custom_' + Date.now(),
                        question_label: 'Pertanyaan Baru',
                        placeholder: '',
                        is_required: true
                    });
                },

                removeQuestion(index) {
                    if (this.editQuestions.length > 1) {
                        this.editQuestions.splice(index, 1);
                    }
                },

                resetQuestions() {
                    if (confirm('Reset semua pertanyaan ke default?')) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '{{ route('form_questions.reset', 'keluh_kesah') }}';
                        const csrf = document.createElement('input');
                        csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = '{{ csrf_token() }}';
                        form.appendChild(csrf);
                        document.body.appendChild(form);
                        form.submit();
                    }
                },

                getAnswer(msg, key) {
                    const builtInKeys = ['keluh_kesah', 'phone_number'];
                    if (builtInKeys.includes(key)) {
                        return msg[key] || '';
                    }
                    if (msg.custom_answers) {
                        const answers = typeof msg.custom_answers === 'string' ? JSON.parse(msg.custom_answers) : msg.custom_answers;
                        return answers[key] || '';
                    }
                    return '';
                },

                highlightText(text, query) {
                    if (!query || !text) return text;
                    const escapedText = text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                    const escapedQuery = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                    const regex = new RegExp(`(${escapedQuery})`, 'gi');
                    return escapedText.replace(regex, '<mark class="bg-yellow-200 px-0.5 rounded">$1</mark>');
                },

                async fetchMessages(reset = true) {
                    if (reset) {
                        this.loading = true;
                        this.currentPage = 1;
                        this.messages = [];
                    } else {
                        this.loadingMore = true;
                    }

                    const params = new URLSearchParams();
                    params.set('page', this.currentPage);
                    if (this.searchQuery) params.set('search', this.searchQuery);
                    if (this.dateFrom) params.set('dateFrom', this.dateFrom);
                    if (this.dateTo) params.set('dateTo', this.dateTo);

                    try {
                        const res = await fetch(`/api/aspiration-keluh-kesah?${params}`);
                        const data = await res.json();
                        if (reset) {
                            this.messages = data.data;
                        } else {
                            this.messages = [...this.messages, ...data.data];
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
                    this.fetchMessages(true);
                },

                loadMore() {
                    if (this.loading || this.loadingMore || this.currentPage >= this.lastPage) return;
                    this.currentPage++;
                    this.fetchMessages(false);
                },

                checkAndLoadMore() {
                    if (this.loading || this.loadingMore || this.currentPage >= this.lastPage) return;
                    if (window.innerHeight + window.scrollY >= document.documentElement.scrollHeight - 200) {
                        this.loadMore();
                    }
                },

                resetFilters() {
                    this.searchQuery = '';
                    this.dateFrom = '';
                    this.dateTo = '';
                    this.fetchMessages(true);
                },

                async showDetail(messageId) {
                    this.detailLoading = true;
                    this.showModal = true;

                    try {
                        const existingMessage = this.messages.find(msg => msg.id === messageId);

                        if (existingMessage) {
                            this.currentMessage = existingMessage;
                            this.detailLoading = false;
                        } else {
                            const response = await fetch(`/aspiration-keluh-kesah/${messageId}`);
                            if (!response.ok) {
                                throw new Error('Gagal mengambil data');
                            }
                            this.currentMessage = await response.json();
                            this.detailLoading = false;
                        }
                    } catch (error) {
                        console.error('Error fetching detail:', error);
                        this.detailLoading = false;
                        this.showModal = false;
                        alert('Gagal memuat detail keluh kesah');
                    }
                },

                getTopicLabel(topic) {
                    const labels = {
                        'akademik': 'Akademik',
                        'fasilitas': 'Fasilitas',
                        'non-akademik': 'Non-Akademik',
                        'lingkungan': 'Lingkungan',
                        'pelayanan': 'Pelayanan',
                        'lainnya': 'Lainnya',
                    };
                    return labels[topic] || topic || 'Lainnya';
                },

                getTopicIcon(topic) {
                    return '';
                },

                formatDate(dateString) {
                    const date = new Date(dateString);
                    const options = {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    };
                    return date.toLocaleDateString("id-ID", options);
                },

                formatTime(dateString) {
                    const date = new Date(dateString);
                    return date.toLocaleTimeString("id-ID", {
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                },

                exportCsv() {
                    const params = new URLSearchParams();
                    if (this.searchQuery) params.set('search', this.searchQuery);
                    if (this.dateFrom) params.set('date_from', this.dateFrom);
                    if (this.dateTo) params.set('date_to', this.dateTo);
                    const qs = params.toString();
                    window.location.href = "{{ route('aspiration_keluhkesah.export') }}" + (qs ? '?' + qs : '');
                },

                toggleSelect(id) {
                    const idx = this.selectedIds.indexOf(id);
                    if (idx === -1) {
                        this.selectedIds.push(id);
                    } else {
                        this.selectedIds.splice(idx, 1);
                    }
                },

                async handleShare() {
                    if (this.shareLoading) return;
                    this.shareLoading = true;
                    try {
                        const payload = {
                            type: 'keluh_kesah',
                            selected_ids: this.selectedIds.length > 0 ? this.selectedIds : null,
                            title: this.shareTitle || null,
                        };
                        if (this.selectedIds.length === 0) {
                            payload.filters = {};
                            if (this.searchQuery) payload.filters.search = this.searchQuery;
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
                            alert(result.message || 'Gagal membuat link share');
                        }
                    } catch (error) {
                        console.error('Gagal share:', error);
                        alert('Terjadi kesalahan saat membuat link share');
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

                async handleBulkDelete() {
                    if (this.selectedIds.length === 0) return;
                    this.bulkLoading = true;
                    try {
                        const response = await fetch("{{ route('aspiration_keluhkesah.bulk-destroy') }}", {
                            method: 'POST',
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            body: JSON.stringify({ ids: this.selectedIds })
                        });
                        const result = await response.json();
                        if (response.ok) {
                            this.selectedIds = [];
                            await this.fetchMessages(true);
                        } else {
                            alert(result.message || 'Gagal menghapus keluh kesah');
                        }
                    } catch (error) {
                        console.error('Gagal menghapus massal:', error);
                        alert('Terjadi kesalahan saat menghapus');
                    } finally {
                        this.bulkLoading = false;
                        this.showBulkDeleteModal = false;
                    }
                },

                async fetchTargetEmails() {
                    this.targetEmailLoading = true;
                    this.targetEmailError = '';
                    try {
                        const res = await fetch("{{ route('target_emails.index') }}");
                        this.targetEmails = await res.json();
                    } catch (error) {
                        console.error('Gagal memuat email:', error);
                    } finally {
                        this.targetEmailLoading = false;
                    }
                },

                async addTargetEmail() {
                    if (!this.newTargetEmail.trim()) return;
                    this.targetEmailLoading = true;
                    this.targetEmailError = '';
                    try {
                        const res = await fetch("{{ route('target_emails.store') }}", {
                            method: 'POST',
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            body: JSON.stringify({
                                email: this.newTargetEmail.trim(),
                            })
                        });
                        const result = await res.json();
                        if (res.ok) {
                            this.newTargetEmail = '';
                            await this.fetchTargetEmails();
                        } else {
                            const data = await res.json();
                            this.targetEmailError = data.message || Object.values(data.errors || {}).flat().join(', ');
                        }
                    } catch (error) {
                        this.targetEmailError = 'Terjadi kesalahan';
                    } finally {
                        this.targetEmailLoading = false;
                    }
                },

             async toggleTargetEmail(id) {
    try {
        const url = "{{ route('target_emails.toggle', ['id' => 'PLACEHOLDER']) }}".replace('PLACEHOLDER', id);
        const res = await fetch(url, {
            method: 'POST',
            headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" }
        });
        if (res.ok) {
            await this.fetchTargetEmails();
        }
    } catch (error) {
        console.error('Gagal mengubah status:', error);
    }
},

async deleteTargetEmail(id) {
    if (!confirm('Hapus email ini?')) return;
    try {
        const url = "{{ route('target_emails.destroy', ['id' => 'PLACEHOLDER']) }}".replace('PLACEHOLDER', id);
        const res = await fetch(url, {
            method: 'DELETE',
            headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" }
        });
        if (res.ok) {
            await this.fetchTargetEmails();
        }
    } catch (error) {
        console.error('Gagal menghapus email:', error);
    }
},

                init() {
                    this.fetchMessages(true);
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