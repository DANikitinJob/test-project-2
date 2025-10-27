<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ActivityController extends Controller
{
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
    public function organizations(Activity $activity)
    {
        // Получаем организации, связанные с данной активностью, включая вложенные подкатегории
        $organizations = $activity->organizations()->with("OrganizationPhones", "activities", "building")->get();

        return response()->json($organizations);

    }
}
