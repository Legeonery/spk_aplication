<?php

namespace App\Http\Controllers\Swagger;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
/**
 * @OA\Info(
 *     title="SPK API",
 *     version="1.0.0",
 *     description="Документация API для системы учёта СПК Земледелец"
 * )
 * @OA\PathItem(
 *      path="/api/"
 * ),
 * 
 * @OA\Compontents(
 *     @OA\SecurityScheme(
 *         securityScheme = "bearerAuth",
 *         type = "http",
 *         scheme = "bearer"
 *     )
 * )
 * @OA\Tag(
 *     name="Post",
 *     description="Управление записями постов"
 * )
 * @OA\Tag(
 *     name="WarehousesPost",
 *     description="Операции с данными складов"
 * )
 * @OA\Tag(
 *     name="GrainDeliveries",
 *     description="Поставки зерна"
 * )
 * @OA\Tag(
 *     name="GrainShipments",
 *     description="Отгрузки зерна"
 * )
 * @OA\Tag(
 *     name="GrainLogs",
 *     description="История изменений по поставкам/отгрузкам"
 * )
 * @OA\Tag(
 *     name="SpareParts",
 *     description="Учёт запчастей"
 * )
 * @OA\Tag(
 *     name="Users",
 *     description="Управление пользователями"
 * )
 * @OA\Tag(
 *     name="Roles",
 *     description="Справочник ролей"
 * )
 * @OA\Tag(
 *     name="LicenseCategories",
 *     description="Категории водительских прав"
 * )
 * @OA\Tag(
 *     name="TareMeasurements",
 *     description="Замеры тары транспортных средств"
 * )
 * @OA\Tag(
 *     name="Vehicles",
 *     description="Транспорт"
 * )
 * @OA\Tag(
 *     name="Auth",
 *     description="Авторизация"
 * )
 */
class MainController extends Controller
{
    //
}
