<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeckRequest;
use App\Models\Deck;
use App\Services\PracticeSession;

class DeckController extends Controller
{
    public function index()
    {
        return view('decks.index', ['decks' => Deck::withCount('cards')->latest('updated_at')
            ->orderByDesc('id')->paginate(12)]);
    }

    public function create()
    {
        return view('decks.form', ['deck' => new Deck]);
    }

    public function store(DeckRequest $request)
    {
        $deck = Deck::create($request->validated());

        return to_route('decks.show', $deck)->with('status', 'Kumpulan dibuat. Tambahkan kartu pertama Anda.');
    }

    public function show(Deck $deck, PracticeSession $sessions)
    {
        $practices = $deck->practices()->latest('id')->paginate(8, ['*'], 'latihan')->withQueryString();

        return view('decks.show', ['deck' => $deck,
            'cards' => $deck->cards()->orderBy('id')->paginate(12, ['*'], 'kartu')->withQueryString(),
            'cardCount' => $deck->cards()->count(), 'decks' => Deck::orderBy('name')->get(),
            'practices' => $practices, 'sessions' => $sessions]);
    }

    public function edit(Deck $deck)
    {
        return view('decks.form', compact('deck'));
    }

    public function update(DeckRequest $request, Deck $deck)
    {
        $deck->update($request->validated());

        return to_route('decks.show', $deck)->with('status', 'Kumpulan berhasil diperbarui.');
    }

    public function destroy(Deck $deck)
    {
        $deck->delete();

        return to_route('decks.index')->with('status', 'Kumpulan, kartu, dan riwayat latihannya telah dihapus.');
    }
}
