<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Building",
 *     required={"id", "address", "latitude", "longitude"},
 *     @OA\Property(property="id", type="integer", format="int64"),
 *     @OA\Property(property="address", type="string"),
 *     @OA\Property(property="latitude", type="number", format="float"),
 *     @OA\Property(property="longitude", type="number", format="float"),
 *     @OA\Property(
 *         property="organizations",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/Organization")
 *     )
 * )
 */
class Building extends Model
{
    use HasFactory;
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
