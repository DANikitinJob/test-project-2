<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;
    protected $fillable = [
        "name",
        "parent_id"
    ];

    // категории могут иметь подкатегории
    public function children()
    {
        // hasMany ищет все записи в таблице activities, у которых parent_id равен id текущей категории
        return $this->hasMany(Activity::class, 'parent_id');

    }

    // подкатегория имеет родителя
    public function parent()
    {
        // belongsTo ищет запись в таблице activities, у которой id равен parent_id текущей категории
        return $this->belongsTo(Activity::class, 'parent_id');

    }

    public function organizations()
    {
        return $this->belongsToMany(Organization::class);
    }
}
