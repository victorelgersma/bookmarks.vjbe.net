<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TagController extends Controller
{
    public function index(Request $request): View
    {
        $tags = $request->user()->tags()
            ->withCount('bookmarks')
            ->orderBy('name')
            ->get();

        return view('tags.index', [
            'tags' => $tags,
        ]);
    }

    public function show(Request $request, Tag $tag): View
    {
        abort_unless($tag->user_id === $request->user()->id, 404);

        $bookmarks = $tag->bookmarks()
            ->with('tags')
            ->orderBy('name')
            ->get();

        return view('tags.show', [
            'tag' => $tag,
            'bookmarks' => $bookmarks,
        ]);
    }
}
