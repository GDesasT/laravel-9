<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AhorcadoController extends Controller
{
    protected $words = ['example', 'hangman', 'laravel', 'controller'];

    public function startGame(Request $request)
    {
        $word = $this->words[array_rand($this->words)];
        $maskedWord = str_repeat('_', strlen($word));

        Session::put('word', $word);
        Session::put('maskedWord', $maskedWord);
        Session::put('attempts', 0);
        Session::put('maxAttempts', 6);

        return response()->json([
            'maskedWord' => $maskedWord,
            'attempts' => 0,
            'maxAttempts' => 6
        ]);
    }

    public function guessLetter(Request $request)
    {
        $letter = $request->input('letter');
        $word = Session::get('word');
        $maskedWord = Session::get('maskedWord');
        $attempts = Session::get('attempts');
        $maxAttempts = Session::get('maxAttempts');

        if (strpos($word, $letter) !== false) {
            for ($i = 0; $i < strlen($word); $i++) {
                if ($word[$i] == $letter) {
                    $maskedWord[$i] = $letter;
                }
            }
            Session::put('maskedWord', $maskedWord);
        } else {
            $attempts++;
            Session::put('attempts', $attempts);
        }

        if ($maskedWord == $word) {
            return response()->json([
                'message' => 'You won!',
                'word' => $word
            ]);
        }

        if ($attempts >= $maxAttempts) {
            return response()->json([
                'message' => 'You lost!',
                'word' => $word
            ]);
        }

        return response()->json([
            'maskedWord' => $maskedWord,
            'attempts' => $attempts,
            'maxAttempts' => $maxAttempts
        ]);
    }
}
