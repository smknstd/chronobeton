<?php

namespace App\Models;

use App\Enums\ColorTheme;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        "color" => ColorTheme::class,
    ];

    public function consumers()
    {
        return $this->hasMany(Consumer::class, "customer_id");
    }
}
