<?php

namespace App\Helpers\Managers;

use App\Models\Game;
use Illuminate\Support\Facades\DB;

class GameManager
{
    public static function FindAndDeleteInactiveGames(): void
    {
        $games = Game::all();

        DB::beginTransaction();

        foreach ($games as $game) {
            if ($game->created_at->diffInMinutes() > 7) {
                DB::table('game_user')->where('game_id', $game->id)->delete();
                $game->delete();
            }
        }

        DB::commit();
    }
}
