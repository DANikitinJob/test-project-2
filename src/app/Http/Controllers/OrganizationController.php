<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Notifications\Action;

/**
 * @OA\Tag(
 *     name="Organizations",
 *     description="API Endpoints для работы с организациями"
 * )
 */
class OrganizationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/organizations/search",
     *     summary="Поиск организаций по названию",
     *     tags={"Organizations"},
     *     security={{"api_key":{}}},
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         required=true,
     *         description="Название организации для поиска",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешный поиск",
     *         @OA\JsonContent(ref="#/components/schemas/Organization")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Ошибка валидации"
     *     )
     * )
     */
    public function searchByName(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $organization = Organization::where("name", "LIKE", "%" . $data["name"] . "%")
            ->with(["building", "OrganizationPhones", "activities"])->first();
        return response()->json($organization);
    }

    // поиск организаций по геопозиции
    /**
     * @OA\Get(
     *     path="/organizations/geo/search",
     *     summary="Поиск организаций по геолокации",
     *     tags={"Organizations"},
     *     security={{"api_key":{}}},
     *     @OA\Parameter(
     *         name="lat",
     *         in="query",
     *         required=true,
     *         description="Широта",
     *         @OA\Schema(type="number", format="float", minimum=-90, maximum=90)
     *     ),
     *     @OA\Parameter(
     *         name="lng",
     *         in="query",
     *         required=true,
     *         description="Долгота",
     *         @OA\Schema(type="number", format="float", minimum=-180, maximum=180)
     *     ),
     *     @OA\Parameter(
     *         name="radius",
     *         in="query",
     *         description="Радиус поиска в километрах",
     *         @OA\Schema(type="number", format="float", minimum=0, default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Список найденных организаций",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Organization")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Ошибка валидации"
     *     )
     * )
     */
    public function geoSearch(Request $request)
    {
        $data = $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:0',
        ]);

        $lat = $data['lat'];
        $lng = $data['lng'];
        $radius = $data['radius'] ?? 1; // км

        // если связанное с организацией здание входит в радиус, то выбираем эту организацию
        $organizations = Organization::whereHas('building', function ($query) use ($lat, $lng, $radius) {
            $query->selectRaw("6371 * acos(
                cos(radians(?)) * cos(radians(latitude)) * 
                cos(radians(longitude) - radians(?)) + 
                sin(radians(?)) * sin(radians(latitude))
            ) as distance", [$lat, $lng, $lat])
                ->havingRaw('distance <= ?', [$radius]);
        })
            ->with(['building', 'OrganizationPhones', 'activities'])
            ->get();

        return response()->json($organizations);
    }

    // поиск организаций по виду деятельности с учётом вложенных подкатегорий
    /**
     * @OA\Get(
     *     path="/organizations/activity/{activity}",
     *     summary="Поиск организаций по виду деятельности",
     *     tags={"Organizations"},
     *     security={{"api_key":{}}},
     *     @OA\Parameter(
     *         name="activity",
     *         in="path",
     *         required=true,
     *         description="ID вида деятельности",
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Список организаций с указанным видом деятельности",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Organization")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Вид деятельности не найден"
     *     )
     * )
     */
    public function searchByActivity(Activity $activity)
    {
        $ids = $this->getAllActivityIds($activity);
        // выбирвем организации, которые связаны с любым из Ids деятельностей
        $organizations = Organization::whereHas('activities', fn($q) => $q->whereIn('activities.id', $ids))
            ->with(['building', 'OrganizationPhones', 'activities'])
            ->get();

        return response()->json($organizations);
    }

    // рекурсивно получить все id подкатегорий
    private function getAllActivityIds(Activity $activity, &$ids = [])
    {
        $ids[] = $activity->id;
        foreach ($activity->children as $child) {
            $this->getAllActivityIds($child, $ids);
        }

        return $ids;
    }

    /**
     * @OA\Get(
     *     path="/organizations/{organization}",
     *     summary="Получить информацию об организации",
     *     tags={"Organizations"},
     *     security={{"api_key":{}}},
     *     @OA\Parameter(
     *         name="organization",
     *         in="path",
     *         required=true,
     *         description="ID организации",
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Детальная информация об организации",
     *         @OA\JsonContent(ref="#/components/schemas/Organization")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Организация не найдена"
     *     )
     * )
     */
    public function show(Organization $organization)
    {
        return response()->json($organization->load(["building", "OrganizationPhones", "activities"]));
    }
}
