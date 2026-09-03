@extends('layouts.app')

@section('content')
    @if (session('status') === 'link-saved')
        <div class="status">Saved.</div>
    @elseif (session('status') === 'link-deleted')
        <div class="status">Deleted.</div>
    @endif

    <form method="POST" action="{{ route('links.store') }}" class="save-form">
        @csrf
        <div class="save-form-row">
            <input type="url" name="url" placeholder="https://…" value="{{ old('url') }}" required>
        </div>
        @error('url')
            <p class="error">{{ $message }}</p>
        @enderror

        <div class="save-form-row">
            <input type="text" name="description" placeholder="Description (optional)"
                value="{{ old('description') }}">
            <button type="submit" class="btn btn-solid">Save</button>
        </div>
        @error('description')
            <p class="error">{{ $message }}</p>
        @enderror
    </form>

    <form method="GET" action="{{ route('links.index') }}" class="search-row">
        <input type="text" name="q" value="{{ $query }}" placeholder="Search links…">
    </form>

    @forelse ($links as $link)
        @if ($loop->first)
            <div class="link-list">
        @endif

        <div class="link-item">
            <a class="url" href="{{ $link->url }}" target="_blank" rel="noopener noreferrer">
                {{ $link->displayUrl() }}
            </a>
            @if ($link->description)
                <p class="description">{{ $link->description }}</p>
            @endif
            <div class="meta">
                <span class="date">{{ $link->created_at->format('j M Y') }}</span>
                <form method="POST" action="{{ route('links.destroy', $link) }}"
                    onsubmit="return confirm('Delete this link?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-plain">Delete</button>
                </form>
            </div>
        </div>

        @if ($loop->last)
            </div>
        @endif
    @empty
        <div class="empty">
            {{ $query !== '' ? 'No links match that search.' : "No links saved yet — add your first one above." }}
        </div>
    @endforelse

    @if ($links->hasPages())
        <div class="pagination">
            {{ $links->links() }}
        </div>
    @endif
@endsection
