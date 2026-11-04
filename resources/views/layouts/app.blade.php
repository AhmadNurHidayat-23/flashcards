<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Buat kartu, ulangi materi, dan uji ingatanmu bersama Flashcards.">
    <title>@yield('title', 'Belajar dengan Flashcards.') — Flashcards</title>
    <link rel="icon" href="data:,">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <a class="skip-link" href="#konten">Lewati ke konten</a>
    <div class="page-shell">
        <header class="site-header">
            <a class="wordmark" href="{{ route('decks.index') }}">Flashcards<span aria-hidden="true" class="brand-mark"></span></a>
            <nav aria-label="Navigasi utama"><a class="nav-link" href="{{ route('decks.index') }}">Koleksi saya</a>
                <a class="button" href="{{ route('cards.create') }}">Buat Kartu <span aria-hidden="true">↗</span></a></nav>
        </header>
        <main id="konten">
            @if(session('status'))<div class="notice" role="status">{{ session('status') }}</div>@endif
            @yield('content')
        </main>
        <footer class="site-footer"><span>Flashcards — Satu kartu, satu langkah lebih paham.</span><span>Belajar sedikit. Ingat lebih banyak.</span></footer>
    </div>
</body>
</html>
