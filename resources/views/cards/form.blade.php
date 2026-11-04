<form class="stack-form" method="POST" action="{{ isset($card) && $card->exists ? route('cards.update', [$deck, $card]) : route('cards.store') }}">
    @csrf @if(isset($card) && $card->exists) @method('PUT') @endif
    <div class="field"><label for="question">Pertanyaan atau istilah <span>Wajib</span></label><textarea id="question" name="question" rows="3" maxlength="1000" required placeholder="Apa yang ingin Anda ingat?">{{ old('question', $card->question ?? '') }}</textarea></div>
    <div class="field"><label for="answer">Jawaban atau penjelasan <span>Wajib</span></label><textarea id="answer" name="answer" rows="4" maxlength="2000" required placeholder="Tulis jawaban yang jelas dan ringkas.">{{ old('answer', $card->answer ?? '') }}</textarea><p class="field-hint">Pertanyaan maksimal 1.000 karakter. Jawaban maksimal 2.000 karakter.</p></div>
    <div class="field"><label for="deck_id">Kumpulan kartu <span>Wajib</span></label><select id="deck_id" name="deck_id" required>
            <option value="">Pilih kumpulan</option>
            @foreach($decks as $collection)<option value="{{ $collection->id }}" @selected((string) old('deck_id', $card->deck_id ?? $deck->id ?? '') === (string) $collection->id)>{{ $collection->name }}</option>@endforeach
        </select></div>
    <button class="button">{{ isset($card) && $card->exists ? 'Simpan perubahan' : 'Simpan kartu' }} <span aria-hidden="true">↗</span></button>
</form>
