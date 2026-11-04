<?php

namespace Tests\Feature;

use App\Models\Deck;
use App\Models\Practice;
use App\Services\PracticeSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PracticeTest extends TestCase
{
    use RefreshDatabase;

    private function start(int $count = 3): Practice
    {
        $deck = Deck::create(['name' => 'Dasar desain']);
        foreach (range(1, $count) as $n) $deck->cards()->create(['question' => "Pertanyaan {$n}", 'answer' => "Jawaban {$n}"]);
        $this->post("/kumpulan/{$deck->id}/latihan")->assertRedirect();

        return $deck->practices()->firstOrFail();
    }

    public function test_empty_decks_cannot_start_a_session(): void
    {
        $deck = Deck::create(['name' => 'Kosong']);
        $this->post("/kumpulan/{$deck->id}/latihan")->assertSessionHasErrors('cards');
        $this->assertDatabaseCount('practices', 0);
    }

    public function test_start_creates_persistent_snapshot_and_zero_progress(): void
    {
        $practice = $this->start();
        $this->get("/latihan/{$practice->id}")->assertOk()->assertSee('Pertanyaan 1');
        $this->getJson("/latihan/{$practice->id}/data")->assertOk()->assertJsonCount(3, 'cards')
            ->assertJsonPath('stats.reviewed', 0)->assertJsonPath('stats.mastered', 0)
            ->assertJsonPath('stats.unanswered', 3)->assertJsonPath('stats.mastery', 0);
        $card = $practice->deck->cards()->firstOrFail();
        $card->update(['question' => 'Diubah', 'answer' => 'Diubah']);
        $card->delete();
        $this->getJson("/latihan/{$practice->id}/data")->assertJsonPath('cards.0.question', 'Pertanyaan 1')
            ->assertJsonCount(3, 'cards');
    }

    public function test_answers_are_persistent_idempotent_and_can_be_revised(): void
    {
        $practice = $this->start();
        $url = "/latihan/{$practice->id}/jawaban";
        $id = $practice->cards[0]['id'];
        $this->putJson($url, ['card_id' => $id, 'mastered' => true])->assertOk()
            ->assertJsonPath('stats.reviewed', 1)->assertJsonPath('stats.mastered', 1)->assertJsonPath('stats.mastery', 33);
        $this->putJson($url, ['card_id' => $id, 'mastered' => true])->assertJsonPath('stats.reviewed', 1);
        $this->putJson($url, ['card_id' => $id, 'mastered' => false])->assertJsonPath('stats.reviewed', 1)
            ->assertJsonPath('stats.mastered', 0)->assertJsonPath('stats.unmastered', 1);
        $this->getJson("/latihan/{$practice->id}/data")->assertJsonPath("answers.{$id}", false)
            ->assertJsonPath('stats.reviewed', 1);
    }

    public function test_invalid_or_foreign_answers_do_not_change_progress(): void
    {
        $practice = $this->start();
        $other = Deck::create(['name' => 'Lain']);
        $foreign = $other->cards()->create(['question' => 'Asing', 'answer' => 'Asing']);
        $url = "/latihan/{$practice->id}/jawaban";
        $this->putJson($url, [])->assertUnprocessable();
        $this->putJson($url, ['card_id' => $foreign->id, 'mastered' => true])->assertUnprocessable();
        $this->putJson($url, ['card_id' => $practice->cards[0]['id'], 'mastered' => 'false'])->assertUnprocessable();
        $this->assertSame([], $practice->fresh()->answers);
    }

    public function test_shuffle_preserves_answers_membership_and_is_saved(): void
    {
        $practice = $this->start();
        $before = array_column($practice->cards, 'id');
        $id = $before[0];
        $this->putJson("/latihan/{$practice->id}/jawaban", ['card_id' => $id, 'mastered' => true]);
        $this->postJson("/latihan/{$practice->id}/acak")->assertOk()->assertJsonPath('stats.reviewed', 1);
        $after = array_column($practice->fresh()->cards, 'id');
        $this->assertNotSame($before, $after);
        $this->assertEqualsCanonicalizing($before, $after);
        $this->getJson("/latihan/{$practice->id}/data")->assertJsonPath('cards.0.id', $after[0])
            ->assertJsonPath("answers.{$id}", true);
    }

    public function test_results_use_real_answers_and_finished_session_is_immutable(): void
    {
        $practice = $this->start();
        foreach ($practice->cards as $i => $card) {
            $this->putJson("/latihan/{$practice->id}/jawaban", ['card_id' => $card['id'], 'mastered' => $i < 2]);
        }
        $this->post("/latihan/{$practice->id}/selesai")->assertRedirect("/latihan/{$practice->id}/hasil");
        $this->get("/latihan/{$practice->id}/hasil")->assertOk()->assertSee('67')->assertSee('Ulangi yang Belum Hafal');
        $this->assertSame(['total' => 3, 'reviewed' => 3, 'mastered' => 2, 'unmastered' => 1, 'unanswered' => 0, 'mastery' => 67],
            app(PracticeSession::class)->stats($practice->fresh()));
        $finished = $practice->fresh()->finished_at->toIso8601String();
        $this->travel(5)->minutes();
        $this->post("/latihan/{$practice->id}/selesai")->assertRedirect();
        $this->assertSame($finished, $practice->fresh()->finished_at->toIso8601String());
        $this->putJson("/latihan/{$practice->id}/jawaban", ['card_id' => $practice->cards[0]['id'], 'mastered' => false])->assertConflict();
        $this->postJson("/latihan/{$practice->id}/acak")->assertConflict();
        $this->get("/latihan/{$practice->id}")->assertRedirect("/latihan/{$practice->id}/hasil");
        $this->get("/kumpulan/{$practice->deck_id}")->assertSee('67%')->assertSee('Lihat hasil');
    }

    public function test_early_finish_and_retry_include_unknown_and_unanswered_cards(): void
    {
        $practice = $this->start();
        $this->putJson("/latihan/{$practice->id}/jawaban", ['card_id' => $practice->cards[0]['id'], 'mastered' => true]);
        $this->putJson("/latihan/{$practice->id}/jawaban", ['card_id' => $practice->cards[1]['id'], 'mastered' => false]);
        $this->post("/latihan/{$practice->id}/selesai");
        $this->assertSame(33, app(PracticeSession::class)->stats($practice->fresh())['mastery']);
        $this->get("/latihan/{$practice->id}/hasil")->assertSee('kartu yang belum dijawab');
        $this->post("/latihan/{$practice->id}/ulangi")->assertRedirect();
        $retry = Practice::latest('id')->firstOrFail();
        $this->assertCount(2, $retry->cards);
        $this->assertSame([$practice->cards[1]['id'], $practice->cards[2]['id']], array_column($retry->cards, 'id'));
        $this->assertSame([], $retry->answers);
        $this->assertNotNull($practice->fresh()->finished_at);
        $this->assertDatabaseCount('cards', 3);
    }

    public function test_restart_keeps_material_and_history_and_uses_current_cards(): void
    {
        $practice = $this->start(1);
        $this->putJson("/latihan/{$practice->id}/jawaban", ['card_id' => $practice->cards[0]['id'], 'mastered' => true]);
        $this->post("/latihan/{$practice->id}/selesai");
        $this->post("/latihan/{$practice->id}/ulangi")->assertSessionHasErrors('cards');
        $practice->deck->cards()->create(['question' => 'Materi baru', 'answer' => 'Baru']);
        $this->post("/kumpulan/{$practice->deck_id}/latihan")->assertRedirect();
        $new = Practice::latest('id')->firstOrFail();
        $this->assertCount(2, $new->cards);
        $this->assertSame([], $new->answers);
        $this->assertDatabaseCount('cards', 2);
        $this->assertDatabaseCount('practices', 2);
        $this->assertNotNull($practice->fresh()->finished_at);
    }

    public function test_incomplete_results_redirect_to_active_session_and_retry_requires_completion(): void
    {
        $practice = $this->start(1);
        $this->get("/latihan/{$practice->id}/hasil")->assertRedirect("/latihan/{$practice->id}");
        $this->post("/latihan/{$practice->id}/ulangi")->assertConflict();
        $this->post("/latihan/{$practice->id}/selesai");
        $stats = app(PracticeSession::class)->stats($practice->fresh());
        $this->assertSame(0, $stats['reviewed']);
        $this->assertSame(0, $stats['mastery']);
        $this->assertSame(1, $stats['unanswered']);
    }
}
