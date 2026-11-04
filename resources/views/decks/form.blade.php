@extends('layouts.app')
@section('title', $deck->exists ? 'Edit kumpulan' : 'Buat kumpulan')
@section('content')
    <a class="back-link" href="{{ $deck->exists ? route('decks.show', $deck) : route('decks.index') }}">← Kembali</a>
    <div class="form-page"><header><p class="eyebrow">{{ $deck->exists ? 'RAPIKAN MATERI' : 'MULAI DARI RASA INGIN TAHU' }}</p>
            <h1>{{ $deck->exists ? 'Edit kumpulan.' : 'Satu topik. Banyak pemahaman.' }}</h1><p>Beri tempat untuk materi yang ingin Anda kuasai.</p></header>
        <div>@include('partials.errors')
            <form class="stack-form" method="POST" action="{{ $deck->exists ? route('decks.update', $deck) : route('decks.store') }}">
                @csrf @if($deck->exists) @method('PUT') @endif
                <div class="field"><label for="name">Nama kumpulan <span>Wajib</span></label><input id="name" name="name" maxlength="120" required autofocus value="{{ old('name', $deck->name) }}" placeholder="Misalnya, Dasar-dasar desain"></div>
                <div class="field"><label for="description">Deskripsi singkat <span>Opsional</span></label><textarea id="description" name="description" rows="4" maxlength="500" placeholder="Materi apa yang akan Anda pelajari?">{{ old('description', $deck->description) }}</textarea><p class="field-hint">Maksimal 500 karakter.</p></div>
                <div class="form-actions"><button class="button">{{ $deck->exists ? 'Simpan perubahan' : 'Buat kumpulan' }} <span aria-hidden="true">↗</span></button><a href="{{ $deck->exists ? route('decks.show', $deck) : route('decks.index') }}">Batal</a></div>
            </form>
        </div>
    </div>
@endsection
