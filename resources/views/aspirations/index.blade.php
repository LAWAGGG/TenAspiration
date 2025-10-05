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
    <div class="max-w-4xl mx-auto mb-8 flex flex-col md:flex-row items-center justify-between gap-4">
        <h1 class="text-2xl font-bold text-red-700 text-center md:text-left">📜 Daftar Aspirasi</h1>

        <div class="flex items-center gap-3">
            <!-- Filter Dropdown -->
            <select x-model="filterTarget"
                class="px-3 py-2 rounded-md bg-red-100 text-sm border border-red-200 focus:outline-none focus:ring-2 focus:ring-red-400">
                <option value="">ALL</option>
                <option value="MPK">MPK</option>
                <option value="OSIS">OSIS</option>
                <option value="SEKOLAH">SEKOLAH</option>
            </select>

            <!-- Export CSV -->
            <button @click="exportCsv"
                class="px-4 py-2 bg-green-500 text-white rounded-lg shadow hover:bg-green-600 transition text-sm flex items-center gap-2">
                📥 Export CSV
            </button>
        </div>
    </div>

    <!-- Content -->
    <div class="flex flex-col gap-4 max-w-2xl mx-auto">
        <template x-for="asp in filteredAspirations" :key="asp.id">
            <div class="bg-white rounded-xl flex justify-between shadow-md p-5 border-l-8 hover:shadow-lg transition cursor-pointer"
                :class="asp.to === 'MPK' ? 'border-red-600' : asp.to === 'OSIS' ? 'border-blue-500' : 'border-gray-400'">
                <div>
                    <h3 class="text-lg font-semibold" :class="asp.to === 'MPK' ? 'text-red-700' : asp.to === 'OSIS' ? 'text-blue-700' : 'text-black'">
                        🎯 <span x-text="asp.to"></span>
                    </h3>
                    <p class="text-gray-700 mt-1" x-text="asp.message"></p>
                    <p class="text-xs text-gray-500 mt-3" x-text="asp.date ? formatDate(asp.date) : ''"></p>
                </div>
                <button @click="confirmDelete(asp.id)"
                    class="text-gray-500 px-3 py-1 rounded-lg text-sm hover:bg-gray-200 cursor-pointer">
                    🗑
                </button>
            </div>
        </template>
        <p x-show="filteredAspirations.length === 0" class="text-center text-gray-500">🙁 Tidak ada aspirasi.</p>
    </div>

    <!-- Delete Modal -->
    <div x-show="showDeleteModal" class="fixed inset-0 bg-opacity-75 flex justify-center items-center z-50 backdrop-blur-sm" x-cloak>
        <div class="bg-white rounded-xl shadow-lg p-6 w-96 text-center">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Konfirmasi Hapus</h2>
            <p class="text-gray-600 mb-6">Apakah kamu yakin ingin menghapus aspirasi ini?</p>
            <div class="flex justify-center gap-4">
                <button @click="showDeleteModal=false" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400">Batal</button>
                <button @click="handleDelete" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Ya, Hapus</button>
            </div>
        </div>
    </div>

    <!-- Back button -->
    <div class="fixed right-5 bottom-5 bg-red-500 p-3 rounded-xl text-white">
        <a href="{{ route('dashboard') }}">Go back</a>
    </div>

    <script>
        function aspirationApp() {
            return {
                aspirations: @json($aspirations),
                filterTarget: '',
                showDeleteModal: false,
                deleteId: null,

                get filteredAspirations() {
                    return this.aspirations.filter(a => this.filterTarget === '' || a.to === this.filterTarget);
                },

                formatDate(date) {
                    return new Date(date).toLocaleString("id-ID", { dateStyle: "long", timeStyle: "short" });
                },

                confirmDelete(id) {
                    this.deleteId = id;
                    this.showDeleteModal = true;
                },

                handleDelete() {
                    if (!this.deleteId) return;
                    fetch(`/aspirations/${this.deleteId}`, {
                        method: 'DELETE',
                        headers: {
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        }
                    }).then(() => {
                        this.aspirations = this.aspirations.filter(a => a.id !== this.deleteId);
                        this.deleteId = null;
                        this.showDeleteModal = false;
                    });
                },

                exportCsv() {
                    window.location.href = "{{ route('aspirations.export') }}";
                }
            }
        }
    </script>

</body>
</html>
