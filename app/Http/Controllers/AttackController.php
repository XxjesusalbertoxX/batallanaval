<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PlayerGame;
use Illuminate\Support\Facades\Auth;

class AttackController extends Controller
{
    function generateRandomBoard(int $shipCount = 15): array {
    $board = array_fill(0, 8, array_fill(0, 8, 0)); // Tablero 8x8 lleno de agua (0)
    $positions = [];

    // generar todas las coordenadas posibles
    for ($x = 0; $x < 8; $x++) {
        for ($y = 0; $y < 8; $y++) {
            $positions[] = [$x, $y];
        }
    }

    // mezclar posiciones y seleccionar las primeras $shipCount
    shuffle($positions);
    $selected = array_slice($positions, 0, $shipCount);

    foreach ($selected as [$x, $y]) {
        $board[$x][$y] = 1; // colocar barco
    }

    return $board;
}


    public function attack($x, $y)
    {

        // validación básica de índices
        if ($x < 0 || $x > 7 || $y < 0 || $y > 7) {
            return response()->json(['error' => 'Coordenadas inválidas'], 422);
        }

        // obtenemos la partida activa de este usuario
        $playerGame = PlayerGame::where('user_id', 11)->whereHas('game', fn($q) => $q->where('status', 'started'))->first();



        // cargamos el tablero (array 8×8)
        $board = json_decode($playerGame->board, true);

        // chequeo de casilla ya atacada
        $current = $board[$x][$y] ?? null;
        if ($current === null) {
            return response()->json(['error' => 'Coordenada fuera de rango'], 422);
        }
        if ($current >= 2) {
            return response()->json(['error' => 'Casilla ya atacada'], 422);
        }

        // actualizamos: 0 → 2 (miss), 1 → 3 (hit)
        $board[$x][$y] = $current + 2;
        $playerGame->board = json_encode($board);
        $playerGame->save();

        // determinamos el resultado
        $status = $current === 1 ? 'hit' : 'miss';

        return response()->json([
            'status' => $status,
            'x'      => (int) $x,
            'y'      => (int) $y,
        ]);
    }
}
