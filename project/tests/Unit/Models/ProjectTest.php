<?php

namespace Tests\Unit\Models;

use App\Models\Project;
use App\Models\User;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test project creation
     */
    public function test_project_can_be_created(): void
    {
        $user = User::factory()->create();
        
        $projectData = [
            'name' => 'Test Project',
            'description' => 'A test project description',
            'status' => 'active',
            'user_id' => $user->id,
            'start_date' => now()->addDay(),
            'end_date' => now()->addMonth(),
        ];

        $project = Project::create($projectData);

        $this->assertInstanceOf(Project::class, $project);
        $this->assertEquals('Test Project', $project->name);
        $this->assertEquals('A test project description', $project->description);
        $this->assertEquals('active', $project->status);
        $this->assertEquals($user->id, $project->user_id);
        $this->assertDatabaseHas('projects', ['id' => $project->id]);
    }

    /**
     * Test project factory
     */
    public function test_project_factory_creates_valid_project(): void
    {
        $project = Project::factory()->create();

        $this->assertInstanceOf(Project::class, $project);
        $this->assertNotEmpty($project->name);
        $this->assertNotEmpty($project->description);
        $this->assertNotEmpty($project->status);
        $this->assertNotNull($project->user_id);
        $this->assertDatabaseHas('projects', ['id' => $project->id]);
    }

    /**
     * Test project belongs to user relationship
     */
    public function test_project_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $project->user);
        $this->assertEquals($user->id, $project->user->id);
    }

    /**
     * Test project has many tasks relationship
     */
    public function test_project_has_many_tasks(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id]);
        $tasks = Task::factory()->count(3)->create([
            'project_id' => $project->id,
            'user_id' => $user->id,
        ]);

        $this->assertEquals(3, $project->tasks()->count());
        $this->assertTrue($project->tasks()->exists());
        
        foreach ($tasks as $task) {
            $this->assertTrue($project->tasks->contains($task));
        }
    }

    /**
     * Test project fillable attributes
     */
    public function test_project_fillable_attributes(): void
    {
        $project = new Project();
        $expectedFillable = [
            'name',
            'description',
            'status',
            'user_id',
            'start_date',
            'end_date',
        ];

        $this->assertEquals($expectedFillable, $project->getFillable());
    }

    /**
     * Test project date casting
     */
    public function test_project_date_casting(): void
    {
        $project = new Project();
        $casts = $project->getCasts();

        $this->assertEquals('date', $casts['start_date']);
        $this->assertEquals('date', $casts['end_date']);
    }

    /**
     * Test project owned scope
     */
    public function test_project_owned_scope(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        $project1 = Project::factory()->create(['user_id' => $user1->id]);
        $project2 = Project::factory()->create(['user_id' => $user2->id]);

        // Act as user1 for authentication context
        $this->actingAs($user1, 'sanctum');
        
        $ownedProjects = Project::owned()->get();
        
        $this->assertEquals(1, $ownedProjects->count());
        $this->assertEquals($project1->id, $ownedProjects->first()->id);
        $this->assertFalse($ownedProjects->contains($project2));
    }

    /**
     * Test project factory states
     */
    public function test_project_factory_pending_state(): void
    {
        $project = Project::factory()->pending()->create();

        $this->assertEquals('pending', $project->status);
    }    public function test_project_factory_active_state(): void
    {
        $project = Project::factory()->active()->create();
        
        $this->assertEquals('active', $project->status);
    }

    public function test_project_factory_completed_state(): void
    {
        $project = Project::factory()->completed()->create();
        
        $this->assertEquals('completed', $project->status);
        $this->assertNotNull($project->end_date);
        $this->assertTrue($project->end_date->isPast());
    }

    public function test_project_factory_cancelled_state(): void
    {
        $project = Project::factory()->cancelled()->create();
        
        $this->assertEquals('cancelled', $project->status);
    }

    /**
     * Test project deletion cascades to tasks
     */
    public function test_project_deletion_cascades_to_tasks(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id]);
        $tasks = Task::factory()->count(2)->create([
            'project_id' => $project->id,
            'user_id' => $user->id,
        ]);

        $project->delete();

        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
        
        foreach ($tasks as $task) {
            $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
        }
    }

    /**
     * Test project validation (if implemented)
     */
    public function test_project_requires_name(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Project::create([
            'description' => 'Test description',
            'status' => 'active',
            'user_id' => User::factory()->create()->id,
        ]);
    }

    /**
     * Test project status values
     */
    public function test_project_accepts_valid_status_values(): void
    {
        $user = User::factory()->create();
        $validStatuses = ['pending', 'active', 'completed', 'cancelled'];

        foreach ($validStatuses as $status) {
            $project = Project::factory()->create([
                'user_id' => $user->id,
                'status' => $status,
            ]);

            $this->assertEquals($status, $project->status);
        }
    }
}
