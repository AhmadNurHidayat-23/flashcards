@extends('layouts.app')
@section('content')
    <section class="home-hero">
        <p class="eyebrow"><span class="small-dash" aria-hidden="true"></span> RUANG UNTUK MENGINGAT.</p>
        <h1>Belajar dengan Flashcards.</h1>
        <div class="hero-bottom"><p>Buat kartu, ulangi materi, dan uji ingatanmu.</p><span aria-hidden="true">TANYA. BALIK. INGAT.</span></div>
    </section>
    <section aria-labelledby="collection-title">
        <div class="section-heading"><h2 id="collection-title">Kumpulan kartu <span class="count">{{ $decks->total() }}</span></h2>
            <a class="text-link" href="{{ route('decks.create') }}">Kumpulan baru <span aria-hidden="true">↗</span></a></div>
        @forelse($decks as $deck)
            <article class="collection-row">
                <span class="row-number" aria-hidden="true">{{ str_pad($decks->firstItem() + $loop->index, 2, '0', STR_PAD_LEFT) }}</span>
                <div class="row-story"><h3><a href="{{ route('decks.show', $deck) }}">{{ $deck->name }}</a></h3>
                    <p>{{ $deck->description ?: 'Satu pertanyaan membuka pemahaman baru.' }}</p></div>
                <div class="row-meta"><strong>{{ $deck->cards_count }} kartu</strong><span>Diperbarui {{ $deck->updated_at->translatedFormat('d M Y') }}</span></div>
                <div class="row-actions">
                    @if($deck->cards_count)
                        <form action="{{ route('practices.store', $deck) }}" method="POST">@csrf<button class="text-button start-link" aria-label="Mulai belajar {{ $deck->name }}">Mulai belajar ↗</button></form>
                    @else<a class="text-link" href="{{ route('decks.show', $deck) }}">Tambah kartu ↗</a>@endif
                    <div class="secondary-actions"><a href="{{ route('decks.show', $deck) }}">Kelola</a>
                        <a href="{{ route('decks.edit', $deck) }}" aria-label="Edit {{ $deck->name }}">Edit</a>
                        <form method="POST" action="{{ route('decks.destroy', $deck) }}" data-confirm="Hapus kumpulan ini beserta kartu dan riwayat latihannya?">@csrf @method('DELETE')
                            <button class="text-button" aria-label="Hapus {{ $deck->name }}">Hapus</button></form></div>
                </div>
            </article>
        @empty
            <div class="empty-collection"><div class="empty-card" aria-hidden="true"><span>?</span><span>↻</span></div>
                <div><p class="eyebrow">PEMAHAMAN DIMULAI DARI RASA INGIN TAHU</p><h3>Apa yang ingin<br>Anda ingat hari ini?</h3>
                    <p>Kumpulkan pertanyaan, temukan jawaban, dan latih ingatan.<br>Mulai dari kumpulan kartu pertama Anda.</p>
                    <a class="button" href="{{ route('decks.create') }}">Buat kumpulan pertama <span aria-hidden="true">↗</span></a></div>
            </div>
        @endforelse
        @include('partials.pagination', ['items' => $decks, 'label' => 'Halaman kumpulan'])
    </section>
@endsection
