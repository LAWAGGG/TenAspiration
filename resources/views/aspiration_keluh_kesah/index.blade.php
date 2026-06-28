<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Keluh Kesah</title>
    @vite('resources/css/app.css')
    <script src="//unpkg.com/alpinejs" defer></script>
    <style>
        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .modal-overlay {
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            max-height: 90vh;
            overflow-y: auto;
        }

        [x-cloak] {
            display: none !important;
        }

        .card-container {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .card-content {
            flex: 1;
        }

        .card-footer {
            margin-top: auto;
        }
    </style>
</head>

<body class="min-h-screen bg-gradient-to-br from-red-50 via-white to-red-100 py-10 px-4" x-data="keluhKesahApp()">

    <!-- Header -->
    <div class="max-w-4xl mx-auto mb-8">
        <!-- Title + Actions -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 mb-5">
            <h1 class="text-2xl font-bold text-red-700 flex items-center gap-2">
                📜 Daftar Keluh Kesah
                <span class="text-gray-500 font-normal text-lg" x-text="'(' + total + ')'"></span>
            </h1>

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

        <!-- Filters Panel -->
        <div class="bg-white/70 backdrop-blur-sm rounded-xl border border-red-200 shadow-sm p-3 md:p-4">
            <div class="flex flex-col md:flex-row md:items-center gap-3">
                <!-- Search -->
                <div class="relative md:min-w-[220px]">
                    <input type="text" x-model="searchQuery" @input.debounce.800ms="onFilterChange()" placeholder="Cari keluh kesah..."
                        class="w-full pl-3 pr-3 py-2 rounded-lg bg-white border border-red-200 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-red-400 placeholder:text-gray-400">
                </div>

                <!-- Divider (desktop) -->
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
            <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition w-full sm:w-[48%] lg:w-[31%] card-container cursor-pointer"
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
                            <!-- Topic Badge -->
                            <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full mb-3"
                                x-text="getTopicLabel(msg.topic)">
                            </span>

                            <!-- Phone Number -->
                            <div class="flex items-center gap-2 text-sm text-gray-700 mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <span x-text="msg.phone_number"></span>
                            </div>

                            <!-- Message Preview -->
                            <div class="relative">
                                <p class="text-gray-700 text-sm line-clamp-3" :ref="'desc' + msg.id"
                                    x-text="msg.keluh_kesah">
                                </p>

                                <!-- Gradient overlay untuk menunjukkan ada konten lebih lanjut -->
                                <div x-show="isContentOverflowing"
                                    class="absolute bottom-0 left-0 right-0 h-6 bg-gradient-to-t from-white to-transparent">
                                </div>
                            </div>
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
                            <span x-text="getTopicIcon(msg.topic)"></span>
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
                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full mt-2"
                        x-text="currentMessage ? getTopicLabel(currentMessage.topic) : ''">
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
                <!-- Phone Number -->
                <div class="mb-6 p-4 bg-red-50 rounded-lg">
                    <div class="flex items-center gap-2 text-gray-700 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <span class="font-semibold">Nomor Telepon:</span>
                    </div>
                    <p class="text-gray-800 text-lg font-medium" x-text="currentMessage?.phone_number"></p>
                </div>

                <!-- Message Content -->
                <div class="mb-6">
                    <h3 class="font-semibold text-gray-700 mb-3">Keluh Kesah:</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-gray-700 whitespace-pre-wrap" x-text="currentMessage?.keluh_kesah"></p>
                    </div>
                </div>

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

    <!-- Share Modal -->
    <div x-show="showShareModal" x-cloak
        class="fixed inset-0 p-5 bg-opacity-50 flex justify-center items-center bg-black z-50 backdrop-blur-sm"
        @click.self="showShareModal = false">
        <div class="bg-white rounded-xl shadow-lg p-6 w-96">
            <h2 class="text-lg font-semibold text-gray-800 mb-2">🔗 Bagikan Keluh Kesah</h2>
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

    <!-- Back button -->
    <div class="fixed right-5 bottom-5 bg-red-500 p-3 rounded-xl text-white shadow-lg">
        @if (Auth::user()->role == 'admin')
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
        @elseif (Auth::user()->role == 'wakil')
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="flex items-center space-x-2 text-white md:px-4 rounded-lg transition duration-200">
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
                showShareModal: false,
                shareLoading: false,
                shareUrl: '',
                shareTitle: '',
                copied: false,

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
                        // Cek apakah data sudah ada di local messages
                        const existingMessage = this.messages.find(msg => msg.id === messageId);

                        if (existingMessage) {
                            this.currentMessage = existingMessage;
                            this.detailLoading = false;
                        } else {
                            // Fetch detail dari API jika tidak ada di local
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
                    const icons = {
                        'akademik': '📚',
                        'fasilitas': '🏗️',
                        'non-akademik': '🎯',
                        'lingkungan': '🌿',
                        'pelayanan': '🤝',
                        'lainnya': '📌',
                    };
                    return icons[topic] || '📌';
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

                init() {
                    this.fetchMessages(true);
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

</body>

</html>
