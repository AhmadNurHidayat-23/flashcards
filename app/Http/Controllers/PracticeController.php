<?php

namespace App\Http\Controllers;

use App\Models\Deck;
use App\Models\Practice;
use App\Services\PracticeSession;
use Illuminate\Http\Request;

class PracticeController extends Controller
{
    public function __construct(private PracticeSession $sessions) {}

    public function store(Deck $deck)
    {
        return to_route('practices.show', $this->sessions->start($deck));
    }

    public function show(Practice $practice)
    {
        if ($practice->finished_at) return to_route('practices.result', $practice);

        return view('practices.study', ['practice' => $practice,
            'stats' => $this->sessions->stats($practice)]);
    }

    public function state(Practice $practice)
    {
        return response()->json($this->sessions->state($practice));
    }

    public function answer(Request $request, Practice $practice)
    {
        $data = $request->validate(['card_id' => ['required', 'integer'], 'mastered' => ['required', 'boolean']]);
        $updated = $this->sessions->answer($practice, (int) $data['card_id'], (bool) $data['mastered']);

        return response()->json($this->sessions->state($updated));
    }

    public function shuffle(Practice $practice)
    {
        return response()->json($this->sessions->state($this->sessions->shuffle($practice)));
    }

    public function finish(Practice $practice)
    {
        $this->sessions->finish($practice);

        return to_route('practices.result', $practice);
    }

    public function result(Practice $practice)
    {
        if (!$practice->finished_at) return to_route('practices.show', $practice);

        return view('practices.result', ['practice' => $practice, 'stats' => $this->sessions->stats($practice)]);
    }

    public function retry(Practice $practice)
    {
        return to_route('practices.show', $this->sessions->retry($practice));
    }
}
