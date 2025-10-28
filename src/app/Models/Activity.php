<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Activity",
 *     required={"id", "name"},
 *     @OA\Property(property="id", type="integer", format="int64"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="parent_id", type="integer", format="int64", nullable=true),
 *     @OA\Property(
 *         property="children",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/Activity")
 *     ),
 *     @OA\Property(
 *         property="organizations",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/Organization")
 *     )
 * )
 */
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
