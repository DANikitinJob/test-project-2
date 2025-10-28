<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="OrganizationPhone",
 *     required={"id", "phone", "organization_id"},
 *     @OA\Property(property="id", type="integer", format="int64"),
 *     @OA\Property(property="phone", type="string"),
 *     @OA\Property(property="organization_id", type="integer", format="int64")
 * )
 */
class OrganizationPhone extends Model
{
    use HasFactory;
    protected $fillable = [
        "organization_id",
        "phone",
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
