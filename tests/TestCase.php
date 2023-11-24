<?php

namespace Tests;

use Database\Seeders\CommentSeeder;
use Database\Seeders\QuestSeeder;
use Database\Seeders\QuestionSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\PostSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, DatabaseMigrations;

	public function setUp() : void
    {
		parent::setUp();

		$multiplier = 1;

		Log::info('Refreshing database...');

		Artisan::call('migrate:refresh');

		Log::info('Seeding database using multiplier: ' . $multiplier . ' ...');

        (new QuestionSeeder)->call(new QuestionSeeder(), false, ['count' => 20 * $multiplier]);
        (new UserSeeder)->call(new UserSeeder(), false, ['count' => 20 * $multiplier]);
        (new PostSeeder)->call(new PostSeeder(), false, ['count' => 20 * $multiplier]);
        (new CommentSeeder)->call(new CommentSeeder(), false, ['count' => 20 * $multiplier]);
        // (new QuestSeeder)->call(new QuestSeeder(), false, ['count' => 20 * $multiplier]); // commented on purpose, do not change for now
	}

    protected function tearDown():void
    {
        DB::disconnect();
    }
}
