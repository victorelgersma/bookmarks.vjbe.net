@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-6 py-10" x-data="{
    formOpen: false,
    search: '',
    bookmarks: @js($bookmarks->map(fn ($b) => [
        'id' => $b->id,
        'text' => Str::lower(($b->name ?: '').' '.$b->displayUrl().' '.$b->tags->pluck('name')->implode(' ')),
    ])),
    get visibleCount() {
        const q = this.search.trim().toLowerCase();
        return this.bookmarks.filter(b => q === '' || b.text.includes(q)).length;
    }
}">

    @if (session('status') === 'bookmark-saved')
        <div class="mb-4 text-sm text-green-600 dark:text-green-400">Saved.</div>
    @elseif (session('status') === 'bookmark-updated')
        <div class="mb-4 text-sm text-green-600 dark:text-green-400">Updated.</div>
    @elseif (session('status') === 'bookmark-deleted')
        <div class="mb-4 text-sm text-green-600 dark:text-green-400">Deleted.</div>
    @endif

    {{-- Big, central search — filters instantly as you type, no reload --}}
    <div class="mb-10">
        <input
            type="text"
            x-model="search"
            placeholder="Search your bookmarks…"
            class="w-full text-2xl sm:text-3xl font-medium text-center bg-transparent border-0 border-b-2 border-gray-200 dark:border-gray-800 focus:border-gray-900 dark:focus:border-gray-100 focus:ring-0 outline-none px-1 py-4 placeholder:text-gray-300 dark:placeholder:text-gray-700 transition-colors"
            autofocus
        >
    </div>

    <div class="flex items-center justify-between mb-6">
        <p class="text-sm text-gray-500 dark:text-gray-400">
            <span x-text="visibleCount"></span>
            <span x-text="visibleCount === 1 ? 'bookmark' : 'bookmarks'"></span>
        </p>

        <button
            type="button"
            @click="formOpen = !formOpen"
            class="text-sm font-medium px-4 py-2 rounded-full bg-gray-900 text-white hover:bg-gray-700 dark:bg-gray-100 dark:text-gray-900 dark:hover:bg-white transition-colors"
        >
            <span x-show="!formOpen">+ New Bookmark</span>
            <span x-show="formOpen" x-cloak>Cancel</span>
        </button>
    </div>

    {{-- New bookmark form — hidden until "New Bookmark" is pressed --}}
    <div x-show="formOpen" x-cloak x-transition class="mb-10 p-5 rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-900">
        <form method="POST" action="{{ route('bookmarks.store') }}" class="grid gap-3 sm:grid-cols-2">
            @csrf
            <input type="url" name="url" placeholder="https://…" value="{{ old('url') }}" required
                class="px-3 py-2 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 focus:ring-gray-900 dark:focus:ring-gray-100">
            <input type="text" name="name" placeholder="Name (optional)" value="{{ old('name') }}"
                class="px-3 py-2 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 focus:ring-gray-900 dark:focus:ring-gray-100">

            {{-- Tag chip input: typing a comma or pressing Enter turns the
                 current text into a chip immediately; backspace on an empty
                 field removes the last chip. The hidden input carries the
                 comma-joined list under the same "tags" name the controller
                 already parses. --}}
            <div
                x-data="tagInput([])"
                class="sm:col-span-2 flex flex-wrap items-center gap-1.5 rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-2 py-1.5 focus-within:ring-2 focus-within:ring-gray-900 dark:focus-within:ring-gray-100"
            >
                <template x-for="(tag, index) in tags" :key="tag">
                    <span class="inline-flex items-center gap-1 text-xs pl-2 pr-1 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                        <span x-text="tag"></span>
                        <button type="button" @click="removeTag(index)" class="text-gray-400 hover:text-gray-700 dark:hover:text-gray-100" aria-label="Remove tag">&times;</button>
                    </span>
                </template>
                <input
                    type="text"
                    x-model="draft"
                    @keydown.enter.prevent="addTag()"
                    @keydown.comma.prevent="addTag()"
                    @keydown.backspace="removeLastTag()"
                    @blur="addTag()"
                    placeholder="Tags…"
                    class="flex-1 min-w-[6rem] bg-transparent border-0 p-0 text-sm focus:ring-0 dark:text-gray-100 placeholder:text-gray-400 dark:placeholder:text-gray-600"
                >
                <input type="hidden" name="tags" :value="tags.join(',')">
            </div>

            @error('url')<p class="text-sm text-red-600 sm:col-span-2">{{ $message }}</p>@enderror
            @error('name')<p class="text-sm text-red-600 sm:col-span-2">{{ $message }}</p>@enderror
            @error('tags')<p class="text-sm text-red-600 sm:col-span-2">{{ $message }}</p>@enderror

            <button type="submit" class="sm:col-span-2 justify-self-start px-4 py-2 rounded-lg bg-gray-900 text-white text-sm font-medium hover:bg-gray-700 dark:bg-gray-100 dark:text-gray-900 dark:hover:bg-white transition-colors">
                Save bookmark
            </button>
        </form>
    </div>

    {{-- Grid of cards. All are rendered; matching search just toggles visibility --}}
    @if ($bookmarks->isEmpty())
        <div class="text-center text-gray-400 dark:text-gray-600 py-20">
            No bookmarks yet — add your first one above.
        </div>
    @else
        <div x-show="visibleCount === 0" x-cloak class="text-center text-gray-400 dark:text-gray-600 py-20">
            No bookmarks match that search.
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($bookmarks as $bookmark)
                @include('bookmarks._card', ['bookmark' => $bookmark])
            @endforeach
        </div>
    @endif
</div>
@endsection
