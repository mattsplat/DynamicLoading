<?php

namespace Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    public function rank()
    {
        return $this->belongsTo(Rank::class);
    }
}
