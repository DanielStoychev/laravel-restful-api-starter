<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     title="User",
 *     description="User model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *     @OA\Property(property="role", type="string", example="user"),
 *     @OA\Property(property="email_verified_at", type="string", format="datetime", nullable=true, example="2024-01-01T00:00:00.000000Z"),
 *     @OA\Property(property="created_at", type="string", format="datetime", example="2024-01-01T00:00:00.000000Z"),
 *     @OA\Property(property="updated_at", type="string", format="datetime", example="2024-01-01T00:00:00.000000Z")
 * )
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'email_verified_at' => $this->email_verified_at?->toISOString(),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            
            // Include additional data when requested
            'projects_count' => $this->when(
                $this->relationLoaded('projects'),
                fn() => $this->projects->count()
            ),
            
            'assigned_tasks_count' => $this->when(
                $this->relationLoaded('assignedTasks'),
                fn() => $this->assignedTasks->count()
            ),
            
            // Include last login information for admin users
            'last_login_at' => $this->when(
                $request->user()?->isAdmin() && isset($this->last_login_at),
                $this->last_login_at?->toISOString()
            ),
            
            // Include sensitive information only for the authenticated user
            'email_verification_sent_at' => $this->when(
                $request->user()?->id === $this->id && isset($this->email_verification_sent_at),
                $this->email_verification_sent_at?->toISOString()
            ),
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @return array<string, mixed>
     */
    public function with(Request $request): array
    {
        return [
            'meta' => [
                'permissions' => $this->getPermissions($request),
            ],
        ];
    }

    /**
     * Get user permissions based on role
     *
     * @param Request $request
     * @return array
     */
    private function getPermissions(Request $request): array
    {
        $permissions = [
            'can_create_projects' => true,
            'can_view_own_projects' => true,
            'can_edit_own_projects' => true,
            'can_delete_own_projects' => true,
        ];

        if ($this->isAdmin()) {
            $permissions = array_merge($permissions, [
                'can_view_all_projects' => true,
                'can_edit_all_projects' => true,
                'can_delete_all_projects' => true,
                'can_manage_users' => true,
                'can_view_admin_panel' => true,
            ]);
        }

        return $permissions;
    }
}
