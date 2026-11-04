<?php

namespace App\Services;

use App\Models\Deck;
use App\Models\Practice;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PracticeSession
{
    public function start(Deck $deck, ?array $cards = null): Practice
    {
        $cards ??= $deck->cards()->orderBy('id')->get(['id', 'question', 'answer'])->toArray();
        if ($cards === []) {
            throw ValidationException::withMessages(['cards' => 'Tambahkan setidaknya satu kartu sebelum memulai latihan.']);
        }

        return $deck->practices()->create(['cards' => array_values($cards), 'answers' => []]);
    }

    public function answer(Practice $practice, int $cardId, bool $mastered): Practice
    {
        return DB::transaction(function () use ($practice, $cardId, $mastered) {
            $practice = $this->editable($practice);
            if (!in_array($cardId, array_column($practice->cards, 'id'), true)) {
                throw ValidationException::withMessages(['card_id' => 'Kartu ini tidak termasuk dalam sesi latihan.']);
            }
            $answers = $practice->answers;
            $answers[$cardId] = $mastered;
            $practice->update(['answers' => $answers]);

            return $practice;
        });
    }

    public function shuffle(Practice $practice): Practice
    {
        return DB::transaction(function () use ($practice) {
            $practice = $this->editable($practice);
            $cards = Arr::shuffle($practice->cards);
            if (count($cards) > 1 && $cards === $practice->cards) {
                $cards[] = array_shift($cards);
            }
            $practice->update(['cards' => $cards]);

            return $practice;
        });
    }

    public function finish(Practice $practice): Practice
    {
        return DB::transaction(function () use ($practice) {
            $locked = Practice::query()->lockForUpdate()->findOrFail($practice->id);
            if (!$locked->finished_at) $locked->update(['finished_at' => now()]);

            return $locked;
        });
    }

    public function retry(Practice $practice): Practice
    {
        abort_unless($practice->finished_at, 409, 'Selesaikan latihan terlebih dahulu.');
        $remaining = array_filter($practice->cards,
            fn (array $card) => ($practice->answers[$card['id']] ?? false) !== true);

        return $this->start($practice->deck, array_values($remaining));
    }

    public function stats(Practice $practice): array
    {
        $total = count($practice->cards);
        $reviewed = count($practice->answers);
        $mastered = count(array_filter($practice->answers, fn ($value) => $value === true));

        return ['total' => $total, 'reviewed' => $reviewed, 'mastered' => $mastered,
            'unmastered' => $reviewed - $mastered, 'unanswered' => $total - $reviewed,
            'mastery' => $total ? (int) round($mastered / $total * 100) : 0];
    }

    public function state(Practice $practice): array
    {
        return ['cards' => $practice->cards, 'answers' => (object) $practice->answers,
            'stats' => $this->stats($practice), 'finished' => $practice->finished_at !== null];
    }

    private function editable(Practice $practice): Practice
    {
        $locked = Practice::query()->lockForUpdate()->findOrFail($practice->id);
        abort_if($locked->finished_at, 409, 'Latihan sudah selesai. Mulai sesi baru untuk berlatih kembali.');

        return $locked;
    }
}
