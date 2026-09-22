<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookmarkController extends Controller
{
    public function index(Request $request): View
    {
        $query = trim((string) $request->query('q', ''));

        $bookmarks = $request->user()->bookmarks()
            ->when($query !== '', function ($builder) use ($query) {
                $builder->where(function ($sub) use ($query) {
                    $sub->where('name', 'like', "%{$query}%")
                        ->orWhere('url', 'like', "%{$query}%");
                });
            })
            ->inRandomOrder()
            ->get();

        return view('bookmarks.index', [
            'bookmarks' => $bookmarks,
            'query' => $query,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'url' => ['required', 'url', 'max:2048'],
            'name' => ['nullable', 'string', 'max:1000'],
        ]);

        $request->user()->bookmarks()->create($validated);

        return redirect()->route('bookmarks.index')->with('status', 'bookmark-saved');
    }

    public function update(Request $request, Bookmark $bookmark): RedirectResponse
    {
        abort_unless($bookmark->user_id === $request->user()->id, 404);

        $validated = $request->validate([
            'url' => ['required', 'url', 'max:2048'],
            'name' => ['nullable', 'string', 'max:1000'],
        ]);

        $bookmark->update($validated);

        return redirect()->route('bookmarks.index')->with('status', 'bookmark-updated');
    }

    public function destroy(Request $request, Bookmark $bookmark): RedirectResponse
    {
        abort_unless($bookmark->user_id === $request->user()->id, 404);

        $bookmark->delete();

        return redirect()->route('bookmarks.index')->with('status', 'bookmark-deleted');
    }
}
