<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PlayerGame;
use App\Services\GameService;
use App\Models\Game;
use Illuminate\Support\Str;
use App\Traits\GenerateBoard;

use Illuminate\Support\Facades\Auth;

class GameController extends Controller
{
    use GenerateBoard;
    //
    public function attack($x, $y)
    {
        $userId = Auth::id(); // Esto debería ser Auth::id() si ya estás autenticando

        // Validación de coordenadas
        if ($x < 0 || $x > 7 || $y < 0 || $y > 7) {
            return response()->json(['error' => 'Coordenadas inválidas'], 422);
        }

        // Buscar el juego activo del usuario
        $playerGame = PlayerGame::where('user_id', $userId)
            ->whereHas('game', fn($q) => $q->where('status', 'started'))
            ->with('game') // importante para acceder al turno
            ->first();

        if (!$playerGame) {
            return response()->json(['error' => 'No estás en una partida activa'], 404);
        }

        // Verificar si es su turno
        if ($playerGame->game->current_turn_user_id !== $userId) {
            return response()->json(['error' => 'No es tu turno'], 403);
        }

        // Cargar y verificar casilla
        $board = json_decode($playerGame->board, true);
        $current = $board[$x][$y] ?? null;

        if ($current === null) {
            return response()->json(['error' => 'Coordenada fuera de rango'], 422);
        }

        if ($current >= 2) {
            return response()->json(['error' => 'Casilla ya atacada'], 422);
        }

        // Actualizar casilla
        $board[$x][$y] = $current + 2;
        $playerGame->board = json_encode($board);
        $playerGame->save();

        // Cambiar turno al oponente
        $opponent = PlayerGame::where('game_id', $playerGame->game_id)
            ->where('user_id', '!=', $userId)
            ->first();

        $playerGame->game->update([
            'current_turn_user_id' => $opponent->user_id,
        ]);

        $status = $current === 1 ? 'hit' : 'miss';

        return response()->json([
            'status' => $status,
            'x' => (int) $x,
            'y' => (int) $y,
        ]);
    }


    public function start()
    {
        $user1 = Auth::id();
        $user2 = Auth::id();

        $gameService = new GameService();
        $game = $gameService->startGame($user1, $user2);

        return response()->json([
            'message' => 'Partida creada con éxito',
            'game_id' => $game->id,
            'turno_inicial' => $game->current_turn_user_id,
        ]);
    }

    public function createGame(Request $request)
    {
        // $user1 = 1;
        $user1 = Auth::id();

        $game = Game::create([
            'code' => strtoupper(Str::random(6)),
            'status' => 'waiting',
            'current_turn_user_id' => null,
        ]);

        PlayerGame::create([
            'user_id' => $user1,
            'game_id' => $game->id,
            'board' => json_encode($this->generateRandomBoard(15)),
            'result' => 'pending',
        ]);

        return response()->json(['code' => $game->code]);
    }

    public function joinGame(Request $request)
    {
        // $user2 = 1;
        $user2 = Auth::id();

        $code = $request->input('code');

        $game = Game::where('code', $code)
            ->where('status', 'waiting')
            ->first();

        if (!$game) {
            return response()->json(['error' => 'Partida no encontrada o ya iniciada'], 404);
        }

        if (PlayerGame::where('game_id', $game->id)->count() >= 2) {
            return response()->json(['error' => 'La partida ya tiene 2 jugadores'], 409);
        }

        PlayerGame::create([
            'user_id' => $user2,
            'game_id' => $game->id,
            'board' => json_encode($this->generateRandomBoard(15)),
            'result' => 'pending',
        ]);

        return response()->json(['message' => 'Unido correctamente']);
    }

    // 3. Polling de estado del juego
    public function checkGameStatus($id)
    {
        $game = Game::findOrFail($id);

        $players = PlayerGame::where('game_id', $game->id)->with('user')->get();

        if ($game->status === 'waiting' && $players->count() === 2) {
            // Elegir aleatoriamente quién empieza
            $firstPlayer = $players->random();
            $game->status = 'started';
            $game->current_turn_user_id = $firstPlayer->user_id;
            $game->save();
        }

        return response()->json([
            'status' => $game->status,
            'players' => $players->map(fn($p) => [
                'id' => $p->user->id,
                'name' => $p->user->name
            ]),
            'current_turn_user_id' => $game->current_turn_user_id,
        ]);
    }

}
