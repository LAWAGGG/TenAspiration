<div
    class="rounded-xl border p-4 md:p-5 transition duration-200 group
    @if ($hidden) bg-gray-50 border-gray-200 opacity-60 hover:opacity-80
    @else bg-gradient-to-br from-white to-red-50 border-red-200 hover:shadow-md @endif">
    <div class="flex justify-between items-start mb-3">
        <h3 class="font-bold text-gray-900 text-base md:text-lg @if(!$hidden) group-hover:text-red-600 @endif transition duration-200 line-clamp-2 flex-1 min-w-0">
            {{ $event->name }}
            <span class="font-normal text-gray-400">({{ $event->aspiration->count() }} Aspirasi)</span>
        </h3>
        <div class="flex items-center gap-1 flex-shrink-0 ml-2">
            @if ($hidden)
                <span class="bg-gray-200 text-gray-500 text-xs px-2 py-0.5 rounded-full font-medium">Tersembunyi</span>
            @endif
            <form action="{{ route('events.toggle-visibility', $event) }}" method="POST" class="inline">
                @csrf
                @method('PATCH')
                <button type="submit"
                    class="p-1 rounded-lg transition duration-200
                    @if ($hidden) text-gray-400 hover:text-gray-600 hover:bg-gray-200
                    @else text-gray-400 hover:text-blue-500 hover:bg-blue-50 @endif"
                    title="{{ $hidden ? 'Tampilkan ke form publik' : 'Sembunyikan dari form publik' }}">
                    @if ($hidden)
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    @endif
                </button>
            </form>
            <button
                type="button"
                @click="eventToDelete = {{ $event->id }}; eventToDeleteName = {{ json_encode($event->name) }}; showDeleteModal = true"
                class="text-gray-400 hover:text-red-500 p-1 rounded-lg hover:bg-red-50 transition duration-200"
                title="Hapus Event">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        </div>
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
        class="w-full py-2 px-3 md:py-2 md:px-4 rounded-lg transition duration-200 flex items-center justify-center space-x-2 text-xs md:text-sm font-medium
        @if ($hidden) bg-gray-300 text-gray-600 hover:bg-gray-400
        @else bg-red-500 hover:bg-red-600 text-white @endif">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 md:h-4 md:w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
        </svg>
        <span>Lihat Aspirasi</span>
    </a>
</div>
