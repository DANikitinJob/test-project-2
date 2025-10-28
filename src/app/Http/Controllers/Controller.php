<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Test Project 2 API Documentation",
 *     description="API документация для проекта с организациями и зданиями",
 *     @OA\Contact(
 *         email="steamnikitin@mail.ru"
 *     )
 * )
 * 
 * @OA\Server(
 *     url="/api",
 *     description="API Server"
 * )
 * 
 * @OA\SecurityScheme(
 *     type="apiKey",
 *     in="header",
 *     name="X-API-KEY",
 *     securityScheme="api_key",
 *     description="Введите ваш API ключ"
 * )
 */
abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
