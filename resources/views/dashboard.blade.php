<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin Dashboard</title>
    @vite('resources/css/app.css')
    <script src="//unpkg.com/alpinejs" defer></script>
</head>

<body x-data="{
    showAddModal: false,
    showDeleteModal: false,
    eventToDelete: null,
    eventToDeleteName: ''
}" class="bg-gradient-to-br from-red-50 via-white to-red-50 min-h-screen">
    <div class="min-h-screen">
        <!-- Header -->
        <div class="bg-white shadow-sm border-b border-red-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4 md:py-6">
                    <div class="flex items-center space-x-3">
                        <div class="bg-red-500 p-2 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white md:h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
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
                        <button type="submit" class="flex items-center space-x-2 bg-red-500 hover:bg-red-600 text-white p-2 md:px-4 md:py-2 rounded-lg transition duration-200">
                            <!-- Ikon untuk mobile -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 md:h-4 md:w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
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
                        <h2 class="text-xl md:text-2xl font-bold text-gray-900 mb-2">Selamat Datang, Admin! 👋</h2>
                        <p class="text-gray-600 text-sm md:text-base">Kelola aspirasi dan event sekolah dengan mudah dan efisien.</p>
                    </div>
                    <div class="hidden md:block bg-red-100 p-4 rounded-xl ml-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 mb-6 md:mb-8">
                <a href="{{ route('aspirations.index') }}" class="bg-white rounded-2xl shadow-sm border border-red-100 p-4 md:p-6 hover:shadow-md transition duration-200 group">
                    <div class="flex items-center space-x-3 md:space-x-4">
                        <div class="bg-red-500 p-2 md:p-3 rounded-xl group-hover:bg-red-600 transition duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 md:h-6 md:w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 text-base md:text-lg">Aspirasi The Voxes</h3>
                            <p class="text-gray-600 text-xs md:text-sm">Kelola aspirasi proker "The Voxes" dari siswa</p>
                        </div>
                    </div>
                </a>

                <button @click="showAddModal = true" class="bg-white rounded-2xl shadow-sm border border-red-100 p-4 md:p-6 hover:shadow-md transition duration-200 group text-left w-full">
                    <div class="flex items-center space-x-3 md:space-x-4">
                        <div class="bg-red-500 p-2 md:p-3 rounded-xl group-hover:bg-red-600 transition duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 md:h-6 md:w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
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
                <div class="flex items-center justify-between mb-4 md:mb-6">
                    <h2 class="text-lg md:text-xl font-bold text-gray-900 flex items-center space-x-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 md:h-5 md:w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>Daftar Event</span>
                    </h2>
                    <span class="bg-red-100 text-red-600 px-2 py-1 md:px-3 md:py-1 rounded-full text-xs md:text-sm font-medium">
                        {{ $events->count() }} Event
                    </span>
                </div>

                @if ($events->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
                        @foreach ($events as $event)
                            <div class="bg-gradient-to-br from-white to-red-50 rounded-xl border border-red-200 p-4 md:p-5 hover:shadow-md transition duration-200 group">
                                <div class="flex justify-between items-start mb-3">
                                    <h3 class="font-bold text-gray-900 text-base md:text-lg group-hover:text-red-600 transition duration-200 line-clamp-2">{{ $event->name }} <span class="font-normal text-gray-400">({{$event->aspiration->count()}} Aspirasi)</span></h3>
                                    <button @click="eventToDelete = {{ $event->id }}; eventToDeleteName = '{{ $event->name }}'; showDeleteModal = true"
                                        class="text-gray-400 hover:text-red-500 p-1 rounded-lg hover:bg-red-50 transition duration-200 flex-shrink-0 ml-2"
                                        title="Hapus Event">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>

                                <p class="text-gray-600 text-xs md:text-sm mb-3 md:mb-4 line-clamp-2">
                                    {{ $event->description ?? 'Tidak ada deskripsi' }}
                                </p>

                                <div class="flex items-center justify-between text-xs md:text-sm text-gray-500 mb-3 md:mb-4">
                                    <div class="flex items-center space-x-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 md:h-4 md:w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span>{{ \Carbon\Carbon::parse($event->date)->translatedFormat('d M Y') }}</span>
                                    </div>
                                </div>

                                <a href="{{ route('aspiration_events.by_event', $event->id) }}"
                                    class="w-full bg-red-500 hover:bg-red-600 text-white py-2 px-3 md:py-2 md:px-4 rounded-lg transition duration-200 flex items-center justify-center space-x-2 text-xs md:text-sm font-medium">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 md:h-4 md:w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <span>Lihat Aspirasi</span>
                                </a>
                            </div>
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
                        <button @click="showAddModal = true" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 md:px-6 md:py-2 rounded-lg transition duration-200 font-medium text-sm md:text-base">
                            Buat Event Pertama
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- Modal Tambah Event -->
        <div x-show="showAddModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showAddModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm transition-opacity"></div>

                <div class="inline-block w-full align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 sm:px-6 pt-6 pb-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg sm:text-xl font-bold text-gray-900">Tambah Event Baru</h3>
                            <button @click="showAddModal = false" class="text-gray-400 hover:text-black">
                                <svg class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <form action="{{ route('events.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Event</label>
                                <input type="text" name="name" placeholder="Masukkan nama event" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 sm:px-4 sm:py-3 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition duration-200 text-sm sm:text-base">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                                <textarea name="description" placeholder="Masukkan deskripsi event (opsional)" rows="3"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 sm:px-4 sm:py-3 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition duration-200 text-sm sm:text-base"></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Event</label>
                                <input type="date" name="date" required
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
                <div x-show="showDeleteModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm transition-opacity"></div>

                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 sm:px-6 pt-6 pb-4">
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="bg-red-100 p-2 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                            </div>
                            <h3 class="text-lg sm:text-xl font-bold text-gray-900">Hapus Event</h3>
                        </div>

                        <p class="text-gray-700 text-sm sm:text-base mb-2">
                            Anda akan menghapus event: <span class="font-semibold text-red-600" x-text="eventToDeleteName"></span>
                        </p>
                        <p class="text-xs sm:text-sm text-gray-500 mb-6">
                            Tindakan ini tidak dapat dibatalkan. Semua aspirasi yang terkait dengan event ini juga akan dihapus.
                        </p>

                        <div class="flex justify-end space-x-3">
                            <button @click="showDeleteModal = false; eventToDelete = null; eventToDeleteName = ''"
                                class="px-3 py-2 sm:px-4 sm:py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition duration-200 font-medium text-sm sm:text-base">
                                Batal
                            </button>
                            <form :action="`/events/${eventToDelete}`" method="POST">
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
</body>
</html>
