<div
    x-data="{ editing: false, copied: false }"
    data-bookmark-card
    tabindex="0"
    @click="if (!editing) window.location.href = '{{ $bookmark->url }}'"
    @keydown.enter="if (!editing) window.location.href = '{{ $bookmark->url }}'"
    class="group relative rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-5 cursor-pointer transition-all duration-150 hover:scale-[1.03] hover:shadow-lg hover:z-10 focus:scale-[1.03] focus:shadow-lg focus:z-10 focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-100"
>
    {{-- View mode --}}
    <div x-show="!editing">
        <div class="flex items-start justify-between gap-2">
            <p class="font-semibold text-base leading-snug break-words">
                {{ $bookmark->name ?: $bookmark->displayUrl() }}
            </p>

            <div class="flex items-center gap-1 shrink-0 opacity-0 group-hover:opacity-100 group-focus:opacity-100 transition-opacity" @click.stop>
                {{-- Copy URL --}}
                <button
                    type="button"
                    title="Copy URL"
                    @click="navigator.clipboard.writeText('{{ $bookmark->url }}'); copied = true; setTimeout(() => copied = false, 1200)"
                    class="p-1.5 rounded-md text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                        <path d="M7 3.5A1.5 1.5 0 0 1 8.5 2h4A1.5 1.5 0 0 1 14 3.5v1h.75A2.25 2.25 0 0 1 17 6.75v9.5A2.25 2.25 0 0 1 14.75 18.5h-6.5A2.25 2.25 0 0 1 6 16.25v-9.5A2.25 2.25 0 0 1 8.25 4.5H9v-1Z"/>
                        <path d="M3 6.25A2.25 2.25 0 0 1 5.25 4H6v10.25A2.75 2.75 0 0 0 8.75 17H13v.25A2.25 2.25 0 0 1 10.75 19.5h-5.5A2.25 2.25 0 0 1 3 17.25v-11Z"/>
                    </svg>
                </button>

                {{-- Edit --}}
                <button
                    type="button"
                    title="Edit"
                    @click="editing = true"
                    class="p-1.5 rounded-md text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                        <path d="m17.414 2.586-1.414 1.414a1 1 0 0 0 0 1.414L18.586 8l1.414-1.414a1 1 0 0 0 0-1.414l-1.414-1.414a1 1 0 0 0-1.414 0Z"/>
                        <path d="M16 6 4 18v3h3L19 9l-3-3Z"/>
                    </svg>
                </button>
            </div>
        </div>

        <p class="mt-1.5 text-xs text-gray-400 dark:text-gray-600 truncate">
            {{ $bookmark->displayUrl() }}
        </p>

        <p x-show="copied" x-transition x-cloak class="mt-1 text-xs text-green-600 dark:text-green-400">Copied!</p>
    </div>

    {{-- Edit mode --}}
    <div x-show="editing" x-cloak @click.stop>
        <form method="POST" action="{{ route('bookmarks.update', $bookmark) }}" class="space-y-2">
            @csrf
            @method('PUT')
            <input type="text" name="name" value="{{ $bookmark->name }}" placeholder="Name"
                class="w-full text-sm rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 focus:ring-gray-900 dark:focus:ring-gray-100">
            <input type="url" name="url" value="{{ $bookmark->url }}" required
                class="w-full text-sm rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 focus:ring-gray-900 dark:focus:ring-gray-100">

            <div class="flex items-center justify-between pt-1">
                <button type="button" @click="editing = false" class="text-xs text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                    Cancel
                </button>
                <button type="submit" class="text-xs font-medium text-gray-900 dark:text-gray-100 hover:underline">
                    Save
                </button>
            </div>
        </form>

        <form method="POST" action="{{ route('bookmarks.destroy', $bookmark) }}" onsubmit="return confirm('Delete this bookmark?')" class="mt-2">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-xs text-red-500 hover:text-red-700">Delete bookmark</button>
        </form>
    </div>
</div>
