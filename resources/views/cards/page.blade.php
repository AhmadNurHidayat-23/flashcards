@extends('layouts.app')
@section('title', $card->exists ? 'Edit kartu' : 'Buat kartu')
@section('content')
    <a class="back-link" href="{{ isset($deck) ? route('decks.show', $deck) : route('decks.index') }}">← Kembali</a>
    <div class="form-page"><header><p class="eyebrow">SATU KARTU, SATU GAGASAN</p><h1>{{ $card->exists ? 'Perjelas pemahaman.' : 'Tanya di depan. Jawab di belakang.' }}</h1><p>Kartu yang ringkas membuat materi lebih mudah diingat.</p></header>
        <div>@include('partials.errors')
            @if($decks->isEmpty())<div class="empty-note"><h2>Siapkan kumpulan dahulu.</h2><p>Setiap kartu perlu sebuah kumpulan agar materi tetap tertata.</p><a class="button" href="{{ route('decks.create') }}">Buat kumpulan <span aria-hidden="true">↗</span></a></div>
            @else @include('cards.form') @endif
        </div>
    </div>
@endsection
