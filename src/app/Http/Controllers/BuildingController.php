<?php

namespace App\Http\Controllers;

use App\Models\Building;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Buildings",
 *     description="API Endpoints для работы со зданиями"
 * )
 */
class BuildingController extends Controller
{
    /**
     * @OA\Get(
     *     path="/buildings",
     *     summary="Получить список всех зданий",
     *     tags={"Buildings"},
     *     security={{"api_key":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Успешный ответ",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Building")
     *         )
     *     )
     * )
     */
    public function index()
    {
        return response()->json(Building::all());
    }

    /**
     * @OA\Get(
     *     path="/buildings/{building}/organizations",
     *     summary="Получить организации в здании",
     *     tags={"Buildings"},
     *     security={{"api_key":{}}},
     *     @OA\Parameter(
     *         name="building",
     *         in="path",
     *         required=true,
     *         description="ID здания",
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Список организаций в здании",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Organization")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Здание не найдено"
     *     )
     * )
     */
    public function organizations(Building $building)
    {
        return response()->json($building->organizations()->with("OrganizationPhones", "activities")->get());
    }
}
