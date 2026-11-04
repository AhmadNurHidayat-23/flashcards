<?php

namespace App\Http\Controllers;

use App\Http\Requests\CardRequest;
use App\Models\Card;
use App\Models\Deck;

class CardController extends Controller
{
    public function create()
    {
        return view('cards.page', ['card' => new Card, 'decks' => Deck::orderBy('name')->get()]);
    }

    public function store(CardRequest $request)
    {
        $card = Card::create($request->validated());

        return to_route('decks.show', $card->deck_id)->with('status', 'Kartu berhasil ditambahkan.');
    }

    public function edit(Deck $deck, Card $card)
    {
        return view('cards.page', ['card' => $card, 'decks' => Deck::orderBy('name')->get(), 'deck' => $deck]);
    }

    public function update(CardRequest $request, Deck $deck, Card $card)
    {
        $card->update($request->validated());
        $deck->touch();

        return to_route('decks.show', $card->deck_id)->with('status', 'Kartu berhasil diperbarui.');
    }

    public function destroy(Deck $deck, Card $card)
    {
        $card->delete();
        $deck->touch();

        return to_route('decks.show', $deck)->with('status', 'Kartu telah dihapus.');
    }
}
