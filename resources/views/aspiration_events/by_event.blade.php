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

<body class="min-h-screen bg-gradient-to-br from-red-50 via-white to-red-100 p-4 pb-20 md:p-8" x-data="aspirationEventApp()">

    <div class="max-w-6xl mx-auto">
        {{-- Header --}}
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 mb-5">
                <div class="text-center sm:text-left">
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-1">
                        Aspirasi <span class="text-red-600">{{ $eventName }}</span>
                    </h1>
                    <p class="text-gray-500 text-sm">Daftar aspirasi yang telah dikumpulkan
                        <span class="text-gray-700 font-medium" x-text="'(' + total + ')'"></span>
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <button @click="showShareModal = true"
                        class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-sm flex items-center gap-1.5 hover:bg-blue-700 transition font-medium shadow-sm">
                        🔗 Share <span x-text="selectedIds.length > 0 ? selectedIds.length : 'Semua'"></span>
                    </button>

                    <button @click="exportCsv" :disabled="total === 0"
                        :class="total === 0 ? 'bg-gray-300 cursor-not-allowed' : 'bg-green-500 hover:bg-green-600'"
                        class="px-3 py-1.5 text-white rounded-lg text-sm flex items-center gap-1.5 transition font-medium shadow-sm">
                        📥 Export
                    </button>
                </div>
            </div>

            {{-- Filters Panel --}}
            <div class="bg-white/70 backdrop-blur-sm rounded-xl border border-red-200 shadow-sm p-3 md:p-4">
                <div class="flex flex-col md:flex-row md:items-center gap-3">
                    <div class="relative md:min-w-[220px]">
                        <input type="text" x-model="searchQuery" @input.debounce.800ms="onFilterChange()" placeholder="Cari aspirasi..."
                            class="w-full pl-3 pr-3 py-2 rounded-lg bg-white border border-red-200 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-red-400 placeholder:text-gray-400">
                    </div>

                    <div class="hidden md:block w-px h-7 bg-red-200 shrink-0"></div>

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

                    <button @click="resetFilters()"
                        x-show="searchQuery || dateFrom || dateTo"
                        class="shrink-0 text-xs text-red-500 hover:text-red-700 font-medium transition px-2 py-1.5 rounded-lg hover:bg-red-50">
                        ↺ Reset
                    </button>
                </div>
            </div>
        </div>

        {{-- Content --}}
        <!-- Loading State -->
        <div x-show="loading" class="bg-white rounded-xl shadow-md p-12 text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-red-600 mx-auto"></div>
            <p class="mt-4 text-gray-600">Memuat data aspirasi...</p>
        </div>

        <template x-if="!loading && aspirations.length === 0">
            <div class="bg-white rounded-xl shadow-md p-8 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="text-xl font-semibold text-gray-700 mb-2"
                    x-text="total === 0 && !searchQuery && !dateFrom && !dateTo ? 'Belum ada aspirasi' : 'Tidak ada hasil filter'"></h3>
                <p class="text-gray-500"
                    x-text="total === 0 && !searchQuery && !dateFrom && !dateTo ? 'Belum ada aspirasi yang dikirim untuk event ini.' : 'Tidak ada aspirasi yang sesuai dengan filter yang dipilih.'">
                </p>
            </div>
        </template>

        <template x-if="!loading && aspirations.length > 0">
            <div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <template x-for="asp in aspirations" :key="asp.id">
                        <div x-data="{ open: false }"
                            class="bg-white rounded-xl shadow-md overflow-hidden border-l-4 border-red-500 hover:shadow-lg transition-all duration-300 flex flex-col">

                            <div class="p-6 flex-1 flex flex-col">
                                {{-- Header --}}
                                <div class="flex items-start gap-3 mb-3">
                                    <!-- Checkbox -->
                                    <input type="checkbox" :checked="selectedIds.includes(asp.id)" @change="toggleSelect(asp.id)"
                                        @click.stop
                                        class="mt-1 h-4 w-4 rounded border-gray-300 text-red-600 focus:ring-red-500 cursor-pointer shrink-0">

                                    <h3 class="text-lg font-semibold text-red-600 flex-1">Kritik, Saran & Masukkan</h3>
                                </div>

                                {{-- Message Preview --}}
                                <div class="mb-4 flex-1">
                                    <p class="text-gray-800 text-sm leading-relaxed"
                                       :class="open ? '' : 'line-clamp-3'"
                                       x-html="highlightText(asp.message, searchQuery)"></p>
                                </div>

                                {{-- Additional Content --}}
                                <div x-show="open" x-transition class="space-y-4 mb-4">
                                    <template x-if="asp.kesan_pesan">
                                        <div class="bg-red-50 rounded-lg p-3 border border-red-100">
                                            <h4 class="text-sm font-semibold text-red-700 mb-2 flex items-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Kesan & Pesan
                                            </h4>
                                            <p class="text-gray-700 text-sm leading-relaxed" x-html="highlightText(asp.kesan_pesan, searchQuery)"></p>
                                        </div>
                                    </template>

                                    <template x-if="asp.bad_moment">
                                        <div class="bg-amber-50 rounded-lg p-3 border border-amber-100">
                                            <h4 class="text-sm font-semibold text-amber-700 mb-2 flex items-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                </svg>
                                                Kejadian buruk
                                            </h4>
                                            <p class="text-gray-700 text-sm leading-relaxed" x-html="highlightText(asp.bad_moment, searchQuery)"></p>
                                        </div>
                                    </template>

                                    <template x-if="asp.perubahan_dari_event">
                                        <div class="bg-blue-50 rounded-lg p-3 border border-blue-100">
                                            <h4 class="text-sm font-semibold text-blue-700 mb-2 flex items-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                                </svg>
                                                Perubahan dari event
                                            </h4>
                                            <p class="text-gray-700 text-sm leading-relaxed" x-html="highlightText(asp.perubahan_dari_event, searchQuery)"></p>
                                        </div>
                                    </template>
                                </div>

                                {{-- Footer --}}
                                <div class="mt-auto pt-4 border-t border-gray-100">
                                    <div class="flex items-center justify-between">
                                        <div class="text-xs text-gray-500">
                                            <span x-text="formatDate(asp.created_at)"></span>
                                        </div>

                                        {{-- Expand Button --}}
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

                {{-- Loading More --}}
                <div x-show="loadingMore" class="w-full text-center py-6">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-600 mx-auto"></div>
                    <p class="mt-2 text-gray-500 text-sm">Memuat data lainnya...</p>
                </div>

                {{-- Sentinel for infinite scroll --}}
                <div x-ref="sentinel" class="w-full h-4"></div>
            </div>
        </template>
    </div>

    <!-- Share Modal -->
    <div x-show="showShareModal" x-cloak
        class="fixed inset-0 p-5 bg-opacity-50 flex justify-center items-center bg-black z-50 backdrop-blur-sm"
        @click.self="showShareModal = false">
        <div class="bg-white rounded-xl shadow-lg p-6 w-96">
            <h2 class="text-lg font-semibold text-gray-800 mb-2">🔗 Bagikan Aspirasi Event</h2>
            <p class="text-gray-500 text-sm mb-4">Buat link untuk membagikan <span class="font-bold text-blue-600"
                    x-text="selectedIds.length > 0 ? selectedIds.length + ' aspirasi yang dipilih' : 'semua aspirasi yang cocok dengan filter'"></span>.</p>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Judul (opsional)</label>
                <input type="text" x-model="shareTitle" placeholder="Contoh: Aspirasi Event Sekolah"
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
                        <span x-text="copied ? '✓' : '📋'"></span>
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
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                    </template>
                    <span x-text="shareLoading ? 'Membuat...' : (shareUrl ? 'Buat Link Baru' : 'Buat Link')"></span>
                </button>
            </div>
        </div>
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
        const exportUrl = "{{ route('aspiration_events.export', ':id') }}";

        function aspirationEventApp() {
            return {
                aspirations: [],
                currentPage: 1,
                lastPage: 1,
                total: 0,
                loading: true,
                loadingMore: false,
                searchQuery: '',
                dateFrom: '',
                dateTo: '',
                filterTarget: '',
                selectedIds: [],
                showShareModal: false,
                shareLoading: false,
                shareUrl: '',
                shareTitle: '',
                copied: false,

                highlightText(text, query) {
                    if (!query || !text) return text;
                    const escapedText = text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                    const escapedQuery = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                    const regex = new RegExp(`(${escapedQuery})`, 'gi');
                    return escapedText.replace(regex, '<mark class="bg-yellow-200 px-0.5 rounded">$1</mark>');
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
                    if (this.dateFrom) params.set('dateFrom', this.dateFrom);
                    if (this.dateTo) params.set('dateTo', this.dateTo);

                    try {
                        const res = await fetch(`/api/aspiration-events/{{ $eventId }}?${params}`);
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
                    this.dateFrom = '';
                    this.dateTo = '';
                    this.fetchAspirations(true);
                },

                formatDate(dateString, short = false) {
                    const date = new Date(dateString);
                    if (short) {
                        return date.toLocaleDateString("id-ID", {
                            day: 'numeric',
                            month: 'short'
                        });
                    }
                    return date.toLocaleString("id-ID", {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric',
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
                    window.location.href = exportUrl.replace(':id', {{ $eventId }}) + (qs ? '?' + qs : '');
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
                            type: 'aspiration_event',
                            selected_ids: this.selectedIds.length > 0 ? this.selectedIds : null,
                            title: this.shareTitle || null,
                        };
                        const filters = { eventId: {{ $eventId }} };
                        if (this.selectedIds.length === 0) {
                            if (this.searchQuery) filters.search = this.searchQuery;
                            if (this.dateFrom) filters.dateFrom = this.dateFrom;
                            if (this.dateTo) filters.dateTo = this.dateTo;
                        }
                        payload.filters = filters;
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

                init() {
                    this.fetchAspirations(true);
                    this.setupScroll();
                },

                setupScroll() {
                    window.addEventListener('scroll', () => {
                        if (this.loading || this.loadingMore || this.currentPage >= this.lastPage) return;
                        if (window.innerHeight + window.scrollY >= document.documentElement.scrollHeight - 200) {
                            this.loadMore();
                        }
                    });
                }
            }
        }
    </script>

    <style>
        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>

</body>

</html>
