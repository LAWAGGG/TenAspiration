<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Aspirasi</title>
    @vite('resources/css/app.css')
    <script src="//unpkg.com/alpinejs" defer></script>
</head>

<body class="min-h-screen bg-gradient-to-br from-red-50 via-white to-red-100 py-10 px-4" x-data="aspirationApp()">

    <!-- Header -->
    <div class="max-w-4xl mx-auto mb-8">
        <!-- Title + Actions -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 mb-5">
            <h1 class="text-2xl font-bold text-red-700 flex items-center gap-2">
                📜 Daftar Aspirasi
                <span class="text-gray-500 font-normal text-lg" x-text="'(' + total + ')'"></span>
            </h1>

            <div class="flex items-center gap-2">
                <button @click="showShareModal = true"
                    class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-sm flex items-center gap-1.5 hover:bg-blue-700 transition font-medium shadow-sm">
                    🔗 Share <span x-text="selectedIds.length > 0 ? selectedIds.length : ''"></span>
                </button>

                <button @click="showBulkDeleteModal = true" x-show="selectedIds.length > 0"
                    class="px-3 py-1.5 bg-red-600 text-white rounded-lg text-sm flex items-center gap-1.5 hover:bg-red-700 transition font-medium shadow-sm">
                    🗑 Hapus <span x-text="selectedIds.length"></span>
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
                    <input type="text" x-model="searchQuery" @input.debounce.800ms="onFilterChange()"
                        placeholder="Cari aspirasi..."
                        class="w-full pl-3 pr-3 py-2 rounded-lg bg-white border border-red-200 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-red-400 placeholder:text-gray-400">
                </div>

                <!-- Divider (desktop) -->
                <div class="hidden md:block w-px h-7 bg-red-200 shrink-0"></div>

                <!-- Bagian -->
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-500 font-medium shrink-0 w-12 md:w-auto">Bagian</span>
                    <select x-model="filterBagian" @change="onFilterChange()"
                        class="flex-1 md:flex-none px-3 py-2 rounded-lg bg-white border border-red-200 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-red-400">
                        <option value="">Semua</option>
                        <option value="wakil kesiswaan">Wakil Kesiswaan</option>
                        <option value="wakil sarpras">Wakil Sarpras</option>
                        <option value="wakil kurikulum">Wakil Kurikulum</option>
                        <option value="wakil humas">Wakil Humas</option>
                        <option value="tata usaha">Tata Usaha</option>
                        <option value="Ekskul">Ekskul</option>
                        <option value="MPK">MPK</option>
                        <option value="OSIS">OSIS</option>
                        <option value="umum">Umum</option>
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

                <!-- Divider (desktop) -->
                <div class="hidden md:block w-px h-7 bg-red-200 shrink-0"></div>

                <!-- Date Range -->
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-500 font-medium shrink-0 w-12 md:w-auto">Tanggal</span>
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
            <div class="bg-white rounded-xl flex flex-col sm:flex-row justify-between shadow-md p-5 border-l-8 hover:shadow-lg transition w-full sm:w-[48%] lg:w-[31%]"
                :class="[asp.to === 'MPK' ? 'border-red-600' : asp.to === 'OSIS' ? 'border-blue-500' :
                    'border-gray-400', selectedIds.includes(asp.id) ? 'ring-2 ring-red-400' : ''
                ]">
                <div class="flex items-start gap-3 flex-1 min-w-0">
                    <!-- Checkbox -->
                    <input type="checkbox" :checked="selectedIds.includes(asp.id)" @change="toggleSelect(asp.id)"
                        class="mt-1 h-4 w-4 rounded border-gray-300 text-red-600 focus:ring-red-500 cursor-pointer shrink-0">

                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-semibold"
                            :class="asp.to === 'MPK' ? 'text-red-700' : asp.to === 'OSIS' ? 'text-blue-700' :
                                'text-black'">
                            🎯 <span x-html="highlightText(asp.to, searchQuery)"></span>
                        </h3>
                        <p class="text-gray-700 mt-1 break-words" x-html="highlightText(asp.message, searchQuery)"></p>
                        <p class="text-gray-700 mt-1" x-text="asp.kelas"></p>
                        <p class="text-xs text-gray-500 mt-3" x-text="asp.created_at ? formatDate(asp.created_at) : ''">
                        </p>
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
            <h2 class="text-lg font-semibold text-gray-800 mb-2">🔗 Share</h2>
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
    <div class="fixed right-5 bottom-5 bg-red-500 p-3 rounded-xl text-white">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
    </div>

    <script>
        function aspirationApp() {
            return {
                aspirations: [],
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
                            alert(result.message || 'Gagal menghapus aspirasi');
                        }
                    } catch (error) {
                        console.error('Gagal menghapus massal:', error);
                        alert('Terjadi kesalahan saat menghapus');
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

</body>

</html>
