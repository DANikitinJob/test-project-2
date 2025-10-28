<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Organization",
 *     required={"id", "name", "building_id"},
 *     @OA\Property(property="id", type="integer", format="int64"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="building_id", type="integer", format="int64"),
 *     @OA\Property(
 *         property="building",
 *         ref="#/components/schemas/Building"
 *     ),
 *     @OA\Property(
 *         property="OrganizationPhones",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/OrganizationPhone")
 *     ),
 *     @OA\Property(
 *         property="activities",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/Activity")
 *     )
 * )
 */
class Organization extends Model
{
    use HasFactory;
    protected $fillable = [
        "name",
        "building_id",
    ];

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function OrganizationPhones()
    {
        return $this->hasMany(OrganizationPhone::class);
    }

    public function activities()
    {
        return $this->belongsToMany(Activity::class);
    }
}
