<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    @vite('resources/css/app.css')
</head>

<body>
    <div class="min-h-screen bg-gradient-to-br from-red-50 via-white to-red-100 py-10 px-6">
        <div class="max-w-5xl mx-auto pb-5">
            {{-- Header --}}
            <div class="text-center mb-10">
                <h1 class="text-3xl font-bold text-red-700">Admin Dashboard</h1>
                <p class="mt-3 text-gray-600">
                    Kelola aspirasi dan event sekolah dengan mudah.
                </p>
            </div>

            {{-- Shortcut --}}
            <div class="flex justify-center mb-8 flex-wrap gap-4">
                <a href="{{ route('aspirations.index') }}"
                    class="px-5 py-2 bg-red-500 text-white rounded-lg shadow-md hover:bg-red-600 transition">
                    📜 Lihat Aspirasi Harian
                </a>
                <button onclick="document.getElementById('modal-add-event').classList.remove('hidden')"
                    class="py-2 px-5 bg-red-500 text-white rounded-lg shadow-md hover:bg-red-600 transition">
                    + Tambahkan Event
                </button>
            </div>

            {{-- Event List --}}
            <h2 class="text-xl font-semibold text-gray-800 mb-6">📅 Daftar Event</h2>
            @if ($events->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($events as $event)
                        <div
                            class="p-5 bg-white rounded-xl shadow-md border-l-8 border-red-400 hover:shadow-lg transition">
                            <h3 class="text-lg font-bold text-red-700 mb-2">{{ $event->name }}</h3>
                            <p class="text-gray-700 mb-4">{{ $event->description ?? 'Tidak ada deskripsi' }}</p>
                            <div class="flex justify-between">
                                <a href="{{ route('aspiration_events.by_event', $event->id) }}"
                                    class="inline-block px-4 py-1 bg-red-200 text-gray-700 rounded-lg hover:bg-red-300 transition">
                                    Lihat Aspirasi →
                                </a>
                                <form action="{{ route('events.destroy', $event->id) }}" method="POST" class="ml-4">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="text-gray-500 px-3 py-1 rounded-lg text-sm hover:bg-gray-200 cursor-pointer">
                                        🗑
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-center text-gray-500">🙁 Belum ada event yang tersedia.</p>
            @endif

            {{-- Modal Tambah Event --}}
            <div id="modal-add-event"
                class="hidden fixed inset-0 p-5 bg-opacity-75 flex justify-center items-center z-50 backdrop-blur-sm">
                <div class="bg-white rounded-xl p-6 w-full max-w-md shadow-lg">
                    <h2 class="text-xl font-bold text-red-600 mb-4">Tambah Event Baru</h2>
                    <form action="{{ route('events.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="text" name="name" placeholder="Nama Event"
                            class="w-full border px-3 py-2 rounded-lg" required>
                        <textarea name="description" placeholder="Deskripsi" class="w-full border px-3 py-2 rounded-lg"></textarea>
                        <input type="date" name="date" class="w-full border px-3 py-2 rounded-lg" required>
                        <div class="flex justify-end gap-3">
                            <button type="button"
                                onclick="document.getElementById('modal-add-event').classList.add('hidden')"
                                class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400">
                                Batal
                            </button>
                            <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Logout Button --}}
            <div class="fixed right-5 bottom-5 bg-red-500 p-3 rounded-xl text-white cursor-pointer hover:bg-red-600">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
