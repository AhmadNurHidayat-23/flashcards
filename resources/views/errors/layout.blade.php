<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') — Flashcards</title>
    <link rel="icon" href="data:,">
    @vite(['resources/css/app.css'])
</head>
<body><div class="page-shell"><header class="site-header"><span class="wordmark">Flashcards<span class="brand-mark" aria-hidden="true"></span></span></header>
    <main class="home-hero"><p class="eyebrow">@yield('code')</p><h1>@yield('title')</h1><p>@yield('message')</p></main>
</div></body>
</html>
