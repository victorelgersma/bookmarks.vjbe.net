<?php

namespace App\Http\Controllers;

use App\Models\Link;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LinkController extends Controller
{
    public function index(Request $request): View
    {
        $query = trim((string) $request->query('q', ''));

        $links = $request->user()->links()
            ->when($query !== '', function ($builder) use ($query) {
                $builder->where(function ($sub) use ($query) {
                    $sub->where('description', 'like', "%{$query}%")
                        ->orWhere('url', 'like', "%{$query}%");
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('links.index', [
            'links' => $links,
            'query' => $query,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'url' => ['required', 'url', 'max:2048'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        // A bare URL with no protocol (e.g. "example.com") fails the
        // `url` rule outright rather than silently mis-saving, so people
        // get a clear validation error instead of a dead link.

        $request->user()->links()->create($validated);

        return redirect()->route('links.index')->with('status', 'link-saved');
    }

    public function destroy(Request $request, Link $link): RedirectResponse
    {
        abort_unless($link->user_id === $request->user()->id, 404);

        $link->delete();

        return redirect()->route('links.index')->with('status', 'link-deleted');
    }
}
