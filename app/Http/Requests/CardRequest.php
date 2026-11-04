<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['deck_id' => ['required', 'integer', 'exists:decks,id'],
            'question' => ['required', 'string', 'max:1000'],
            'answer' => ['required', 'string', 'max:2000']];
    }

    public function attributes(): array
    {
        return ['deck_id' => 'kumpulan kartu', 'question' => 'pertanyaan', 'answer' => 'jawaban'];
    }
}
