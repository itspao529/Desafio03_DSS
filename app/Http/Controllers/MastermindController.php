<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\GameScore;

class MastermindController extends Controller {
    public function index() {
        if (!session()->has('secret')) { $this->initGame(); }
        return view('game');
    }

    private function initGame() {
        $colors = ['rojo', 'verde', 'azul', 'amarillo', 'naranja', 'violeta'];
        $secret = [];
        for ($i = 0; $i < 4; $i++) { $secret[] = $colors[array_rand($colors)]; }
        session(['secret' => $secret, 'attempts_left' => 8, 'history' => [], 'gameOver' => false]);
    }

    public function guess(Request $request) {
        if (session('gameOver')) return response()->json(['message' => 'Fin del juego'], 400);
        $guess = $request->input('colors');
        $secret = session('secret');
        $exact = 0; $colorMatch = 0;
        $sCopy = $secret; $gCopy = $guess;

        foreach ($gCopy as $i => $color) {
            if ($color === $sCopy[$i]) { $exact++; $sCopy[$i] = null; $gCopy[$i] = null; }
        }
        foreach ($gCopy as $color) {
            if ($color && ($key = array_search($color, $sCopy)) !== false) { $colorMatch++; $sCopy[$key] = null; }
        }

        $left = session('attempts_left') - 1;
        $win = ($exact === 4);
        $gameOver = ($win || $left <= 0);
        session(['attempts_left' => $left, 'gameOver' => $gameOver]);

        if ($gameOver) {
            GameScore::create([
                'player_name' => $request->input('name', 'Anon'),
                'score' => ($left * 10) + ($exact * 2),
                'attempts_used' => 8 - $left,
                'won' => $win
            ]);
        }
        return response()->json(['exact' => $exact, 'color' => $colorMatch, 'gameOver' => $gameOver, 'win' => $win]);
    }

    public function leaderboard() { return response()->json(GameScore::orderBy('score', 'desc')->take(5)->get()); }
    public function restart() { $this->initGame(); return response()->json(['success' => true]); }
}
