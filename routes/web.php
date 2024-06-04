<?php

use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/dashboard', function () {
    return view('dashboard', [
        "users" => User::whereNot('id', auth()->id())->get()
    ]);
})->middleware(['auth'])->name('dashboard');


Route::get('/chat/{friend}', function (User $friend) {
    return view('chat', [
        "friend" => $friend
    ]);
})->middleware(['auth'])->name('chat');

Route::get('/messages/{friend}', function (User $friend) {
    return ChatMessage::query()
        ->where(function($query) use($friend) {
            $query->where('sender_id', auth()->id())
                ->where('receiver_id', $friend->id);
        })
        ->orWhere(function($query) use($friend) {
            $query->where('sender_id', $friend->id)
                ->where('receiver_id', auth()->id());
        })
        ->with(['sender', 'receiver'])
        ->orderBy('id', 'asc')
        ->get();
})->middleware(['auth'])->name('messages');

require __DIR__.'/auth.php';
