@extends('layouts.app')
@section('title', 'Hasil latihan')
@section('content')
    <a class="back-link" href="{{ route('decks.show', $practice->deck) }}">← Kembali ke kumpulan</a>
    <section class="result"><p class="eyebrow">LATIHAN SELESAI · {{ $practice->finished_at->translatedFormat('d M Y · H:i') }}</p>
        <h1>Setiap pengulangan<br>membawa pemahaman.</h1><p class="result-deck">{{ $practice->deck->name }}</p>
        <div class="result-overview"><div><strong class="mastery-number">{{ $stats['mastery'] }}<span>%</span></strong><p>Penguasaan dari {{ $stats['total'] }} kartu dalam sesi ini.</p></div>
            <dl class="result-stats"><div><dt>Kartu dipelajari</dt><dd>{{ $stats['reviewed'] }} <span>/ {{ $stats['total'] }}</span></dd></div><div><dt>Sudah hafal</dt><dd>{{ $stats['mastered'] }}</dd></div><div><dt>Belum hafal</dt><dd>{{ $stats['unmastered'] }}</dd></div><div><dt>Belum dijawab</dt><dd>{{ $stats['unanswered'] }}</dd></div></dl></div>
        @include('partials.errors')
        <div class="result-actions">
            @if($stats['mastered'] < $stats['total'])<form method="POST" action="{{ route('practices.retry', $practice) }}">@csrf<button class="button active-button">Ulangi yang Belum Hafal <span aria-hidden="true">↻</span></button></form>@endif
            <form method="POST" action="{{ route('practices.store', $practice->deck) }}">@csrf<button class="button">Mulai Lagi <span aria-hidden="true">↗</span></button></form>
        </div>
        <p class="result-note">{{ $stats['unanswered'] ? 'Pengulangan juga menyertakan kartu yang belum dijawab. ' : '' }}Hasil ini tersimpan dalam riwayat kumpulan. Memulai ulang tidak menghapus kartu atau riwayat latihan.</p>
    </section>
@endsection
