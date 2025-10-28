<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @OA\Tag(
 *     name="Activities",
 *     description="API Endpoints для работы с видами деятельности"
 * )
 */
class ActivityController extends Controller
{
    /**
     * @OA\Post(
     *     path="/activities/store",
     *     summary="Создать новый вид деятельности",
     *     tags={"Activities"},
     *     security={{"api_key":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", maxLength=255),
     *             @OA\Property(property="parent_id", type="integer", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Вид деятельности успешно создан",
     *         @OA\JsonContent(ref="#/components/schemas/Activity")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Ошибка валидации или превышена глубина вложенности"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:activities,id',
        ]);

        // Проверим уровень вложенности
        $level = $this->getActivityDepth($request->input('parent_id'));

        if ($level >= 3) {
            return response()->json([
                'error' => 'Превышена вложенность деятельностей'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $activity = Activity::create($request->only('name', 'parent_id'));

        return response()->json($activity, Response::HTTP_CREATED);
    }

    // Получить рекурсивно глубину деятельностей
    private function getActivityDepth($parentId, $depth = 1)
    {
        if (!$parentId) {
            return $depth; // это корневой уровень
        }

        $parent = Activity::find($parentId);

        if (!$parent || !$parent->parent_id) {
            return $depth + 1; // достигнли корневого уровеня
        }

        return $this->getActivityDepth($parent->parent_id, $depth + 1);
    }
    /**
     * @OA\Get(
     *     path="/activities/{activity}/organizations",
     *     summary="Получить организации по виду деятельности",
     *     tags={"Activities"},
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
     *         description="Список организаций",
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
    public function organizations(Activity $activity)
    {
        // Получаем организации, связанные с данной активностью, включая вложенные подкатегории
        $organizations = $activity->organizations()->with("OrganizationPhones", "activities", "building")->get();

        return response()->json($organizations);

    }
}
