<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Building extends Model
{
    protected $fillable = [
        "address",
        "latitude",
        "longitude",
    ];

    public function organizations()
    {
        return $this->hasMany(Organization::class);
    }
}
