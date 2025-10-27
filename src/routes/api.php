<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\OrganizationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::middleware('apikey')->group(function () {

    // Список всех зданий
    Route::get('buildings', [BuildingController::class, 'index']);

    // Список организаций, находящихся в конкретном здании
    Route::get('buildings/{building}/organizations', [BuildingController::class, 'organizations']);

    // Список организаций по виду деятельности
    Route::get('activities/{activity}/organizations', [ActivityController::class, 'organizations']);

    // Список организаций в радиусе / прямоугольной области
    Route::get('organizations/geo/search', [OrganizationController::class, 'geoSearch']);

    // Поиск по названию
    Route::get('organizations/search', [OrganizationController::class, 'searchByName']);

    // Поиск по виду деятельности (включая вложенные)
    Route::get('organizations/activity/{activity}', [OrganizationController::class, 'searchByActivity']);

    // Информация об организации по ID
    Route::get('organizations/{organization}', [OrganizationController::class, 'show']);

    // Создание новой активности
    Route::post('activities/store', [ActivityController::class, 'store']);
});