<?php

namespace Tests;

use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Tests\Models\Rank;
use Tests\Models\User;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $db = new DB();
        $db->addConnection([
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
        $db->setAsGlobal();
        $db->bootEloquent();

        $this->migrate();

        $this->seedData();
    }

    /**
     * Migrate the database.
     *
     * @return void
     */
    protected function migrate()
    {
        DB::schema()->create('ranks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->smallInteger('level')->nullable();
            $table->timestamps();
        });

        DB::schema()->create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->foreignId('rank_id')->constrained();
            $table->timestamps();
        });

    }

    /**
     * Seed the database.
     *
     * @return void
     */
    public function seedData(): void
    {
        Model::unguard();

        Rank::create(['id' => 10, 'name' => 'newbie', 'level' => 1]);
        Rank::create(['id' => 9, 'name' => 'rookie','level' => 2]);
        Rank::create(['id' => 2, 'name' => 'ranger', 'level' => 3]);
        Rank::create(['id' => 6, 'name' => 'expert', 'level' => 4]);
        Rank::create(['id' => 7, 'name' => 'master', 'level' => 5]);

        User::create(['id' => 11, 'rank_id' => 10, 'name' => 'John Doe']);
        User::create(['id' => 12, 'rank_id' => 2, 'name' => 'Jane Doe']);
        User::create(['id' => 13, 'rank_id' => 6, 'name' => 'Harry Hoe']);
    }

    protected function getPackageProviders($app)
    {
        return ['MattSplat\DynamicLoading\DynamicLoadingServiceProvider'];
    }
}
