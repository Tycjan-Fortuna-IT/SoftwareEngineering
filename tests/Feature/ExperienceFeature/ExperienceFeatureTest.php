<?php

namespace Tests\Feature\ExperienceFeature;

use App\Helpers\Managers\UserLevelManager;
use Tests\Unit\APIUnitTestCase;

class ExperienceFeatureTest extends APIUnitTestCase
{
    public function test_user_level_is_increased_when_enough_experience_is_gained()
    {
        $user = $this->prepare_user();

        $user->level = 0;
        $user->experience = 0;

        $user->save();
        $user->refresh();

        $this->assertEquals(0, $user->level);
        $this->assertEquals(0, $user->experience);

        $lvl1exp = UserLevelManager::GetExpForLevel(1);

        UserLevelManager::AddExp($user, $lvl1exp);

        $user->refresh();

        $this->assertEquals(1, $user->level);
        $this->assertEquals(0, $user->experience);

        $lvl2exp = UserLevelManager::GetExpForLevel(2);

        UserLevelManager::AddExp($user, $lvl2exp - 100);

        $user->refresh();

        $this->assertEquals(1, $user->level);
        $this->assertEquals($lvl2exp - 100, $user->experience);

        UserLevelManager::AddExp($user, 100);

        $user->refresh();

        $this->assertEquals(2, $user->level);
    }
}
