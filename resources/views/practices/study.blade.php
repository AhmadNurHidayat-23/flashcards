@extends('layouts.app')
@section('title', 'Latihan — '.$practice->deck->name)
@section('content')
    <a class="back-link" href="{{ route('decks.show', $practice->deck) }}">← Kembali ke kumpulan</a>
    <section class="study" data-study data-state-url="{{ route('practices.state', $practice) }}" data-answer-url="{{ route('practices.answer', $practice) }}" data-shuffle-url="{{ route('practices.shuffle', $practice) }}" data-result-url="{{ route('practices.result', $practice) }}">
        <header class="study-header"><div><p class="eyebrow">MODE LATIHAN</p><h1>{{ $practice->deck->name }}</h1></div><button class="text-button" type="button" data-shuffle disabled>Acak urutan ↻</button></header>
        <div class="study-progress"><div><p data-position>Kartu 1 dari {{ $stats['total'] }}</p><p data-progress-text>{{ $stats['reviewed'] }} dari {{ $stats['total'] }} kartu dijawab</p></div>
            <progress data-progress value="{{ $stats['reviewed'] }}" max="{{ $stats['total'] }}" aria-label="Progres kartu yang telah dijawab"></progress></div>
        <p class="study-status" data-study-status role="status" aria-live="polite">Menyiapkan kartu…</p>
        <button class="flashcard" data-flip type="button" aria-label="Balik kartu untuk melihat jawaban" aria-pressed="false" disabled>
            <span class="flip-inner"><span class="card-face card-front" data-front><span class="eyebrow">PERTANYAAN</span><span class="card-content" data-question>{{ $practice->cards[0]['question'] }}</span><span class="flip-hint">Klik untuk melihat jawaban <span aria-hidden="true">↻</span></span></span>
                <span class="card-face card-back" data-back aria-hidden="true"><span class="eyebrow">JAWABAN</span><span class="card-content" data-answer></span><span class="flip-hint">Klik untuk kembali ke pertanyaan <span aria-hidden="true">↻</span></span></span></span>
        </button>
        <div class="answer-actions" data-answer-actions hidden><p>Seberapa baik Anda mengingatnya?</p><div>
                <button class="button" type="button" data-mark="false">Belum Hafal <span aria-hidden="true">↻</span></button><button class="button active-button" type="button" data-mark="true">Sudah Hafal <span aria-hidden="true">✓</span></button></div></div>
        <div class="study-navigation"><button class="text-button" type="button" data-previous disabled>← Sebelumnya</button><span class="eyebrow">TANYA. BALIK. INGAT.</span><button class="text-button" type="button" data-next disabled>Berikutnya →</button></div>
        <div class="study-footer"><p>Spasi / Enter: balik kartu <span>·</span> ← / →: navigasi</p><form method="POST" action="{{ route('practices.finish', $practice) }}" data-finish>@csrf<button class="text-button" disabled>Selesaikan Latihan ↗</button></form></div>
        <noscript><div class="notice">Aktifkan JavaScript di browser untuk membalik kartu dan menjalankan latihan.</div></noscript>
    </section>
@endsection
