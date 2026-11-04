<?php

namespace Tests\Feature;

use App\Models\Card;
use App\Models\Deck;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CollectionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_empty_states_and_create_forms(): void
    {
        $this->get('/')->assertOk()->assertSee('Belajar dengan Flashcards.')->assertSee('Buat kumpulan pertama');
        $this->get('/kumpulan/create')->assertOk()->assertSee('Nama kumpulan');
        $this->get('/kartu/buat')->assertOk()->assertSee('Siapkan kumpulan dahulu.');
    }

    public function test_deck_crud_persistence_and_cascade(): void
    {
        $this->post('/kumpulan', ['name' => 'Bahasa', 'description' => 'Istilah penting.'])->assertRedirect();
        $deck = Deck::firstOrFail();
        $this->get('/')->assertSee('Bahasa')->assertSee('0 kartu');
        $this->get("/kumpulan/{$deck->id}/edit")->assertOk()->assertSee('Istilah penting.');
        $this->put("/kumpulan/{$deck->id}", ['name' => 'Bahasa Indonesia', 'description' => 'Materi baru.'])->assertRedirect();
        $this->get("/kumpulan/{$deck->id}")->assertSee('Bahasa Indonesia')->assertSee('Materi baru.');
        $this->get("/kumpulan/{$deck->id}")->assertSee('Materi baru.');
        $deck->cards()->create(['question' => 'Apa?', 'answer' => 'Ini.']);
        $this->post("/kumpulan/{$deck->id}/latihan");
        $this->delete("/kumpulan/{$deck->id}")->assertRedirect('/');
        foreach (['decks', 'cards', 'practices'] as $table) $this->assertDatabaseCount($table, 0);
        $this->get("/kumpulan/{$deck->id}")->assertNotFound();
    }

    public function test_cards_can_be_created_edited_moved_and_deleted(): void
    {
        $a = Deck::create(['name' => 'A']);
        $b = Deck::create(['name' => 'B']);
        $this->post('/kartu', ['deck_id' => $a->id, 'question' => 'Pertanyaan pertama', 'answer' => 'Jawaban pertama'])
            ->assertRedirect("/kumpulan/{$a->id}");
        $card = Card::firstOrFail();
        $this->get("/kumpulan/{$a->id}")->assertSee('Pertanyaan pertama')->assertSee('Jawaban pertama');
        $this->get("/kumpulan/{$a->id}/kartu/{$card->id}/edit")->assertOk()->assertSee('Jawaban pertama');
        $this->put("/kumpulan/{$a->id}/kartu/{$card->id}", [
            'deck_id' => $b->id, 'question' => 'Pertanyaan baru', 'answer' => 'Jawaban baru',
        ])->assertRedirect("/kumpulan/{$b->id}");
        $this->get("/kumpulan/{$a->id}")->assertDontSee('Pertanyaan baru');
        $this->get("/kumpulan/{$b->id}")->assertSee('Pertanyaan baru')->assertSee('Jawaban baru');
        $this->get("/kumpulan/{$b->id}")->assertSee('Jawaban baru');
        $this->delete("/kumpulan/{$b->id}/kartu/{$card->id}")->assertRedirect();
        $this->assertDatabaseCount('cards', 0);
    }

    public function test_cards_are_scoped_to_their_collection(): void
    {
        $a = Deck::create(['name' => 'A']);
        $b = Deck::create(['name' => 'B']);
        $card = $a->cards()->create(['question' => 'Milik A', 'answer' => 'A']);
        $url = "/kumpulan/{$b->id}/kartu/{$card->id}";
        $this->get("{$url}/edit")->assertNotFound();
        $this->put($url, ['deck_id' => $b->id, 'question' => 'Ubah', 'answer' => 'Ubah'])->assertNotFound();
        $this->delete($url)->assertNotFound();
        $this->assertDatabaseHas('cards', ['id' => $card->id, 'question' => 'Milik A']);
    }

    public function test_validation_rejects_empty_overlong_and_invalid_collection_values(): void
    {
        $this->post('/kumpulan', ['name' => '   '])->assertSessionHasErrors('name');
        $this->post('/kumpulan', ['name' => str_repeat('x', 121), 'description' => str_repeat('x', 501)])
            ->assertSessionHasErrors(['name', 'description']);
        $this->post('/kartu', [])->assertSessionHasErrors(['deck_id', 'question', 'answer']);
        $this->post('/kartu', ['deck_id' => 999, 'question' => str_repeat('x', 1001), 'answer' => str_repeat('x', 2001)])
            ->assertSessionHasErrors(['deck_id', 'question', 'answer']);
        $this->assertDatabaseCount('decks', 0);
        $this->assertDatabaseCount('cards', 0);
    }

    public function test_user_text_is_escaped(): void
    {
        $deck = Deck::create(['name' => '<script>evil()</script>']);
        $deck->cards()->create(['question' => '<img src=x onerror=evil()>', 'answer' => '<script>evil()</script>']);
        $this->get("/kumpulan/{$deck->id}")->assertSee('&lt;script&gt;evil()&lt;/script&gt;', false)
            ->assertDontSee('<script>evil()</script>', false)->assertDontSee('<img src=x', false);
    }

    public function test_collection_and_card_pagination(): void
    {
        foreach (range(1, 13) as $n) Deck::create(['name' => "Kumpulan {$n}"]);
        $this->get('/')->assertSee('Berikutnya');
        $this->get('/?page=2')->assertOk()->assertSee('Kumpulan 1');
        $deck = Deck::firstOrFail();
        foreach (range(1, 13) as $n) $deck->cards()->create(['question' => "Materi {$n}", 'answer' => "Jawaban {$n}"]);
        $this->get("/kumpulan/{$deck->id}?kartu=2")->assertOk()->assertSee('Materi 13');
    }
}
