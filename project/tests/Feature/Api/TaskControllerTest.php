<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Project $project;
    private array $headers;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->project = Project::factory()->create(['user_id' => $this->user->id]);
        $this->headers = $this->authHeaders($this->user);
    }

    /**
     * Test getting list of user's tasks
     */
    public function test_user_can_get_their_tasks(): void
    {
        // Create tasks for authenticated user
        Task::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'project_id' => $this->project->id,
        ]);
        
        // Create tasks for other user (should not be returned)
        $otherUser = User::factory()->create();
        $otherProject = Project::factory()->create(['user_id' => $otherUser->id]);
        Task::factory()->count(2)->create([
            'user_id' => $otherUser->id,
            'project_id' => $otherProject->id,
        ]);

        $response = $this->withHeaders($this->headers)
            ->getJson('/api/tasks');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'description',
                        'status',
                        'priority',
                        'due_date',
                        'completed_at',
                        'created_at',
                        'updated_at',
                        'project_id',
                        'user_id',
                    ]
                ],
                'current_page',
                'per_page',
                'total',
            ],
        ]);

        // Should only return the user's 3 tasks
        $this->assertEquals(3, $response->json('data.total'));
    }

    /**
     * Test unauthenticated user cannot access tasks
     */
    public function test_unauthenticated_user_cannot_access_tasks(): void
    {
        $response = $this->getJson('/api/tasks');

        $response->assertStatus(401);
    }

    /**
     * Test creating a new task
     */
    public function test_user_can_create_task(): void
    {
        $taskData = [
            'title' => 'New Test Task',
            'description' => 'A new test task description',
            'status' => 'todo',
            'priority' => 'medium',
            'due_date' => now()->addWeek()->toDateString(),
            'project_id' => $this->project->id,
        ];

        $response = $this->withHeaders($this->headers)
            ->postJson('/api/tasks', $taskData);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'title',
                'description',
                'status',
                'priority',
                'due_date',
                'completed_at',
                'created_at',
                'updated_at',
                'project_id',
                'user_id',
            ],
        ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'New Test Task',
            'description' => 'A new test task description',
            'status' => 'todo',
            'priority' => 'medium',
            'user_id' => $this->user->id,
            'project_id' => $this->project->id,
        ]);

        $this->assertEquals($this->user->id, $response->json('data.user_id'));
        $this->assertEquals($this->project->id, $response->json('data.project_id'));
    }

    /**
     * Test creating task with invalid data
     */
    public function test_create_task_validation_fails_with_invalid_data(): void
    {
        $invalidData = [
            'title' => '', // Required
            'description' => str_repeat('a', 2001), // Too long
            'status' => 'invalid-status',
            'priority' => 'invalid-priority',
            'due_date' => 'invalid-date',
            'project_id' => 99999, // Non-existent project
        ];

        $response = $this->withHeaders($this->headers)
            ->postJson('/api/tasks', $invalidData);

        $this->assertValidationError($response, ['title', 'status', 'priority', 'due_date', 'project_id']);
    }

    /**
     * Test user cannot create task for other user's project
     */
    public function test_user_cannot_create_task_for_other_users_project(): void
    {
        $otherUser = User::factory()->create();
        $otherProject = Project::factory()->create(['user_id' => $otherUser->id]);

        $taskData = [
            'title' => 'Unauthorized Task',
            'description' => 'Should not be created',
            'status' => 'todo',
            'priority' => 'medium',
            'due_date' => now()->addWeek()->toDateString(),
            'project_id' => $otherProject->id,
        ];

        $response = $this->withHeaders($this->headers)
            ->postJson('/api/tasks', $taskData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors('project_id');
        $this->assertDatabaseMissing('tasks', ['title' => 'Unauthorized Task']);
    }

    /**
     * Test viewing a specific task
     */
    public function test_user_can_view_their_task(): void
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'project_id' => $this->project->id,
        ]);

        $response = $this->withHeaders($this->headers)
            ->getJson('/api/tasks/' . $task->id);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'id',
                'title',
                'description',
                'status',
                'priority',
                'due_date',
                'completed_at',
                'created_at',
                'updated_at',
                'project_id',
                'user_id',
                'project', // Should include related project
                'user',    // Should include related user
            ],
        ]);

        $this->assertEquals($task->id, $response->json('data.id'));
        $this->assertEquals($this->user->id, $response->json('data.user_id'));
        $this->assertEquals($this->project->id, $response->json('data.project_id'));
    }

    /**
     * Test user cannot view other user's task
     */
    public function test_user_cannot_view_other_users_task(): void
    {
        $otherUser = User::factory()->create();
        $otherProject = Project::factory()->create(['user_id' => $otherUser->id]);
        $task = Task::factory()->create([
            'user_id' => $otherUser->id,
            'project_id' => $otherProject->id,
        ]);

        $response = $this->withHeaders($this->headers)
            ->getJson('/api/tasks/' . $task->id);

        $response->assertStatus(403);
    }

    /**
     * Test viewing non-existent task
     */
    public function test_viewing_non_existent_task_returns_404(): void
    {
        $response = $this->withHeaders($this->headers)
            ->getJson('/api/tasks/99999');

        $response->assertStatus(404);
    }

    /**
     * Test updating a task
     */
    public function test_user_can_update_their_task(): void
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'project_id' => $this->project->id,
        ]);

        $updateData = [
            'title' => 'Updated Task Title',
            'description' => 'Updated task description',
            'status' => 'in_progress',
            'priority' => 'high',
        ];

        $response = $this->withHeaders($this->headers)
            ->putJson('/api/tasks/' . $task->id, $updateData);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'title',
                'description',
                'status',
                'priority',
                'due_date',
                'completed_at',
                'created_at',
                'updated_at',
                'project_id',
                'user_id',
            ],
        ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Task Title',
            'description' => 'Updated task description',
            'status' => 'in_progress',
            'priority' => 'high',
            'user_id' => $this->user->id,
        ]);

        $this->assertEquals('Updated Task Title', $response->json('data.title'));
        $this->assertEquals('in_progress', $response->json('data.status'));
    }

    /**
     * Test marking task as completed
     */
    public function test_user_can_mark_task_as_completed(): void
    {
        $task = Task::factory()->todo()->create([
            'user_id' => $this->user->id,
            'project_id' => $this->project->id,
        ]);

        $updateData = [
            'status' => 'completed',
            'completed_at' => now()->toISOString(),
        ];

        $response = $this->withHeaders($this->headers)
            ->putJson('/api/tasks/' . $task->id, $updateData);

        $response->assertStatus(200);
        $this->assertEquals('completed', $response->json('data.status'));
        $this->assertNotNull($response->json('data.completed_at'));

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'completed',
        ]);
    }

    /**
     * Test user cannot update other user's task
     */
    public function test_user_cannot_update_other_users_task(): void
    {
        $otherUser = User::factory()->create();
        $otherProject = Project::factory()->create(['user_id' => $otherUser->id]);
        $task = Task::factory()->create([
            'user_id' => $otherUser->id,
            'project_id' => $otherProject->id,
        ]);

        $updateData = [
            'title' => 'Attempted Update',
            'status' => 'completed',
        ];

        $response = $this->withHeaders($this->headers)
            ->putJson('/api/tasks/' . $task->id, $updateData);

        $response->assertStatus(403);

        // Ensure task was not updated
        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
            'title' => 'Attempted Update',
        ]);
    }

    /**
     * Test updating task with invalid data
     */
    public function test_update_task_validation_fails_with_invalid_data(): void
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'project_id' => $this->project->id,
        ]);

        $invalidData = [
            'title' => '',
            'status' => 'invalid-status',
            'priority' => 'invalid-priority',
            'due_date' => 'invalid-date',
        ];

        $response = $this->withHeaders($this->headers)
            ->putJson('/api/tasks/' . $task->id, $invalidData);

        $this->assertValidationError($response, ['title', 'status', 'priority', 'due_date']);
    }

    /**
     * Test deleting a task
     */
    public function test_user_can_delete_their_task(): void
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'project_id' => $this->project->id,
        ]);

        $response = $this->withHeaders($this->headers)
            ->deleteJson('/api/tasks/' . $task->id);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Task deleted successfully',
        ]);

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    /**
     * Test user cannot delete other user's task
     */
    public function test_user_cannot_delete_other_users_task(): void
    {
        $otherUser = User::factory()->create();
        $otherProject = Project::factory()->create(['user_id' => $otherUser->id]);
        $task = Task::factory()->create([
            'user_id' => $otherUser->id,
            'project_id' => $otherProject->id,
        ]);

        $response = $this->withHeaders($this->headers)
            ->deleteJson('/api/tasks/' . $task->id);

        $response->assertStatus(403);

        // Ensure task still exists
        $this->assertDatabaseHas('tasks', ['id' => $task->id]);
    }

    /**
     * Test deleting non-existent task
     */
    public function test_deleting_non_existent_task_returns_404(): void
    {
        $response = $this->withHeaders($this->headers)
            ->deleteJson('/api/tasks/99999');

        $response->assertStatus(404);
    }

    /**
     * Test task list filtering by status
     */
    public function test_task_list_can_be_filtered_by_status(): void
    {
        Task::factory()->create(['user_id' => $this->user->id, 'project_id' => $this->project->id, 'status' => 'todo']);
        Task::factory()->create(['user_id' => $this->user->id, 'project_id' => $this->project->id, 'status' => 'completed']);
        Task::factory()->create(['user_id' => $this->user->id, 'project_id' => $this->project->id, 'status' => 'todo']);

        $response = $this->withHeaders($this->headers)
            ->getJson('/api/tasks?status=todo');

        $response->assertStatus(200);
        $this->assertEquals(2, $response->json('data.total'));
        
        // All returned tasks should have 'todo' status
        $tasks = $response->json('data.data');
        foreach ($tasks as $task) {
            $this->assertEquals('todo', $task['status']);
        }
    }

    /**
     * Test task list filtering by priority
     */
    public function test_task_list_can_be_filtered_by_priority(): void
    {
        Task::factory()->create(['user_id' => $this->user->id, 'project_id' => $this->project->id, 'priority' => 'high']);
        Task::factory()->create(['user_id' => $this->user->id, 'project_id' => $this->project->id, 'priority' => 'low']);
        Task::factory()->create(['user_id' => $this->user->id, 'project_id' => $this->project->id, 'priority' => 'high']);

        $response = $this->withHeaders($this->headers)
            ->getJson('/api/tasks?priority=high');

        $response->assertStatus(200);
        $this->assertEquals(2, $response->json('data.total'));
        
        // All returned tasks should have 'high' priority
        $tasks = $response->json('data.data');
        foreach ($tasks as $task) {
            $this->assertEquals('high', $task['priority']);
        }
    }

    /**
     * Test task list filtering by project
     */
    public function test_task_list_can_be_filtered_by_project(): void
    {
        $project2 = Project::factory()->create(['user_id' => $this->user->id]);
        
        Task::factory()->count(2)->create(['user_id' => $this->user->id, 'project_id' => $this->project->id]);
        Task::factory()->count(3)->create(['user_id' => $this->user->id, 'project_id' => $project2->id]);

        $response = $this->withHeaders($this->headers)
            ->getJson('/api/tasks?project_id=' . $this->project->id);

        $response->assertStatus(200);
        $this->assertEquals(2, $response->json('data.total'));
        
        // All returned tasks should belong to the specified project
        $tasks = $response->json('data.data');
        foreach ($tasks as $task) {
            $this->assertEquals($this->project->id, $task['project_id']);
        }
    }

    /**
     * Test task list filtering by overdue status
     */
    public function test_task_list_can_be_filtered_by_overdue_status(): void
    {
        // Create overdue tasks
        Task::factory()->count(2)->overdue()->create([
            'user_id' => $this->user->id,
            'project_id' => $this->project->id,
        ]);
        
        // Create future tasks
        Task::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'project_id' => $this->project->id,
            'due_date' => now()->addWeek(),
        ]);

        $response = $this->withHeaders($this->headers)
            ->getJson('/api/tasks?overdue=true');

        $response->assertStatus(200);
        $this->assertEquals(2, $response->json('data.total'));
        
        // All returned tasks should be overdue
        $tasks = $response->json('data.data');
        foreach ($tasks as $task) {
            $this->assertTrue(now()->greaterThan($task['due_date']));
        }
    }

    /**
     * Test task pagination
     */
    public function test_task_list_pagination_works(): void
    {
        // Create 25 tasks for pagination testing
        Task::factory()->count(25)->create([
            'user_id' => $this->user->id,
            'project_id' => $this->project->id,
        ]);

        $response = $this->withHeaders($this->headers)
            ->getJson('/api/tasks?page=1&per_page=10');

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
}
