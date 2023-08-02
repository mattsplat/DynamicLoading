<?php

namespace Tests;

use Tests\Models\Rank;
use Tests\Models\User;

class TestUser extends TestCase
{
    public function testGetRanksWithRForUsers()
    {

        $users = User::get()->dynamicLoad(
            'r_ranks',
            fn($m) => Rank::where('name', 'like', 'r%')->where('id', '!=', $m->rank_id)
        );

        $this->assertNotNull($users);
        $this->assertNotNull($users[0]->r_ranks);
        $this->assertNotNull($users[1]->r_ranks);
        $hasRankWithR = $users->contains(
            fn($u) => $u->r_ranks->contains(
                fn($r) => strtolower($r->name[0]) === 'r'
            )
        );
        $this->assertTrue($hasRankWithR);
    }

    public function testUserGetNextRank()
    {
        $users = User::with('rank')->get()->dynamicLoad(
            'next_rank',
            fn($m) => Rank::where('level', '>', $m->rank->level)->orderBy('level', 'asc')->limit(1),
            null, null,
            true
        );

        $this->assertNotNull($users);
        $firstUser = $users->first();
        $this->assertNotNull($firstUser->next_rank);
        $this->assertNotNull($firstUser->rank);
        $this->assertNotNull($firstUser->next_rank->level);
        $this->assertNotNull($firstUser->rank->level);
        $this->assertEquals($firstUser->next_rank->level, $firstUser->rank->level + 1);
    }
}