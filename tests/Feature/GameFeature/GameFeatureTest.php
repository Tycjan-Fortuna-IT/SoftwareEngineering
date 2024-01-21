<?php

namespace Tests\Feature\GameFeature;

use App\Models\Game;
use App\Models\User;
use Illuminate\Http\Response;
use Tests\AssertableJson;
use Tests\Unit\APIUnitTestCase;

class GameFeatureTest extends APIUnitTestCase
{
    private function prepare_player(): User
    {
        $player = $this->prepare_user();

        $player->level = 0;
        $player->experience = 0;

        $player->save();
        $player->refresh();

        return $player;
    }

    private function prepare_finished_game(User $host): Game
    {
        $game = Game::create([
            'stage' => Game::FINISHED,
            'limit' => 4,
            'level' => Game::GAME_1,
            'user_id' => $host->id,
        ]);

        return $game;
    }

    private function prepare_cancelled_game(User $host): Game
    {
        $game = Game::create([
            'stage' => Game::CANCELLED,
            'limit' => 4,
            'level' => Game::GAME_1,
            'user_id' => $host->id,
        ]);

        return $game;
    }

    // sockets are not tested hehe :)
    public function test_game_system_working()
    {
        $gameTypes = [
            Game::GAME_1,
            Game::GAME_2,
            Game::GAME_3,
            Game::GAME_4,
            Game::GAME_5,
            Game::GAME_6,
            Game::GAME_7,
        ];

        $host = $this->prepare_player();

        $level = array_rand($gameTypes);

        $gameResponse = $this->actingAs($host)
            ->post('/api/games', [
                'limit' => 4,
                'level' => $gameTypes[$level],
            ])
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson(function (AssertableJson $json) {
				$json->has('data', function (AssertableJson $data) {
                    LobbyResourceValidator::validate($data);
				});
			})
            ->json()['data'];

        $game = Game::where('uuid', $gameResponse['uuid'])->first();

        $this->assertDatabaseHas('games', [
            'limit' => 4,
            'level' => $gameTypes[$level],
            'stage' => Game::LOBBY,
            'user_id' => $host->id,
        ]);

        $this->actingAs($host)
            ->post('/api/games', [
                'limit' => 4,
                'level' => $gameTypes[$level],
            ])
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->json();

        $player1 = $this->prepare_player();
        $player2 = $this->prepare_player();

        $this->actingAs($host)
            ->put('/api/games/' . $game->uuid, [
                'stage' => Game::PLAYING,
                'joiner_uuid' => $player1->uuid,
            ])
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson([
                'message' => 'e003 - Only one operation at a time is allowed',
            ]);

        $finishedGame = $this->prepare_finished_game($host);

        $this->actingAs($host)
            ->put('/api/games/' . $finishedGame->uuid, [
                'stage' => Game::PLAYING,
            ])
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson([
                'message' => 'e004 - This game has already finished',
            ]);

        $cancelledGame = $this->prepare_cancelled_game($host);

        $this->actingAs($host)
            ->put('/api/games/' . $cancelledGame->uuid, [
                'stage' => Game::PLAYING,
            ])
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson([
                'message' => 'e005 - This game has already been cancelled',
            ]);

        $this->actingAs($player1)
            ->put('/api/games/' . $game->uuid, [
                'stage' => Game::PLAYING,
            ])
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson([
                'message' => 'e006 - You are not the host of this game, only the host can update the stage of game',
            ]);

        $this->actingAs($player1)
            ->put('/api/games/' . $game->uuid, [
                'joiner_uuid' => $player1->uuid,
            ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment([
                'message' => 'You have successfully joined the game',
                'data' => [
                    'uuid' => $game->uuid,
                    'limit' => 4,
                    'goal' => $game->goal,
                    'level' => $gameTypes[$level],
                    'stage' => Game::LOBBY,
                    'created_at' => $game["created_at"],
                    'updated_at' => $game["updated_at"],
                ],
            ]);

        $this->actingAs($player1)
            ->put('/api/games/' . $game->uuid, [
                'joiner_uuid' => $player2->uuid,
            ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment([
                'message' => 'You have successfully joined the game',
                'data' => [
                    'uuid' => $game->uuid,
                    'limit' => 4,
                    'goal' => $game->goal,
                    'level' => $gameTypes[$level],
                    'stage' => Game::LOBBY,
                    'created_at' => $game["created_at"],
                    'updated_at' => $game["updated_at"],
                ],
            ]);

        $game = Game::where('uuid', $game->uuid)->first();

        $this->assertDatabaseHas('games', [
            'limit' => 4,
            'level' => $gameTypes[$level],
            'stage' => Game::LOBBY,
            'user_id' => $host->id,
        ]);

        $this->assertDatabaseHas('game_user', [
            'game_id' => $game->id,
            'user_id' => $host->id,
            'collected_points' => 0,
        ]);

        $this->assertDatabaseHas('game_user', [
            'game_id' => $game->id,
            'user_id' => $player1->id,
            'collected_points' => 0,
        ]);

        $this->assertDatabaseHas('game_user', [
            'game_id' => $game->id,
            'user_id' => $player2->id,
            'collected_points' => 0,
        ]);

        $this->actingAs($host)
            ->put('/api/games/' . $game->uuid, [
                'stage' => Game::PLAYING,
            ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => 'Game updated successfully',
            ]);

        $totalGoal = $game->goal;

        $hostPart = (int)($totalGoal / 4);
        $player1Part = (int)($totalGoal / 4);
        $player2Part = (int)($totalGoal / 4);

        $this->actingAs($host)
            ->put('/api/games/' . $game->uuid, [
                'points' => $hostPart,
            ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => 'Game updated successfully',
            ]);

        $this->actingAs($player1)
            ->put('/api/games/' . $game->uuid, [
                'points' => $player1Part,
            ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => 'Game updated successfully',
            ]);

        $this->actingAs($player2)
            ->put('/api/games/' . $game->uuid, [
                'points' => $player2Part,
            ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => 'Game updated successfully',
            ]);

        $this->assertDatabaseHas('games', [
            'limit' => 4,
            'level' => $gameTypes[$level],
            'stage' => Game::PLAYING,
            'user_id' => $host->id,
        ]);

        $this->assertDatabaseHas('game_user', [
            'game_id' => $game->id,
            'user_id' => $host->id,
            'collected_points' => $hostPart,
        ]);

        $this->assertDatabaseHas('game_user', [
            'game_id' => $game->id,
            'user_id' => $player1->id,
            'collected_points' => $player1Part,
        ]);

        $this->assertDatabaseHas('game_user', [
            'game_id' => $game->id,
            'user_id' => $player2->id,
            'collected_points' => $player2Part,
        ]);

        $this->assertEquals(0, $host->experience);
        $this->assertEquals(0, $player1->experience);
        $this->assertEquals(0, $player2->experience);

        $this->actingAs($player2)
            ->put('/api/games/' . $game->uuid, [
                'points' => $game->goal - $hostPart - $player1Part - $player2Part + (int)($game->goal / 4),
            ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => 'Game updated successfully',
            ]);

        $this->assertDatabaseHas('game_user', [
            'game_id' => $game->id,
            'user_id' => $player2->id,
            'collected_points' => $player2Part + $game->goal - $hostPart - $player1Part - $player2Part + (int)($game->goal / 4),
        ]);

        $this->assertDatabaseHas('games', [
            'limit' => 4,
            'level' => $gameTypes[$level],
            'stage' => Game::FINISHED,
            'user_id' => $host->id,
        ]);

        $this->actingAs($player2)
            ->put('/api/games/' . $game->uuid, [
                'points' => 22,
            ])
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson([
                'message' => 'e004 - This game has already finished',
            ]);

        $host->refresh();
        $player1->refresh();
        $player2->refresh();

        $this->assertEquals((int)($hostPart * $game->expModifier), $host->experience);
        $this->assertEquals((int)($player1Part * $game->expModifier), $player1->experience);
        $this->assertEquals((int)(($player2Part + $game->goal - $hostPart - $player1Part - $player2Part + (int)($game->goal / 4)) * $game->expModifier), $player2->experience);
    }
}
