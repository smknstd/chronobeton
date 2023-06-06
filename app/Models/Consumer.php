<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consumer extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function concreteSessions()
    {
        return $this->hasMany(ConcreteSession::class, "consumer_id");
    }
}
