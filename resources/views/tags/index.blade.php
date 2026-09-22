@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-6 py-10">
    <h1 class="text-2xl font-semibold mb-6">Tags</h1>

    @if ($tags->isEmpty())
        <div class="text-center text-gray-400 dark:text-gray-600 py-20">
            No tags yet — add some tags to your bookmarks.
        </div>
    @else
        <div class="flex flex-wrap gap-3">
            @foreach ($tags as $tag)
                <a
                    href="{{ route('tags.show', $tag) }}"
                    class="flex items-center gap-2 px-4 py-2 rounded-full border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 hover:border-gray-900 dark:hover:border-gray-100 transition-colors"
                >
                    <span class="font-medium">{{ $tag->name }}</span>
                    <span class="text-xs text-gray-400 dark:text-gray-600">{{ $tag->bookmarks_count }}</span>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
