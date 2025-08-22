<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        // Don't seed the database automatically in tests
        // Individual tests will create their own data as needed
    }

    /**
     * Create a user for testing
     */
    protected function createUser(array $attributes = []): \App\Models\User
    {
        return \App\Models\User::factory()->create($attributes);
    }

    /**
     * Create an admin user for testing
     */
    protected function createAdmin(array $attributes = []): \App\Models\User
    {
        return \App\Models\User::factory()->create(array_merge([
            'role' => 'admin'
        ], $attributes));
    }

    /**
     * Create an authenticated user and return auth headers
     */
    protected function authHeaders(\App\Models\User $user = null): array
    {
        $user = $user ?: $this->createUser();
        $token = $user->createToken('test-token')->plainTextToken;

        return [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * Create a project for testing
     */
    protected function createProject(\App\Models\User $user = null, array $attributes = []): \App\Models\Project
    {
        $user = $user ?: $this->createUser();
        
        return \App\Models\Project::factory()->create(array_merge([
            'user_id' => $user->id
        ], $attributes));
    }

    /**
     * Create a task for testing
     */
    protected function createTask(\App\Models\Project $project = null, \App\Models\User $user = null, array $attributes = []): \App\Models\Task
    {
        $user = $user ?: $this->createUser();
        $project = $project ?: $this->createProject($user);
        
        return \App\Models\Task::factory()->create(array_merge([
            'project_id' => $project->id,
            'user_id' => $user->id
        ], $attributes));
    }

    /**
     * Assert API response structure
     */
    protected function assertApiResponse($response, int $status = 200): void
    {
        $response->assertStatus($status);
        $response->assertHeader('Content-Type', 'application/json');
        
        if ($status === 200) {
            $response->assertJsonStructure([
                'success',
                'data'
            ]);
        }
    }

    /**
     * Assert validation error response structure
     */
    protected function assertValidationError($response, array $fields = []): void
    {
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message',
            'errors'
        ]);

        if (!empty($fields)) {
            foreach ($fields as $field) {
                $response->assertJsonValidationErrors($field);
            }
        }
    }
}
