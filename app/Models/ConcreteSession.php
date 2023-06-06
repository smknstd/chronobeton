<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConcreteSession extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'imported_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function consumer()
    {
        return $this->belongsTo(Consumer::class);
    }
}
