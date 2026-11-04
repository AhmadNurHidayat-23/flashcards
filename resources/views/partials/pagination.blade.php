@if($items->hasPages())
    <nav class="pagination" aria-label="{{ $label }}">
        @if($items->onFirstPage())<span>Sebelumnya</span>@else<a href="{{ $items->previousPageUrl() }}">← Sebelumnya</a>@endif
        <span>{{ $items->currentPage() }} / {{ $items->lastPage() }}</span>
        @if($items->hasMorePages())<a href="{{ $items->nextPageUrl() }}">Berikutnya →</a>@else<span>Berikutnya</span>@endif
    </nav>
@endif
