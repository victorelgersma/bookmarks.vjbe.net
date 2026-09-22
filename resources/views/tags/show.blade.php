@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-6 py-10" x-data="{ search: '' }">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('tags.index') }}" class="text-sm text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
            ← Tags
        </a>
        <h1 class="text-2xl font-semibold">{{ $tag->name }}</h1>
    </div>

    @if ($bookmarks->isEmpty())
        <div class="text-center text-gray-400 dark:text-gray-600 py-20">
            No bookmarks tagged "{{ $tag->name }}" yet.
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($bookmarks as $bookmark)
                @include('bookmarks._card', ['bookmark' => $bookmark])
            @endforeach
        </div>
    @endif
</div>
@endsection
