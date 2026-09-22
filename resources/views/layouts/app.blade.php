<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Bookmarks') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.14.1/cdn.min.js"></script>
</head>
<body class="h-full bg-white text-gray-900 dark:bg-gray-950 dark:text-gray-100 antialiased">
    <div class="min-h-full flex flex-col">
        <nav class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-800">
            <a href="{{ route('bookmarks.index') }}" class="font-semibold tracking-tight text-lg">Bookmarks</a>
            @auth
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100">
                        Log out
                    </button>
                </form>
            @endauth
        </nav>

        <main class="flex-1">
            @yield('content')
        </main>
    </div>

    <script>
        // Lets people jump between bookmark cards with the Tab key,
        // Ctrl+Shift+Left/Right, or Cmd+Left/Right — whichever is easiest
        // on their platform. Plain Tab still works everywhere via native
        // browser focus order (every card has tabindex="0"); this just
        // adds the extra shortcuts and wraps focus at the ends.
        document.addEventListener('keydown', function (e) {
            const cards = Array.from(document.querySelectorAll('[data-bookmark-card]'));
            if (cards.length === 0) return;

            const isNext = (e.key === 'ArrowRight' && e.metaKey)
                || (e.key === 'ArrowRight' && e.ctrlKey && e.shiftKey);
            const isPrev = (e.key === 'ArrowLeft' && e.metaKey)
                || (e.key === 'ArrowLeft' && e.ctrlKey && e.shiftKey);

            if (!isNext && !isPrev) return;

            e.preventDefault();

            const active = document.activeElement;
            const currentIndex = cards.indexOf(active);

            const nextIndex = currentIndex === -1
                ? 0
                : isNext
                    ? (currentIndex + 1) % cards.length
                    : (currentIndex - 1 + cards.length) % cards.length;

            cards[nextIndex].focus();
        });
    </script>
</body>
</html>