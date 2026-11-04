<?php

namespace Tests\Feature;

use App\Models\Deck;
use App\Services\PracticeSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_remote_access_is_rejected_even_with_forged_local_forwarding(): void
    {
        $deck = Deck::create(['name' => 'Pribadi']);
        $card = $deck->cards()->create(['question' => 'Pribadi?', 'answer' => 'Ya']);
        $practice = app(PracticeSession::class)->start($deck);
        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.7'])->withHeader('X-Forwarded-For', '127.0.0.1');
        $this->get('/')->assertForbidden();
        $this->get("/kumpulan/{$deck->id}")->assertForbidden();
        $this->post('/kumpulan', ['name' => 'Intrusi'])->assertForbidden();
        $this->delete("/kumpulan/{$deck->id}")->assertForbidden();
        $this->post('/kartu', ['deck_id' => $deck->id, 'question' => 'X', 'answer' => 'X'])->assertForbidden();
        $this->put("/kumpulan/{$deck->id}/kartu/{$card->id}", [])->assertForbidden();
        $this->delete("/kumpulan/{$deck->id}/kartu/{$card->id}")->assertForbidden();
        $this->post("/kumpulan/{$deck->id}/latihan")->assertForbidden();
        $this->getJson("/latihan/{$practice->id}/data")->assertForbidden();
        $this->putJson("/latihan/{$practice->id}/jawaban", ['card_id' => $card->id, 'mastered' => true])->assertForbidden();
        $this->post("/latihan/{$practice->id}/selesai")->assertForbidden();
        $this->assertDatabaseCount('decks', 1);
        $this->assertSame([], $practice->fresh()->answers);
    }

    public function test_real_csrf_protection_rejects_mutations_without_a_valid_token(): void
    {
        $this->app['env'] = 'local';
        $this->post('/kumpulan', ['name' => 'Tanpa token'])->assertStatus(419);
        $this->assertDatabaseCount('decks', 0);
        $this->withSession(['_token' => 'valid-test-token'])->post('/kumpulan', [
            'name' => 'Dengan token', '_token' => 'valid-test-token',
        ])->assertRedirect();
        $this->assertDatabaseCount('decks', 1);
    }
}
