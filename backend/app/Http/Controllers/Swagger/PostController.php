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
 */
class PostController extends Controller {}
class WarehousesController extends Controller {}
