<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Notifications\Action;

class OrganizationController extends Controller
{
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

    public function show(Organization $organization)
    {
        return response()->json($organization->load(["building", "OrganizationPhones", "activities"]));
    }
}
