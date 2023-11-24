<?php

namespace Tests\Feature\QuestFeatureTest;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Tests\Unit\APIUnitTestCase;

class QuestFeatureTest extends APIUnitTestCase
{
    public function test_user_quest_system_working()
    {
        $user = $this->prepare_user();

        $user->level = 0;
        $user->experience = 0;

        $user->save();
        $user->refresh();

        $this->assertEquals(0, $user->level);
        $this->assertEquals(0, $user->experience);

        $quests = $this->actingAs($user)
            ->get('/api/quests')
            ->assertStatus(Response::HTTP_OK)
            ->getJson();

        Log::info($quests);
    }
}
