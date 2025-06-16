<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PlayerGame;
use App\Models\Game;
use App\Models\User;
use Illuminate\Support\Str;
use App\Traits\GenerateBoard;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class GameController extends Controller
{
    use GenerateBoard;

    public function showGame($id)
    {
        $userId = 1?:Auth::id();

        // valida que el usuario forme parte de la partida
        $exists = PlayerGame::where('game_id', $id)
                    ->where('user_id', $userId)
                    ->exists();
        if (! $exists) {
            abort(403, 'No puedes ver esta partida');
        }

        // cargo game + players + user de cada player
        $game = Game::with('players.user')->findOrFail($id);

        return Inertia::render('Index', [
            'gameId'   => $game->id,
            'player'   => User::find($userId),
            'code'     => $game->code,
            'players'  => $game->players,           // ← aquí
        ]);
    }

    /**
     * Crea una nueva partida y asigna al primer jugador.
     * Genera un código único para la partida y la guarda en la base de datos.
     */
    public function createGame()
    {
        $user1 = 1 ?: Auth::id();

        $game = Game::create([
            'code' => strtoupper(Str::random(6)),
            'status' => 'waiting',
            'current_turn_user_id' => null,
        ]);

        PlayerGame::create([
            'user_id'  => $user1,
            'game_id'  => $game->id,
            'board'    => null,    // SIN tablero
            'result'   => 'pending',
            'ready'    => false,
        ]);

        return redirect()->route('game.index', ['id' => $game->id]);
    }

    /**
     * Permite a un segundo jugador unirse a una partida existente.
     * Valida que la partida esté en estado 'waiting' y que no tenga más de 2 jugadores.
     */
    public function joinGame(Request $request)
    {
        $user2 = 2?:Auth::id();
        $code  = $request->input('code');

        $game = Game::where('code', $code)
            ->where('status', 'waiting')
            ->firstOrFail();

        if ($game->players()->count() >= 2) {
            return response()->json(['error' => 'La partida ya tiene 2 jugadores'], 409);
        }

        PlayerGame::create([
            'user_id'  => $user2,
            'game_id'  => $game->id,
            'board'    => null,    
            'result'   => 'pending',
            'ready'    => false,
        ]);

        return response()->json([
            'message'    => 'Unido correctamente',
            'game_id'    => $game->id,
            'player_id'  => $user2,
            'opponent'   => $game->players()->where('user_id', '!=', $user2)->with('user')->first(),
            'status'     => $game->status,
        ]);
    }

    /**
     * Marca al jugador como listo para iniciar la partida.
     * Si ambos jugadores están listos, cambia el estado del juego a 'started'.
     */
    public function setReady($id)
    {
        $userId = 2?:Auth::id();  

        $playerGame = PlayerGame::where('game_id', $id)
            ->where('user_id', $userId)
            ->firstOrFail();

        $playerGame->ready = true;
        $playerGame->save();

        $allReady = PlayerGame::where('game_id', $id)
            ->where('ready', true)
            ->count() === 2;

        if ($allReady) {
            Game::where('id', $id)->update(['status' => 'started']);
        }

        return response()->json([
            'message'     => 'Listo',
        ]);
    }

    /**
     * Inicia la partida generando tableros aleatorios para los jugadores.
     * Si el tablero de un jugador es nulo, se genera uno nuevo.
     * Si no hay turno actual, se asigna al primer jugador.
     */
    public function start($gameId)
    {
        $game    = Game::with('players')->findOrFail($gameId);
        $players = $game->players;

        foreach ($players as $playerGame) {
            if (is_null($playerGame->board)) {
                $playerGame->board = json_encode($this->generateRandomBoard(15));
                $playerGame->save();
            }
        }

        if (is_null($game->current_turn_user_id)) {
            $first = $players->random();
            $game->update(['current_turn_user_id' => $first->user_id]);
        }

        $mine = $players->firstWhere('user_id',1?:Auth::id());

        return response()->json([
            'message'               => 'Partida iniciada',
            'current_turn_user_id'  => $game->current_turn_user_id,
            'my_board'              => json_decode($mine->board, true),
        ]);
    }

    /**
     * Realiza un ataque a las coordenadas (x, y) del oponente.
     * Valida que sea el turno del jugador y actualiza el tablero del oponente.
     */
    public function attack($x, $y, $userId = null)
    {
        $userId = $userId ?: Auth::id();

        if ($x < 0 || $x > 7 || $y < 0 || $y > 7) {
            return response()->json(['error' => 'Coordenadas inválidas'], 422);
        }

        $me = PlayerGame::where('user_id', $userId)
            ->whereHas('game', fn($q) => $q->where('status', 'started'))
            ->with('game')
            ->firstOrFail();

        if ($me->game->current_turn_user_id !== $userId) {
            return response()->json(['error' => 'No es tu turno'], 403);
        }

        $opponent = PlayerGame::where('game_id', $me->game_id)
            ->where('user_id', '!=', $userId)
            ->firstOrFail();

        $board = json_decode($opponent->board, true);
        $current = $board[$x][$y] ?? null;

        if ($current === null) {
            return response()->json(['error' => 'Coordenada fuera de rango'], 422);
        }
        if ($current >= 2) {
            return response()->json(['error' => 'Casilla ya atacada'], 422);
        }

        $board[$x][$y] = $current + 2;
        $opponent->board = json_encode($board);

        if ($current === 1) {
            $me->ships_sunk++;
            $opponent->ships_lost++;
        }

        if (! collect($board)->flatten()->contains(1)) {
            return $this->declareVictory($me, $opponent);
        }

        $opponent->save();
        $me->save();
        $me->game->update(['current_turn_user_id' => $opponent->user_id]);

        return response()->json([
            'status' => ($current === 1 ? 'hit' : 'miss'),
            'x'      => $x,
            'y'      => $y,
        ]);
    }

    // 1. Manejo de salida de jugadores
    /**
     * Maneja la salida de un jugador de una partida.
     * Declara la victoria del oponente si el jugador se va.
     */
    public function handlePlayerLeft($gameId, $leavingUserId)
    {
        $game = Game::with('players')->findOrFail($gameId);
        $opponent = $game->players->firstWhere('user_id', '!=', $leavingUserId);
        $me = $game->players->firstWhere('user_id', $leavingUserId);

        return $this->declareVictory($opponent, $me, $game);
    }

    /**
     * Refactored: declara la victoria de $winner frente a $loser
     * opcionalmente recibe la instancia $game si ya la tienes cargada.
     */
    private function declareVictory(PlayerGame $winner, PlayerGame $loser, Game $game = null)
    {
        $game = $game ?: $winner->game;

        $winner->result = 'win';
        $loser->result = 'lose';

        $game->status = 'finished';
        $game->current_turn_user_id = null;

        // Guardar resultados del juego
        $winner->save();
        $loser->save();
        $game->save();

        // 🎯 Actualizar EXP del ganador y perdedor
        $winnerUser = $winner->user; // Relación belongsTo: PlayerGame → User
        $loserUser  = $loser->user;

        $winnerUser->exp += 100;
        $loserUser->exp  += 50;

        $winnerUser->wins += 1;
        $loserUser->losses += 1;

        // Se activa el trigger aquí al hacer update
        $winnerUser->save();
        $loserUser->save();

        return response()->json([
            'status'  => 'win',
            'message' => '¡Has ganado la partida!',
        ]);
    }


    // 2. Heartbeat para mantener la sesión activa
    /**
     * Mantiene la sesión activa del jugador en la partida.
     * Actualiza el campo last_seen_at del jugador.
     */
    public function heartbeat($gameId)
    {
        $userId = Auth::id();

        $playerGame = PlayerGame::where('game_id', $gameId)
            ->where('user_id', $userId)
            ->first();

        if (!$playerGame) {
            return response()->json(['error' => 'No estás en esta partida'], 404);
        }

        $playerGame->last_seen_at = now();
        $playerGame->save();

        return response()->json(['message' => 'Activo']);
    }

    // 3. Polling de estado del juego
    /** 
     * Verifica el estado del juego y devuelve información relevante.
     * Si el juego está en estado 'started', inicia la partida.
     */
    public function checkGameStatus($id)
    {
        $game = Game::with('players.user')->findOrFail($id);

        if ($game->status === 'started') {
            $userId   = 1?:Auth::id();
            $me       = $game->players->firstWhere('user_id', $userId);
            $opponent = $game->players->firstWhere(fn($p) => $p->user_id !== $userId);

            if ($opponent && $opponent->last_seen_at) {
                $inactiveLimit = now()->subSeconds(90);

                if ($opponent->last_seen_at < $inactiveLimit) {
                    return $this->declareVictory($me, $opponent, $game);
                }
            }

            return $this->start($id);
        }

        return response()->json([
            'status'               => $game->status,
            'players'              => $game->players,
            'current_turn_user_id' => $game->current_turn_user_id,
        ]);
    }



    /**
     * Permite a un jugador abandonar una partida.
     * Si el jugador es el único en la partida, se elimina la partida.
     */
    public function surrender($id, $userId = null)
    {
        $userId = $userId ?: Auth::id();

        $playerGame = PlayerGame::where('game_id', $id)
            ->where('user_id', $userId)
            ->first();

        if (!$playerGame) {
            return response()->json(['error' => 'No estás en esta partida'], 404);
        }

        // Eliminar al jugador del juego
        $playerGame->delete();

        // Si el juego queda sin jugadores, eliminarlo
        if (PlayerGame::where('game_id', $id)->count() === 0) {
            Game::destroy($id);
        }

        return response()->json(['message' => 'Has abandonado la partida']);
    }

    
    // frontend - propuesta --------------------------------------------------------------------------------
    /**
     * Maneja las acciones de los jugadores en la partida.
     * Permite atacar o rendirse.
     */
    public function action(Request $request, $gameId)
    {
        $userId = Auth::id();

        $action = $request->input('actionType');

        switch ($action) {
            case 'attack':
                // Validar que vengan coordenadas
                $x = $request->input('row');
                $y = $request->input('col');

                if (!is_numeric($x) || !is_numeric($y)) {
                    return response()->json(['error' => 'Faltan coordenadas'], 422);
                }

                // Usamos la función ya existente
                return $this->attack($x, $y, $userId);

            case 'surrender':
                // Usamos tu función existente
                return $this->surrender($gameId, $userId);

            default:
                return response()->json(['error' => 'Acción no válida'], 400);
        }
    }

}
