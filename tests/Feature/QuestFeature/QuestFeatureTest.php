<?php

namespace Tests\Feature\QuestFeatureTest;

use App\Helpers\Managers\UserLevelManager;
use App\Models\Quest;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Tests\Unit\APIUnitTestCase;
use Illuminate\Support\Str;

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

        $oldQuestUuid1 = Str::uuid();
        $oldQuestUuid2 = Str::uuid();

        DB::table('quests')->insert([
            'uuid' => $oldQuestUuid1,
            'type' => fake()->numberBetween(1, 7),
            'status' => Quest::STATUS_IN_PROGRESS,
            'required' => fake()->numberBetween(1, 10),
            'collected' => 0,
            'reward' => 69,
            'user_id' => $user->id,
            'created_at' => Carbon::now()->subDays(2),
        ]);

        DB::table('quests')->insert([
            'uuid' => $oldQuestUuid2,
            'type' => fake()->numberBetween(1, 7),
            'status' => Quest::STATUS_FAILED,
            'required' => fake()->numberBetween(1, 10),
            'collected' => 0,
            'reward' => 69,
            'user_id' => $user->id,
            'created_at' => Carbon::now()->subDays(2),
        ]);

        $quests = $this->actingAs($user)
            ->get('/api/quests')
            ->assertStatus(Response::HTTP_OK)
            ->json();

        $this->assertCount(2, $quests['data']);

        $user->refresh();

        $this->assertEquals(2, $user->quests->count());

        $user->quests->each(function (Quest $quest) {
            $this->assertEquals(Quest::STATUS_IN_PROGRESS, $quest->status);
        });

        $user->quests->each(function (Quest $quest) use ($oldQuestUuid1, $oldQuestUuid2) {
            $this->assertNotEquals($quest->uuid, $oldQuestUuid1);
            $this->assertNotEquals($quest->uuid, $oldQuestUuid2);
        });

        $randomUserQuest = $user->quests->random();
        $xpForLevelUp = UserLevelManager::GetExpForLevel($user->level + 1);

        $this->assertEquals(0, $randomUserQuest->collected);

        $this->actingAs($user)
            ->put('/api/quests/' . $randomUserQuest->uuid, [
                'collected' => $randomUserQuest->required - 5,
            ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => 'Quest updated successfully',
            ]);

        $user->refresh();

        $this->assertEquals($randomUserQuest->required - 5, $randomUserQuest->fresh()->collected);
        $this->assertEquals(0, $user->experience);
        $this->assertEquals(0, $user->level);

        $this->actingAs($user)
            ->put('/api/quests/' . $randomUserQuest->uuid, [
                'collected' => $randomUserQuest->required + 199,
            ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => 'Quest updated successfully',
            ]);

        $user->refresh();
        $randomUserQuest->refresh();

        $this->assertEquals($randomUserQuest->required + 199, $randomUserQuest->collected);

        if ($randomUserQuest->reward >= $xpForLevelUp) {
            $this->assertEquals($xpForLevelUp, $user->experience);
            $this->assertEquals(1, $user->level);
        } else {
            $this->assertEquals($randomUserQuest->reward, $user->experience);
            $this->assertEquals(0, $user->level);
        }
    }
}
