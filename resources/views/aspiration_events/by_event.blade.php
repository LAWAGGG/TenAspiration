<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    @vite('resources/css/app.css')
    <script src="//unpkg.com/alpinejs" defer></script>
</head>

<body>
    <div class="min-h-screen bg-gradient-to-br from-red-50 via-white to-red-100 p-4 md:p-8">

        <div class="max-w-4xl mx-auto">
            {{-- Header --}}
            <div class="mb-8 text-center flex flex-col gap-4">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">
                    Aspirasi <span class="text-red-600">Event</span>
                </h1>
                <p class="text-gray-600">Daftar aspirasi yang telah dikumpulkan</p>
                <a href="{{ url('/api/aspiration/events/event/' . $eventId . '/csv') }}"
                    class="px-4 py-2 bg-green-500 text-white rounded-lg shadow hover:bg-green-600 transition"
                    download="aspiration-event-{{ $eventId }}.csv">
                    📥 Export CSV
                </a>
            </div>

            @if ($aspirations->isEmpty())
                {{-- Tidak ada aspirasi --}}
                <div class="bg-white rounded-xl shadow-md p-8 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">Belum ada aspirasi</h3>
                    <p class="text-gray-500">Belum ada aspirasi yang dikirim untuk event ini.</p>
                </div>
            @else
                {{-- List Aspirasi --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach ($aspirations as $asp)
                        <div
                            class="bg-white rounded-xl shadow-md overflow-hidden border-l-4 border-red-500 hover:shadow-lg transition-all duration-300">
                            <div class="p-6">
                                {{-- Pesan --}}
                                <div class="flex items-start mb-4">
                                    <div class="bg-red-100 p-2 rounded-full mr-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-gray-800 font-medium">{{ $asp->message }}</p>
                                    </div>
                                </div>

                                {{-- Footer: To dan Tanggal --}}
                                <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-500 mr-1"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        <span class="text-sm font-medium text-gray-700">
                                            {{ $asp->to === 'lainnya' ? $asp->other_to : $asp->to }}
                                        </span>
                                    </div>

                                    @if ($asp->date)
                                        <div class="flex items-center text-xs text-gray-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ \Carbon\Carbon::parse($asp->date)->translatedFormat('d F Y, H:i') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Back button --}}
        <div class="fixed right-5 bottom-5 bg-red-500 p-3 rounded-xl text-white">
            <a href="{{ route('dashboard') }}">Go back</a>
        </div>
    </div>
</body>

</html>
