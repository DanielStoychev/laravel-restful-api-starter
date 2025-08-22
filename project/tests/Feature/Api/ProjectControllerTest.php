<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private array $headers;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->headers = $this->authHeaders($this->user);
    }

    /**
     * Test getting list of user's projects
     */
    public function test_user_can_get_their_projects(): void
    {
        // Create projects for authenticated user
        Project::factory()->count(3)->create(['user_id' => $this->user->id]);
        
        // Create projects for other user (should not be returned)
        $otherUser = User::factory()->create();
        Project::factory()->count(2)->create(['user_id' => $otherUser->id]);

        $response = $this->withHeaders($this->headers)
            ->getJson('/api/projects');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'description',
                        'status',
                        'start_date',
                        'end_date',
                        'created_at',
                        'updated_at',
                        'user_id',
                    ]
                ],
                'current_page',
                'per_page',
                'total',
            ],
        ]);

        // Should only return the user's 3 projects
        $this->assertEquals(3, $response->json('data.total'));
    }

    /**
     * Test unauthenticated user cannot access projects
     */
    public function test_unauthenticated_user_cannot_access_projects(): void
    {
        $response = $this->getJson('/api/projects');

        $response->assertStatus(401);
    }

    /**
     * Test creating a new project
     */
    public function test_user_can_create_project(): void
    {
        $projectData = [
            'name' => 'New Test Project',
            'description' => 'A new test project description',
            'status' => 'pending',
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addMonth()->toDateString(),
        ];

        $response = $this->withHeaders($this->headers)
            ->postJson('/api/projects', $projectData);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'name',
                'description',
                'status',
                'start_date',
                'end_date',
                'created_at',
                'updated_at',
                'user_id',
            ],
        ]);

        $this->assertDatabaseHas('projects', [
            'name' => 'New Test Project',
            'description' => 'A new test project description',
            'status' => 'pending',
            'user_id' => $this->user->id,
        ]);

        $this->assertEquals($this->user->id, $response->json('data.user_id'));
    }

    /**
     * Test creating project with invalid data
     */
    public function test_create_project_validation_fails_with_invalid_data(): void
    {
        $invalidData = [
            'name' => '', // Required
            'description' => str_repeat('a', 1001), // Too long
            'status' => 'invalid-status',
            'start_date' => 'invalid-date',
            'end_date' => 'invalid-date',
        ];

        $response = $this->withHeaders($this->headers)
            ->postJson('/api/projects', $invalidData);

        $this->assertValidationError($response, ['name', 'status', 'start_date', 'end_date']);
    }

    /**
     * Test viewing a specific project
     */
    public function test_user_can_view_their_project(): void
    {
        $project = Project::factory()->create(['user_id' => $this->user->id]);

        $response = $this->withHeaders($this->headers)
            ->getJson('/api/projects/' . $project->id);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'id',
                'name',
                'description',
                'status',
                'start_date',
                'end_date',
                'created_at',
                'updated_at',
                'user_id',
                'tasks', // Should include related tasks
            ],
        ]);

        $this->assertEquals($project->id, $response->json('data.id'));
        $this->assertEquals($this->user->id, $response->json('data.user_id'));
    }

    /**
     * Test user cannot view other user's project
     */
    public function test_user_cannot_view_other_users_project(): void
    {
        $otherUser = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->withHeaders($this->headers)
            ->getJson('/api/projects/' . $project->id);

        $response->assertStatus(403);
    }

    /**
     * Test viewing non-existent project
     */
    public function test_viewing_non_existent_project_returns_404(): void
    {
        $response = $this->withHeaders($this->headers)
            ->getJson('/api/projects/99999');

        $response->assertStatus(404);
    }

    /**
     * Test updating a project
     */
    public function test_user_can_update_their_project(): void
    {
        $project = Project::factory()->create(['user_id' => $this->user->id]);

        $updateData = [
            'name' => 'Updated Project Name',
            'description' => 'Updated project description',
            'status' => 'active',
        ];

        $response = $this->withHeaders($this->headers)
            ->putJson('/api/projects/' . $project->id, $updateData);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'name',
                'description',
                'status',
                'start_date',
                'end_date',
                'created_at',
                'updated_at',
                'user_id',
            ],
        ]);

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'Updated Project Name',
            'description' => 'Updated project description',
            'status' => 'active',
            'user_id' => $this->user->id,
        ]);

        $this->assertEquals('Updated Project Name', $response->json('data.name'));
    }

    /**
     * Test user cannot update other user's project
     */
    public function test_user_cannot_update_other_users_project(): void
    {
        $otherUser = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $otherUser->id]);

        $updateData = [
            'name' => 'Attempted Update',
            'status' => 'active',
        ];

        $response = $this->withHeaders($this->headers)
            ->putJson('/api/projects/' . $project->id, $updateData);

        $response->assertStatus(403);

        // Ensure project was not updated
        $this->assertDatabaseMissing('projects', [
            'id' => $project->id,
            'name' => 'Attempted Update',
        ]);
    }

    /**
     * Test updating project with invalid data
     */
    public function test_update_project_validation_fails_with_invalid_data(): void
    {
        $project = Project::factory()->create(['user_id' => $this->user->id]);

        $invalidData = [
            'name' => '',
            'status' => 'invalid-status',
            'start_date' => 'invalid-date',
        ];

        $response = $this->withHeaders($this->headers)
            ->putJson('/api/projects/' . $project->id, $invalidData);

        $this->assertValidationError($response, ['name', 'status', 'start_date']);
    }

    /**
     * Test deleting a project
     */
    public function test_user_can_delete_their_project(): void
    {
        $project = Project::factory()->create(['user_id' => $this->user->id]);

        $response = $this->withHeaders($this->headers)
            ->deleteJson('/api/projects/' . $project->id);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Project deleted successfully',
        ]);

        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }

    /**
     * Test user cannot delete other user's project
     */
    public function test_user_cannot_delete_other_users_project(): void
    {
        $otherUser = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->withHeaders($this->headers)
            ->deleteJson('/api/projects/' . $project->id);

        $response->assertStatus(403);

        // Ensure project still exists
        $this->assertDatabaseHas('projects', ['id' => $project->id]);
    }

    /**
     * Test deleting non-existent project
     */
    public function test_deleting_non_existent_project_returns_404(): void
    {
        $response = $this->withHeaders($this->headers)
            ->deleteJson('/api/projects/99999');

        $response->assertStatus(404);
    }

    /**
     * Test project list pagination
     */
    public function test_project_list_pagination_works(): void
    {
        // Create 25 projects for pagination testing
        Project::factory()->count(25)->create(['user_id' => $this->user->id]);

        $response = $this->withHeaders($this->headers)
            ->getJson('/api/projects?page=1&per_page=10');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'data',
                'current_page',
                'per_page',
                'total',
                'last_page',
                'from',
                'to',
            ],
        ]);

        $this->assertEquals(1, $response->json('data.current_page'));
        $this->assertEquals(10, $response->json('data.per_page'));
        $this->assertEquals(25, $response->json('data.total'));
        $this->assertEquals(3, $response->json('data.last_page'));
        $this->assertCount(10, $response->json('data.data'));
    }

    /**
     * Test project filtering by status
     */
    public function test_project_list_can_be_filtered_by_status(): void
    {
        Project::factory()->create(['user_id' => $this->user->id, 'status' => 'active']);
        Project::factory()->create(['user_id' => $this->user->id, 'status' => 'completed']);
        Project::factory()->create(['user_id' => $this->user->id, 'status' => 'active']);

        $response = $this->withHeaders($this->headers)
            ->getJson('/api/projects?status=active');

        $response->assertStatus(200);
        $this->assertEquals(2, $response->json('data.total'));
        
        // All returned projects should have 'active' status
        $projects = $response->json('data.data');
        foreach ($projects as $project) {
            $this->assertEquals('active', $project['status']);
        }
    }

    /**
     * Test project with tasks relationship
     */
    public function test_project_includes_tasks_when_requested(): void
    {
        $project = Project::factory()->create(['user_id' => $this->user->id]);
        
        // Create some tasks for this project
        \App\Models\Task::factory()->count(3)->create([
            'project_id' => $project->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->withHeaders($this->headers)
            ->getJson('/api/projects/' . $project->id . '?include=tasks');

        $response->assertStatus(200);
        $this->assertArrayHasKey('tasks', $response->json('data'));
        $this->assertCount(3, $response->json('data.tasks'));
    }
}
