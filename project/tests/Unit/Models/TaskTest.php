<?php

namespace Tests\Unit\Models;

use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test task creation
     */
    public function test_task_can_be_created(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id]);
        
        $taskData = [
            'title' => 'Test Task',
            'description' => 'A test task description',
            'status' => 'todo',
            'priority' => 'medium',
            'due_date' => now()->addWeek(),
            'project_id' => $project->id,
            'user_id' => $user->id,
        ];

        $task = Task::create($taskData);

        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals('Test Task', $task->title);
        $this->assertEquals('A test task description', $task->description);
        $this->assertEquals('todo', $task->status);
        $this->assertEquals('medium', $task->priority);
        $this->assertEquals($project->id, $task->project_id);
        $this->assertEquals($user->id, $task->user_id);
        $this->assertDatabaseHas('tasks', ['id' => $task->id]);
    }

    /**
     * Test task factory
     */
    public function test_task_factory_creates_valid_task(): void
    {
        $task = Task::factory()->create();

        $this->assertInstanceOf(Task::class, $task);
        $this->assertNotEmpty($task->title);
        $this->assertNotEmpty($task->description);
        $this->assertNotEmpty($task->status);
        $this->assertNotEmpty($task->priority);
        $this->assertNotNull($task->project_id);
        $this->assertNotNull($task->user_id);
        $this->assertDatabaseHas('tasks', ['id' => $task->id]);
    }

    /**
     * Test task belongs to project relationship
     */
    public function test_task_belongs_to_project(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id]);
        $task = Task::factory()->create([
            'project_id' => $project->id,
            'user_id' => $user->id,
        ]);

        $this->assertInstanceOf(Project::class, $task->project);
        $this->assertEquals($project->id, $task->project->id);
    }

    /**
     * Test task belongs to user relationship
     */
    public function test_task_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id]);
        $task = Task::factory()->create([
            'project_id' => $project->id,
            'user_id' => $user->id,
        ]);

        $this->assertInstanceOf(User::class, $task->user);
        $this->assertEquals($user->id, $task->user->id);
    }

    /**
     * Test task fillable attributes
     */
    public function test_task_fillable_attributes(): void
    {
        $task = new Task();
        $expectedFillable = [
            'title',
            'description',
            'status',
            'priority',
            'due_date',
            'completed_at',
            'project_id',
            'user_id',
        ];

        $fillable = $task->getFillable();
        
        // Check that all expected attributes are fillable
        foreach ($expectedFillable as $attribute) {
            $this->assertContains($attribute, $fillable);
        }
    }

    /**
     * Test task factory states
     */
    public function test_task_factory_todo_state(): void
    {
        $task = Task::factory()->todo()->create();

        $this->assertEquals('todo', $task->status);
    }    public function test_task_factory_in_progress_state(): void
    {
        $task = Task::factory()->inProgress()->create();
        
        $this->assertEquals('in_progress', $task->status);
    }

    public function test_task_factory_completed_state(): void
    {
        $task = Task::factory()->completed()->create();
        
        $this->assertEquals('completed', $task->status);
        $this->assertNotNull($task->completed_at);
    }

    public function test_task_factory_high_priority_state(): void
    {
        $task = Task::factory()->highPriority()->create();
        
        $this->assertEquals('high', $task->priority);
    }

    public function test_task_factory_low_priority_state(): void
    {
        $task = Task::factory()->lowPriority()->create();
        
        $this->assertEquals('low', $task->priority);
    }

    public function test_task_factory_overdue_state(): void
    {
        $task = Task::factory()->overdue()->create();
        
        $this->assertTrue($task->due_date->isPast());
        $this->assertContains($task->status, ['todo', 'in_progress']);
    }

    /**
     * Test task status validation
     */
    public function test_task_accepts_valid_status_values(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id]);
        $validStatuses = ['todo', 'in_progress', 'completed', 'cancelled'];

        foreach ($validStatuses as $status) {
            $task = Task::factory()->create([
                'project_id' => $project->id,
                'user_id' => $user->id,
                'status' => $status,
            ]);

            $this->assertEquals($status, $task->status);
        }
    }

    /**
     * Test task priority validation
     */
    public function test_task_accepts_valid_priority_values(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id]);
        $validPriorities = ['low', 'medium', 'high'];

        foreach ($validPriorities as $priority) {
            $task = Task::factory()->create([
                'project_id' => $project->id,
                'user_id' => $user->id,
                'priority' => $priority,
            ]);

            $this->assertEquals($priority, $task->priority);
        }
    }

    /**
     * Test task requires title
     */
    public function test_task_requires_title(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        $user = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id]);
        
        Task::create([
            'description' => 'Test description',
            'status' => 'todo',
            'priority' => 'medium',
            'project_id' => $project->id,
            'user_id' => $user->id,
        ]);
    }

    /**
     * Test task completion functionality
     */
    public function test_task_can_be_marked_completed(): void
    {
        $task = Task::factory()->todo()->create();
        
        $task->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        $this->assertEquals('completed', $task->status);
        $this->assertNotNull($task->completed_at);
        $this->assertTrue($task->completed_at->isToday());
    }

    /**
     * Test task overdue check
     */
    public function test_task_overdue_detection(): void
    {
        // Create an overdue task
        $overdueTask = Task::factory()->create([
            'due_date' => now()->subDay(),
            'status' => 'todo',
        ]);

        // Create a future task
        $futureTask = Task::factory()->create([
            'due_date' => now()->addDay(),
            'status' => 'todo',
        ]);

        $this->assertTrue($overdueTask->due_date->isPast());
        $this->assertFalse($futureTask->due_date->isPast());
    }
}
