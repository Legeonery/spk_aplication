<?php

namespace App\Http\Controllers\Swagger;

use App\Http\Controllers\Controller;

/**
 * @OA\Post(
 *     path = "/api/posts",
 *     summary = "Создание записи",
 *     tags = { "Post" },
 *     security = {{ "bearerAuth": {} }},
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *             allOf = {
 *                 @OA\Schema(
 *                     @OA\Property(property = "title", type = "string", example = "some title"),
 *                     @OA\Property(property = "likes", type = "integer", example = 20),
 *                 )
 *             }
 *         )
 *     ),
 *     @OA\Response(
 *         response = 200,
 *         description = "OK",
 *         @OA\JsonContent(
 *             @OA\Property(property = "data", type = "object",
 *                 @OA\Property(property = "id", type = "integer", example = 1),
 *                 @OA\Property(property = "title", type = "string", example = "some title"),
 *                 @OA\Property(property = "likes", type = "integer", example = 20),
 *             ),
 *         ),
 *     ),
 * ),
 * @OA\Get(
 *     path = "/api/posts",
 *     summary = "Получение списка записей",
 *     tags = { "Post" },
 *     security = {{ "bearerAuth": {} }},
 *     @OA\Response(
 *         response = 200,
 *         description = "OK",
 *         @OA\JsonContent(
 *             @OA\Property(property = "data", type = "array",@OA\Items(
 *                 @OA\Property(property = "id", type = "integer", example = 1),
 *                 @OA\Property(property = "title", type = "string", example = "some title"),
 *                 @OA\Property(property = "likes", type = "integer", example = 20), 
 *             )),
 *         ),
 *     ),
 * ),
 * @OA\Get(
 *     path = "/api/posts/{post}",
 *     summary = "Получение единичной записи",
 *     tags = { "Post" },
 *     security = {{ "bearerAuth": {} }},
 *     @OA\Parameter(
 *         description = "ID записи",
 *         in = "path",
 *         name = "post",
 *         required = true,
 *         example = 1
 *     ),
 *     @OA\Response(
 *         response = 200,
 *         description = "OK",
 *         @OA\JsonContent(
 *             @OA\Property(property = "data", type = "object",
 *                 @OA\Property(property = "id", type = "integer", example = 1),
 *                 @OA\Property(property = "title", type = "string", example = "some title"),
 *                 @OA\Property(property = "likes", type = "integer", example = 20),
 *             ),
 *         ),
 *     ),
 *     @OA\Response(
 *         response = 404,
 *         description = "Запись с данным индификатором не найдена",
 *         @OA\JsonContent(
 *             @OA\Property(property = "message", type = "string", example = "No query results for model [App\\Models\\Post] 1")
 *         )
 *     ),
 * ),
 * @OA\Patch(
 *     path = "/api/posts/{post}",
 *     summary = "Обновление записи",
 *     tags = { "Post" },
 *     security = {{ "bearerAuth": {} }},
 *     @OA\Parameter(
 *         description = "ID записи",
 *         in = "path",
 *         name = "post",
 *         required = true,
 *         example = 1
 *     ),
 * 
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *             allOf = {
 *                 @OA\Schema(
 *                     @OA\Property(property = "title", type = "string", example = "some title for you"),
 *                     @OA\Property(property = "likes", type = "integer", example = 25),
 *                 )
 *             }
 *         )
 *     ),
 * 
 *     @OA\Response(
 *         response = 200,
 *         description = "OK",
 *         @OA\JsonContent(
 *             @OA\Property(property = "data", type = "object",
 *                 @OA\Property(property = "id", type = "integer", example = 1),
 *                 @OA\Property(property = "title", type = "string", example = "some title for you"),
 *                 @OA\Property(property = "likes", type = "integer", example = 20),
 *             ),
 *         ),
 *     ),
 * ),
 * 
 * @OA\Delete(
 *     path = "/api/posts/{post}",
 *     summary = "Удаление",
 *     tags = { "Post" },
 *     security = {{ "bearerAuth": {} }},
 *     @OA\Parameter(
 *         description = "ID записи",
 *         in = "path",
 *         name = "post",
 *         required = true,
 *         example = 1
 *     ),
 *     @OA\Response(
 *         response = 200,
 *         description = "OK",
 *         @OA\JsonContent(
 *             @OA\Property(property = "message", type = "string", example = "done"),
 *         ),
 *     ),
 * ),
 * @OA\Post(
 *     path = "/api/auth/login",
 *     summary = "Получение токека авторизации",
 *     tags = {"Auth"},
 *     @OA\RequestBody(
 *         required = true,
 *         @OA\JsonContent(
 *             required = {"email", "password"},
 *             @OA\Property(property = "email", type = "string", format = "email", example = "YourEmail@example.com"),
 *             @OA\Property(property = "password", type = "string", format = "password", example = "YourPassword")
 *         )
 *     ),
 *     @OA\Response(
 *         response = 200,
 *         description = "Успешная авторизация",
 *         @OA\JsonContent(
 *             @OA\Property(property = "access_token", type = "string", example = "wOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2F1dGgvbG9naW4i"),
 *             @OA\Property(property = "token_type", type = "string", example = "bearer"),
 *             @OA\Property(property = "expires_in", type = "integer", example = 3600)
 *         )
 *     ),
 *     @OA\Response(
 *         response = 401,
 *         description = "Ошибка авторизации",
 *         @OA\JsonContent(
 *             @OA\Property(property = "message", type = "string", example = "Invalid credentials")
 *         )
 *     )
 * ),
 * @OA\Post(
 *     path = "/api/warehouses",
 *     summary = "Создание записи о складе",
 *     tags = { "WarehousesPost" },
 *     security = {{ "bearerAuth": {} }},
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *             allOf = {
 *                 @OA\Schema(
 *                 @OA\Property(property = "name", type = "string", example = "some title"),
 *                 @OA\Property(property = "type", type="string",enum={"Зерновой", "Склад запчастей", "Общий"}, example = "Зерновой"),
 *                 @OA\Property(property = "area", type = "integer", example = 2000),
 *                 @OA\Property(property = "status", type = "integer", example = 1),
 *                 @OA\Property(property = "max_historical_load", type = "integer", example = 2000),
 *                 )
 *             }
 *         )
 *     ),
 *     @OA\Response(
 *         response = 200,
 *         description = "OK",
 *         @OA\JsonContent(
 *             @OA\Property(property = "data", type = "object",
 *                 @OA\Property(property = "id", type = "integer", example = 1),
 *                 @OA\Property(property = "name", type = "string", example = "some title"),
 *                 @OA\Property(property = "type", type="string",enum={"Зерновой", "Склад запчастей", "Общий"}, example = "Зерновой"),
 *                 @OA\Property(property = "area", type = "integer", example = 2000),
 *                 @OA\Property(property = "status", type = "integer", example = 1),
 *                 @OA\Property(property = "max_historical_load", type = "integer", example = 2000),
 *             ),
 *         ),
 *     ),
 * ),
 *@OA\Get(
 *     path = "/api/warehouses",
 *     summary = "Получение списка записей складов",
 *     tags = { "WarehousesPost" },
 *     security = {{ "bearerAuth": {} }},
 *     @OA\Response(
 *         response = 200,
 *         description = "OK",
 *         @OA\JsonContent(
 *             @OA\Property(property = "data", type = "array",@OA\Items(
 *                 @OA\Property(property = "id", type = "integer", example = 1),
 *                 @OA\Property(property = "name", type = "string", example = "some title"),
 *                 @OA\Property(property = "type", type="string",enum={"Зерновой", "Склад запчастей", "Общий"}, example = "Зерновой"),
 *                 @OA\Property(property = "status", type = "integer", example = 1),
 *                 @OA\Property(property = "area", type = "integer", example = 2000),
 *                 @OA\Property(property = "max_historical_load", type = "integer", example = 2000),
 *             )),
 *         ),
 *     ),
 * ),
 * @OA\Get(
 *     path = "/api/warehouses/{warehouse}",
 *     summary = "Получение единичной записи склада",
 *     tags = { "WarehousesPost" },
 *     security = {{ "bearerAuth": {} }},
 *     @OA\Parameter(
 *         description = "ID склада",
 *         in = "path",
 *         name = "warehouse",
 *         required = true,
 *         example = 1
 *     ),
 *     @OA\Response(
 *         response = 200,
 *         description = "OK",
 *         @OA\JsonContent(
 *             @OA\Property(property = "data", type = "object",
 *                 @OA\Property(property = "id", type = "integer", example = 1),
 *                 @OA\Property(property = "name", type = "string", example = "some title"),
 *                 @OA\Property(property = "type", type="string",enum={"Зерновой", "Склад запчастей", "Общий"}, example = "Зерновой"),
 *                 @OA\Property(property = "area", type = "integer", example = 2000),
 *                 @OA\Property(property = "status", type = "integer", example = 1),
 *                 @OA\Property(property = "max_historical_load", type = "integer", example = 2000),
 *             ),
 *         ),
 *     ),
 *     @OA\Response(
 *         response = 404,
 *         description = "Запись с данным индификатором не найдена",
 *         @OA\JsonContent(
 *             @OA\Property(property = "message", type = "string", example = "No query results for model [App\\Models\\Post] 1")
 *         )
 *     ),
 * ),
 * @OA\Patch(
 *     path = "/api/warehouses/{warehouse}",
 *     summary = "Обновление записи о складе",
 *     tags = { "WarehousesPost" },
 *     security = {{ "bearerAuth": {} }},
 *     @OA\Parameter(
 *         description = "ID склада",
 *         in = "path",
 *         name = "warehouse",
 *         required = true,
 *         example = 1
 *     ),
 * 
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *             allOf = {
 *                 @OA\Schema(
 *                     @OA\Property(property = "name", type = "string", example = "some title"),
 *                     @OA\Property(property = "type", type="string",enum={"Зерновой", "Склад запчастей", "Общий"}, example = "Зерновой"),
 *                     @OA\Property(property = "area", type = "integer", example = 2000),
 *                     @OA\Property(property = "status", type = "integer", example = 1),
 *                     @OA\Property(property = "max_historical_load", type = "integer", example = 2000),
 *                 )
 *             }
 *         )
 *     ),
 * 
 *     @OA\Response(
 *         response = 200,
 *         description = "OK",
 *         @OA\JsonContent(
 *             @OA\Property(property = "data", type = "object",
 *                 @OA\Property(property = "id", type = "integer", example = 1),
 *                 @OA\Property(property = "name", type = "string", example = "some title"),
 *                 @OA\Property(property = "type", type="string",enum={"Зерновой", "Склад запчастей", "Общий"}, example = "Зерновой"),
 *                 @OA\Property(property = "area", type = "integer", example = 2000),
 *                 @OA\Property(property = "status", type = "integer", example = 1),
 *                 @OA\Property(property = "max_historical_load", type = "integer", example = 2000),
 *             ),
 *         ),
 *     ),
 * ),
 * 
 * @OA\Delete(
 *     path = "/api/warehouses/{warehouse}",
 *     summary = "Удаление",
 *     tags = { "WarehousesPost" },
 *     security = {{ "bearerAuth": {} }},
 *     @OA\Parameter(
 *         description = "ID записи",
 *         in = "path",
 *         name = "warehouse",
 *         required = true,
 *         example = 1
 *     ),
 *     @OA\Response(
 *         response = 200,
 *         description = "OK",
 *         @OA\JsonContent(
 *             @OA\Property(property = "message", type = "string", example = "done"),
 *         ),
 *     ),
 * ),
 *
 * @OA\Get(
 *     path = "/api/warehouses/{warehouse}/grains",
 *     summary = "Получение остатков по зерновым культурам на складе",
 *     tags = { "WarehousesPost" },
 *     security = {{ "bearerAuth": {} }},
 *     @OA\Parameter(
 *         description = "ID склада",
 *         in = "path",
 *         name = "warehouse",
 *         required = true,
 *         example = 1
 *     ),
 *     @OA\Response(
 *         response = 200,
 *         description = "Успешно",
 *         @OA\JsonContent(
 *             type = "array",
 *             @OA\Items(
 *                 @OA\Property(property = "id", type = "integer", example = 1),
 *                 @OA\Property(property = "warehouse_id", type = "integer", example = 1),
 *                 @OA\Property(property = "grain_type_id", type = "integer", example = 2),
 *                 @OA\Property(property = "amount", type = "integer", example = 1500),
 *                 @OA\Property(property = "grain_type", type = "object",
 *                     @OA\Property(property = "id", type = "integer", example = 2),
 *                     @OA\Property(property = "name", type = "string", example = "Пшеница")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response = 401,
 *         description = "Ошибка авторизации",
 *         @OA\JsonContent(
 *             @OA\Property(property = "message", type = "string", example = "Unauthenticated.")
 *         )
 *     ),
 *     @OA\Response(
 *         response = 404,
 *         description = "Склад не найден",
 *         @OA\JsonContent(
 *             @OA\Property(property = "message", type = "string", example = "Not Found")
 *         )
 *     )
 * )
 * @OA\Post(
 *     path="/api/grain-deliveries",
 *     summary="Добавление поставки зерна",
 *     tags={"GrainDeliveries"},
 *     security={{"bearerAuth": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"warehouse_id","grain_type_id","volume","delivery_date"},
 *             @OA\Property(property="warehouse_id", type="integer", example=1),
 *             @OA\Property(property="grain_type_id", type="integer", example=2),
 *             @OA\Property(property="volume", type="number", format="float", example=1500),
 *             @OA\Property(property="delivery_date", type="string", format="date", example="2025-06-01"),
 *             @OA\Property(property="vehicle_id", type="integer", example=3),
 *             @OA\Property(property="driver_id", type="integer", example=5)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Поставка добавлена",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Поставка добавлена"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="warehouse_id", type="integer", example=1),
 *                 @OA\Property(property="grain_type_id", type="integer", example=2),
 *                 @OA\Property(property="volume", type="number", example=1500),
 *                 @OA\Property(property="delivery_date", type="string", example="2025-06-01")
 *             )
 *         )
 *     )
 * ),
 *
 * @OA\Get(
 *     path="/api/warehouses/{warehouse}/deliveries",
 *     summary="Получение истории поставок по складу",
 *     tags={"GrainDeliveries"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="warehouse",
 *         in="path",
 *         description="ID склада",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Список поставок",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="warehouse_id", type="integer", example=1),
 *                 @OA\Property(property="grain_type_id", type="integer", example=2),
 *                 @OA\Property(property="grain_type", type="object",
 *                     @OA\Property(property="id", type="integer", example=2),
 *                     @OA\Property(property="name", type="string", example="Пшеница")
 *                 ),
 *                 @OA\Property(property="volume", type="number", example=1500),
 *                 @OA\Property(property="delivery_date", type="string", example="2025-06-01"),
 *                 @OA\Property(property="driver", type="object",
 *                     @OA\Property(property="id", type="integer", example=5),
 *                     @OA\Property(property="name", type="string", example="Иван Иванов")
 *                 )
 *             )
 *         )
 *     )
 * ),
 *
 * @OA\Get(
 *     path="/api/warehouses/{warehouse}/shipments",
 *     summary="Получение истории отгрузок по складу",
 *     tags={"GrainShipments"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="warehouse",
 *         in="path",
 *         description="ID склада",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Список отгрузок",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="warehouse_id", type="integer", example=1),
 *                 @OA\Property(property="grain_type_id", type="integer", example=2),
 *                 @OA\Property(property="grain_type", type="object",
 *                     @OA\Property(property="id", type="integer", example=2),
 *                     @OA\Property(property="name", type="string", example="Пшеница")
 *                 ),
 *                 @OA\Property(property="volume", type="number", example=1000),
 *                 @OA\Property(property="shipment_date", type="string", example="2025-06-15"),
 *                 @OA\Property(property="driver", type="object",
 *                     @OA\Property(property="id", type="integer", example=3),
 *                     @OA\Property(property="name", type="string", example="Пётр Петров")
 *                 )
 *             )
 *         )
 *     )
 * ),
 *
 * @OA\Post(
 *     path="/api/grain-shipments",
 *     summary="Добавление отгрузки зерна",
 *     tags={"GrainShipments"},
 *     security={{"bearerAuth": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"warehouse_id","grain_type_id","volume","shipment_date"},
 *             @OA\Property(property="warehouse_id", type="integer", example=1),
 *             @OA\Property(property="grain_type_id", type="integer", example=2),
 *             @OA\Property(property="volume", type="number", format="float", example=1000),
 *             @OA\Property(property="shipment_date", type="string", format="date", example="2025-06-15"),
 *             @OA\Property(property="vehicle_id", type="integer", example=1),
 *             @OA\Property(property="driver_id", type="integer", example=3)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Отгрузка добавлена",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Отгрузка добавлена"),
 *         )
 *     )
 * )
 * 
 * @OA\Put(
 *     path="/api/grain-deliveries/{delivery}",
 *     summary="Обновление поставки зерна",
 *     tags={"GrainDeliveries"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="delivery",
 *         in="path",
 *         description="ID поставки",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="warehouse_id", type="integer", example=1),
 *             @OA\Property(property="grain_type_id", type="integer", example=2),
 *             @OA\Property(property="volume", type="number", example=1400),
 *             @OA\Property(property="delivery_date", type="string", format="date", example="2025-06-01"),
 *             @OA\Property(property="vehicle_id", type="integer", example=3),
 *             @OA\Property(property="driver_id", type="integer", example=5)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Поставка обновлена",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Поставка обновлена"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="volume", type="number", example=1400),
 *                 @OA\Property(property="updated_at", type="string", example="2025-06-01T12:00:00Z")
 *             )
 *         )
 *     )
 * )
 * @OA\Put(
 *     path="/api/grain-shipments/{shipment}",
 *     summary="Обновление отгрузки зерна",
 *     tags={"GrainShipments"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="shipment",
 *         in="path",
 *         description="ID отгрузки",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="warehouse_id", type="integer", example=1),
 *             @OA\Property(property="grain_type_id", type="integer", example=2),
 *             @OA\Property(property="volume", type="number", example=950),
 *             @OA\Property(property="shipment_date", type="string", format="date", example="2025-06-17"),
 *             @OA\Property(property="vehicle_id", type="integer", example=1),
 *             @OA\Property(property="driver_id", type="integer", example=3)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Отгрузка обновлена",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Отгрузка обновлена"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="volume", type="number", example=950),
 *                 @OA\Property(property="updated_at", type="string", example="2025-06-01T12:00:00Z")
 *             )
 *         )
 *     )
 * )
 * 
 * @OA\Get(
 *     path="/api/grain-logs/{entityType}/{entityId}",
 *     summary="История изменений поставки или отгрузки",
 *     tags={"GrainLogs"},
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(name="entityType", in="path", required=true, example="shipment"),
 *     @OA\Parameter(name="entityId", in="path", required=true, example=1),
 *     @OA\Response(
 *         response=200,
 *         description="История изменений",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 @OA\Property(property="id", type="integer"),
 *                 @OA\Property(property="entity_type", type="string"),
 *                 @OA\Property(property="entity_id", type="integer"),
 *                 @OA\Property(property="changes", type="object"),
 *                 @OA\Property(property="action", type="string", example="updated"),
 *                 @OA\Property(property="created_at", type="string", example="2025-06-01T12:00:00Z")
 *             )
 *         )
 *     )
 * )
 */
 
class PostController extends Controller
{
}
class WarehousesController extends Controller
{
}
