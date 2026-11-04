@extends('layouts.app')
@section('title', $deck->name)
@section('content')
    <a class="back-link" href="{{ route('decks.index') }}">← Koleksi saya</a>
    <section class="deck-heading"><p class="eyebrow">KUMPULAN KARTU BELAJAR</p><h1>{{ $deck->name }}</h1>
        @if($deck->description)<p class="deck-description">{{ $deck->description }}</p>@endif
        <div class="deck-meta"><span>{{ $cardCount }} kartu</span><a href="{{ route('decks.edit', $deck) }}">Edit kumpulan ↗</a>
            @if($cardCount)<form action="{{ route('practices.store', $deck) }}" method="POST">@csrf<button class="button">Mulai belajar <span aria-hidden="true">↗</span></button></form>@endif
        </div>
    </section>
    <div class="deck-layout"><div>
            <section aria-labelledby="cards-title"><div class="section-heading"><h2 id="cards-title">Materi Anda <span class="count">{{ $cardCount }}</span></h2><p class="eyebrow">PERTANYAAN / JAWABAN</p></div>
                @forelse($cards as $item)
                    <article class="card-row"><span class="row-number">{{ str_pad($cards->firstItem() + $loop->index, 2, '0', STR_PAD_LEFT) }}</span>
                        <div><h3>{{ $item->question }}</h3><p>{{ $item->answer }}</p>
                            <div class="secondary-actions"><a href="{{ route('cards.edit', [$deck, $item]) }}" aria-label="Edit kartu {{ $loop->iteration }}">Edit</a>
                                <form method="POST" action="{{ route('cards.destroy', [$deck, $item]) }}" data-confirm="Hapus kartu ini?">@csrf @method('DELETE')<button class="text-button" aria-label="Hapus kartu {{ $loop->iteration }}">Hapus</button></form></div>
                        </div>
                    </article>
                @empty<div class="empty-note"><p class="eyebrow">KARTU PERTAMA MENUNGGU</p><h3>Mulai dengan satu pertanyaan.</h3><p>Tambahkan pertanyaan dan jawaban melalui formulir. Setelah itu, Anda siap belajar.</p></div>@endforelse
                @include('partials.pagination', ['items' => $cards, 'label' => 'Halaman kartu'])
            </section>
            @include('practices.history')
        </div>
        <aside class="card-editor"><p class="eyebrow">TAMBAHKAN PEMAHAMAN BARU</p><h2>Buat kartu</h2>@include('partials.errors') @include('cards.form')</aside>
    </div>
@endsection
