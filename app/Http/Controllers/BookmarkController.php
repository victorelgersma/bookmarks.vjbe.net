<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BookmarkController extends Controller
{
    public function index(Request $request): View
    {
        $bookmarks = $request->user()->bookmarks()
            ->with('tags')
            ->inRandomOrder()
            ->get();

        $availableTags = $request->user()->tags()
            ->orderBy('name')
            ->pluck('name');

        return view('bookmarks.index', [
            'bookmarks' => $bookmarks,
            'availableTags' => $availableTags,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'url' => ['required', 'url', 'max:2048'],
            'name' => ['nullable', 'string', 'max:1000'],
            'tags' => ['nullable', 'string', 'max:1000'],
        ]);

        $bookmark = $request->user()->bookmarks()->create([
            'url' => $validated['url'],
            'name' => $validated['name'] ?? null,
        ]);

        $this->syncTags($request, $bookmark);

        return redirect()->route('bookmarks.index')->with('status', 'bookmark-saved');
    }

    public function update(Request $request, Bookmark $bookmark): RedirectResponse
    {
        abort_unless($bookmark->user_id === $request->user()->id, 404);

        $validated = $request->validate([
            'url' => ['required', 'url', 'max:2048'],
            'name' => ['nullable', 'string', 'max:1000'],
            'tags' => ['nullable', 'string', 'max:1000'],
        ]);

        $bookmark->update([
            'url' => $validated['url'],
            'name' => $validated['name'] ?? null,
        ]);

        $this->syncTags($request, $bookmark);

        return redirect()->route('bookmarks.index')->with('status', 'bookmark-updated');
    }

    public function destroy(Request $request, Bookmark $bookmark): RedirectResponse
    {
        abort_unless($bookmark->user_id === $request->user()->id, 404);

        $bookmark->delete();

        return redirect()->route('bookmarks.index')->with('status', 'bookmark-deleted');
    }

    /**
     * Splits the comma-separated "tags" input into individual tag names,
     * finds-or-creates each one scoped to the current user, and syncs
     * them onto the bookmark. Blank/duplicate names are dropped.
     */
    private function syncTags(Request $request, Bookmark $bookmark): void
    {
        $names = collect(explode(',', (string) $request->input('tags', '')))
            ->map(fn ($name) => Str::lower(trim($name)))
            ->filter()
            ->unique();

        $tagIds = $names->map(
            fn ($name) => Tag::firstOrCreate(
                ['user_id' => $request->user()->id, 'name' => $name]
            )->id
        );

        $bookmark->tags()->sync($tagIds);
    }
}
