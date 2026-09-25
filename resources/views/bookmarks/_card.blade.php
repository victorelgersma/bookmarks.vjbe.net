<div
    x-data="{ editing: false, copied: false }"
    data-bookmark-card
    data-search="{{ Str::lower(($bookmark->name ?: '').' '.$bookmark->displayUrl().' '.$bookmark->tags->pluck('name')->implode(' ')) }}"
    x-show="search.trim() === '' || $el.dataset.search.includes(search.trim().toLowerCase())"
    tabindex="0"
    @click="if (!editing) window.open(@js($bookmark->url), '_blank', 'noopener')"
    @keydown.enter="if (!editing) window.open(@js($bookmark->url), '_blank', 'noopener')"
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
                    @click="navigator.clipboard.writeText(@js($bookmark->url)); copied = true; setTimeout(() => copied = false, 1200)"
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

        @if ($bookmark->tags->isNotEmpty())
            <div class="mt-2 flex flex-wrap gap-1.5" @click.stop>
                @foreach ($bookmark->tags as $tag)
                    <a
                        href="{{ route('tags.show', $tag) }}"
                        class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors"
                    >
                        {{ $tag->name }}
                    </a>
                @endforeach
            </div>
        @endif

        <p x-show="copied" x-transition x-cloak class="mt-1 text-xs text-green-600 dark:text-green-400">Copied!</p>
    </div>

    {{-- Edit mode --}}
    <div x-show="editing" x-cloak @click.stop>
        <form method="POST" action="{{ route('bookmarks.update', $bookmark) }}" class="space-y-2">
            @csrf
            @method('PUT')
            <input type="text" name="name" value="{{ $bookmark->name }}" placeholder="Name"
                class="w-full px-3 py-2 text-sm rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 focus:ring-gray-900 dark:focus:ring-gray-100">
            <input type="url" name="url" value="{{ $bookmark->url }}" required
                class="w-full px-3 py-2 text-sm rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 focus:ring-gray-900 dark:focus:ring-gray-100">

            {{-- Tag chip input, pre-filled with this bookmark's current tags --}}
            <div
                x-data="tagInput(@js($bookmark->tags->pluck('name')->all()), @js($availableTags ?? []))"
                class="flex flex-wrap items-center gap-1.5 rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-2 py-1.5 focus-within:ring-2 focus-within:ring-gray-900 dark:focus-within:ring-gray-100"
            >
                <template x-for="(tag, index) in tags" :key="tag">
                    <span class="inline-flex items-center gap-1 text-xs pl-2 pr-1 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                        <span x-text="tag"></span>
                        <button type="button" @click="removeTag(index)" class="text-gray-400 hover:text-gray-700 dark:hover:text-gray-100" aria-label="Remove tag">&times;</button>
                    </span>
                </template>
                <div class="relative flex-1 min-w-[5rem]">
                    <input
                        type="text"
                        x-model="draft"
                        @keydown.enter.prevent="selectHighlighted()"
                        @keydown.comma.prevent="selectHighlighted()"
                        @keydown.down.prevent="moveHighlight(1)"
                        @keydown.up.prevent="moveHighlight(-1)"
                        @keydown.escape="draft = ''; highlightedIndex = -1"
                        @keydown.backspace="removeLastTag()"
                        @blur="addTag()"
                        placeholder="Tags…"
                        class="w-full bg-transparent border-0 p-0 text-sm focus:ring-0 dark:text-gray-100 placeholder:text-gray-400 dark:placeholder:text-gray-600"
                        autocomplete="off"
                    >
                    <ul
                        x-show="suggestions.length > 0"
                        x-cloak
                        class="absolute z-10 mt-1 w-48 max-h-40 overflow-auto rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-lg text-sm"
                    >
                        <template x-for="(name, i) in suggestions" :key="name">
                            <li
                                @mousedown.prevent="addTag(name)"
                                :class="i === highlightedIndex ? 'bg-gray-100 dark:bg-gray-700' : ''"
                                class="px-3 py-1.5 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200"
                                x-text="name"
                            ></li>
                        </template>
                    </ul>
                </div>
                <input type="hidden" name="tags" :value="tags.join(',')">
            </div>

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
