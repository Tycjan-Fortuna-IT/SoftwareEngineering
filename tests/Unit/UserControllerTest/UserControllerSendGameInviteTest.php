<?php

namespace Tests\Unit\UserControllerTest;

use App\Models\Notification;
use Illuminate\Http\Response;
use Tests\Unit\APIUnitTestCase;

class UserControllerSendGameInviteTest extends APIUnitTestCase
{
    public function test_users_send_game_invite_sends_invite()
    {
        $user = $this->get_random_user();
        $otherUser = $this->prepare_user();
        $gameUuid = $this->get_random_uuid();

        $this->actingAs($user)
            ->postJson('/api/users/sendGameInvite', [
                'receiver_uuid' => $otherUser->uuid,
                'game_uuid' => $gameUuid
            ])
            ->assertStatus(Response::HTTP_OK);

        $notification = $otherUser->notifications()->first();
        $payload = json_decode($notification->payload, true);

        $this->assertEquals($payload['game_uuid'], $gameUuid);
        $this->assertEquals($notification->type, Notification::GAME_INVITE);
    }
}
