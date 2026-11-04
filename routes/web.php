<?php

use App\Http\Controllers\CardController;
use App\Http\Controllers\DeckController;
use App\Http\Controllers\PracticeController;
use App\Http\Middleware\LocalOnly;
use Illuminate\Support\Facades\Route;

Route::middleware(LocalOnly::class)->scopeBindings()->group(function () {
    Route::get('/', [DeckController::class, 'index'])->name('decks.index');
    Route::resource('kumpulan', DeckController::class)->parameters(['kumpulan' => 'deck'])
        ->names('decks')->except('index');
    Route::get('/kartu/buat', [CardController::class, 'create'])->name('cards.create');
    Route::post('/kartu', [CardController::class, 'store'])->name('cards.store');
    Route::get('/kumpulan/{deck}/kartu/{card}/edit', [CardController::class, 'edit'])->name('cards.edit');
    Route::put('/kumpulan/{deck}/kartu/{card}', [CardController::class, 'update'])->name('cards.update');
    Route::delete('/kumpulan/{deck}/kartu/{card}', [CardController::class, 'destroy'])->name('cards.destroy');
    Route::post('/kumpulan/{deck}/latihan', [PracticeController::class, 'store'])->name('practices.store');
    Route::get('/latihan/{practice}', [PracticeController::class, 'show'])->name('practices.show');
    Route::get('/latihan/{practice}/data', [PracticeController::class, 'state'])->name('practices.state');
    Route::put('/latihan/{practice}/jawaban', [PracticeController::class, 'answer'])->name('practices.answer');
    Route::post('/latihan/{practice}/acak', [PracticeController::class, 'shuffle'])->name('practices.shuffle');
    Route::post('/latihan/{practice}/selesai', [PracticeController::class, 'finish'])->name('practices.finish');
    Route::get('/latihan/{practice}/hasil', [PracticeController::class, 'result'])->name('practices.result');
    Route::post('/latihan/{practice}/ulangi', [PracticeController::class, 'retry'])->name('practices.retry');
});
