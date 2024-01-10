<?php

namespace App\Http\Controllers\API;

use App\Events\GameFinishedSocketEvent;
use App\Events\GameStartedSocketEvent;
use App\Events\GameUpdateSocketEvent;
use App\Events\PlayerJoinedSocketEvent;
use App\Helpers\Managers\UserLevelManager;
use App\Http\Controllers\Controller;
use App\Http\Resources\API\GameResource;
use App\Models\Game;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GameController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return GameResource|JsonResponse
     */
    public function store(Request $request): GameResource|JsonResponse
    {
        $request->validate([
            'limit' => 'required|integer',
            'level' => 'required|integer|between:1,7',
        ]);

        $user = User::whereId(Auth::id())
            ->with('hostGames', 'guestGames')
            ->firstOrFail();

        if ($user->hostGames()->whereIn('stage', [Game::LOBBY, Game::PLAYING])->count() > 0) {
            return response()->json([
                'message' => 'e001 - There was an error creating the game',
            ], 400);
        }

        if ($user->guestGames()->whereIn('stage', [Game::LOBBY, Game::PLAYING])->count() > 0) {
            return response()->json([
                'message' => 'e002 - There was an error creating the game',
            ], 400);
        }

        $game = Game::create([
            'limit' => $request->limit,
            'level' => $request->level,
            'stage' => Game::LOBBY,
            'user_id' => $user->id,
        ]);

        DB::table('game_user')->insert([
            'game_id' => $game->id,
            'user_id' => $user->id,
        ]);

        return new GameResource($game);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Game $game
     * @return JsonResponse|GameResource
     */
    public function update(Request $request, Game $game): JsonResponse|GameResource
    {
        $request->validate([
            'stage' => 'nullable|integer|min:2|max:2',
            'joiner_uuid' => 'nullable|uuid',
            'points' => 'nullable|integer',
        ]);

        if (isset($request->stage) && isset($request->joiner_uuid)) {
            return response()->json(['message' => 'e003 - Only one operation at a time is allowed'], 400);
        }

        if ($game->stage == Game::FINISHED) {
            return response()->json(['message' => 'e004 - This game has already finished'], 400);
        }

        if ($game->stage == Game::CANCELLED) {
            return response()->json(['message' => 'e005 - This game has already been cancelled'], 400);
        }

        // TODO: game activity check, if no activity for 5 minutes, cancel and close the game

        switch($game->stage) {
            case Game::LOBBY:

                if (isset($request->stage)) {

                    if ($game->user->id != Auth::id()) {
                        return response()->json(['message' => 'e006 - You are not the host of this game, only the host can update the stage of game'], 400);
                    }

                    $game->stage = Game::PLAYING;
                    $game->save();

                    event(new GameStartedSocketEvent($game->uuid, $game->users()->pluck('uuid')->toArray()));
                }

                if (isset($request->joiner_uuid)) {

                    if ($game->stage != Game::LOBBY) {
                        return response()->json([
                            'message' => 'e007 - You cannot join a game that has already started',
                        ], 400);
                    }

                    if ($game->users()->count() >= $game->limit) {
                        return response()->json([
                            'message' => 'e008 - You cannot join this game, it is already full',
                        ], 400);
                    }

                    $joiningPlayer = User::whereUuid($request->joiner_uuid)
                        ->with('hostGames', 'guestGames')
                        ->firstOrFail();

                    if ($joiningPlayer->hostGames()->whereIn('stage', [Game::LOBBY, Game::PLAYING])->count() > 0) {
                        return response()->json([
                            'message' => 'e009 - You cannot join a game if you are already hosting one',
                        ], 400);
                    }

                    if ($joiningPlayer->guestGames()->whereIn('stage', [Game::LOBBY, Game::PLAYING])->count() > 0) {
                        return response()->json([
                            'message' => 'e010 - You cannot join a game if you are already playing one',
                        ], 400);
                    }

                    DB::table('game_user')->insert([
                        'game_id' => $game->id,
                        'user_id' => $joiningPlayer->id,
                    ]);

                    event(new PlayerJoinedSocketEvent($game->uuid, $joiningPlayer->uuid));

                    return response()->json([
                        'message' => 'You have successfully joined the game',
                        'data' => new GameResource($game),
                    ], 200);
                }

                break;

            case Game::PLAYING:

                if (isset($request->points)) {

                    $currentPoints = DB::table('game_user')
                        ->where('game_id', $game->id)
                        ->where('user_id', Auth::id())
                        ->first()->collected_points;

                    $game->users()->updateExistingPivot(Auth::id(), [
                        'collected_points' => $request->points + $currentPoints,
                    ]);

                    $players = $game->users()->get();

                    $playersWithPoints = [];

                    foreach ($players as $player) {
                        $playersWithPoints[] = [
                            'uuid' => $player->uuid,
                            'points' => $player->pivot->collected_points,
                        ];
                    }

                    $collectedPoints = $game->users()->sum('collected_points');

                    event(new GameUpdateSocketEvent($game->uuid, $playersWithPoints, [
                        'goal' => $game->goal,
                        'exp_modifier' => $game->expModifier,
                        'collected_points' => $collectedPoints,
                        'progress' => $collectedPoints / $game->goal,
                    ]));

                    if ($collectedPoints >= $game->goal) {
                        $game->stage = Game::FINISHED;
                        $game->save();

                        event(new GameFinishedSocketEvent($game->uuid, $playersWithPoints));

                        foreach ($game->users()->get() as $player) {
                            UserLevelManager::AddExp($player, $player->pivot->collected_points * $game->expModifier);
                        }
                    }

                }

                break;

            default:

                return response()->json(['message' => 'e011 - This game is in an invalid state'], 400);
        }

        return response()->json(['message' => 'Game updated successfully'], 200);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param Game $game
     * @return JsonResponse
     */
    public function destroy(Game $game): JsonResponse
    {
        DB::beginTransaction();

        DB::table('game_user')->where('game_id', $game->id)->delete();

        $game->stage = Game::CANCELLED;
        $game->save();

        $game->delete();

        DB::commit();

        return response()->json(['message' => 'Game deleted successfully'], 200);
    }
}
