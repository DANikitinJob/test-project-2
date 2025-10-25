<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = [
        "name",
        "parent_id"
    ];

    // категории могут иметь подкатегории
    public function children()
    {
        return $this->hasMany(Activity::class, 'parent_id');

    }

    // подкатегория имеет родителя
    public function parent()
    {
        return $this->hasMany(Activity::class, 'parent_id');

    }

    public function organizations()
    {
        return $this->belongsToMany(Organization::class);
    }
}
