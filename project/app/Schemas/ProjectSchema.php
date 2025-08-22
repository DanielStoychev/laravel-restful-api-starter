<?php

namespace App\Schemas;

/**
 * @OA\Schema(
 *     schema="Project",
 *     type="object",
 *     title="Project",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Sample Project"),
 *     @OA\Property(property="description", type="string", example="Project description"),
 *     @OA\Property(property="status", type="string", enum={"pending", "active", "completed", "cancelled"}, example="pending"),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="start_date", type="string", format="date", example="2024-01-01"),
 *     @OA\Property(property="end_date", type="string", format="date", example="2024-12-31"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T00:00:00.000000Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01T00:00:00.000000Z"),
 *     @OA\Property(
 *         property="tasks",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/Task")
 *     )
 * )
 */
class ProjectSchema {}
