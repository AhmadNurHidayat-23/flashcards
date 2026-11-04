<section class="history" aria-labelledby="history-title"><div class="section-heading"><h2 id="history-title">Riwayat latihan</h2><span class="eyebrow">PROSES YANG TERCATAT</span></div>
    @forelse($practices as $past)
        @php($summary = $sessions->stats($past))
        <article class="history-row"><div><time datetime="{{ $past->created_at->toIso8601String() }}">{{ $past->created_at->translatedFormat('d M Y · H:i') }}</time>
                <p>{{ $past->finished_at ? 'Selesai' : 'Berlangsung' }} · {{ $summary['reviewed'] }}/{{ $summary['total'] }} kartu dijawab</p></div>
            <div><strong>{{ $summary['mastery'] }}%</strong><a href="{{ $past->finished_at ? route('practices.result', $past) : route('practices.show', $past) }}">{{ $past->finished_at ? 'Lihat hasil ↗' : 'Lanjutkan ↗' }}</a></div>
        </article>
    @empty<p class="history-empty">Belum ada latihan. Mulai belajar untuk melihat perkembangan Anda.</p>@endforelse
    @include('partials.pagination', ['items' => $practices, 'label' => 'Halaman riwayat latihan'])
</section>
