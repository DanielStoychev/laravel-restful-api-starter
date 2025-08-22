<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user creation
     */
    public function test_user_can_be_created(): void
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
        ];

        $user = User::create($userData);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    /**
     * Test user factory
     */
    public function test_user_factory_creates_valid_user(): void
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);
        $this->assertNotEmpty($user->name);
        $this->assertNotEmpty($user->email);
        $this->assertNotEmpty($user->password);
        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    /**
     * Test user has projects relationship
     */
    public function test_user_has_projects_relationship(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->projects()->exists());
        $this->assertEquals(1, $user->projects()->count());
        $this->assertEquals($project->id, $user->projects->first()->id);
    }

    /**
     * Test user has tasks relationship
     */
    public function test_user_has_tasks_relationship(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id]);
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'project_id' => $project->id
        ]);

        $this->assertTrue($user->tasks()->exists());
        $this->assertEquals(1, $user->tasks()->count());
        $this->assertEquals($task->id, $user->tasks->first()->id);
    }

    /**
     * Test user can create multiple projects
     */
    public function test_user_can_have_multiple_projects(): void
    {
        $user = User::factory()->create();
        Project::factory()->count(3)->create(['user_id' => $user->id]);

        $this->assertEquals(3, $user->projects()->count());
    }

    /**
     * Test user can create multiple tasks
     */
    public function test_user_can_have_multiple_tasks(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id]);
        
        Task::factory()->count(5)->create([
            'user_id' => $user->id,
            'project_id' => $project->id
        ]);

        $this->assertEquals(5, $user->tasks()->count());
    }

    /**
     * Test user fillable attributes
     */
    public function test_user_fillable_attributes(): void
    {
        $user = new User();
        $expectedFillable = ['name', 'email', 'password', 'role'];

        $this->assertEquals($expectedFillable, $user->getFillable());
    }

    /**
     * Test user hidden attributes
     */
    public function test_user_hidden_attributes(): void
    {
        $user = new User();
        $expectedHidden = ['password', 'remember_token'];

        $this->assertEquals($expectedHidden, $user->getHidden());
    }

    /**
     * Test user casts attributes
     */
    public function test_user_casts_attributes(): void
    {
        $user = new User();
        $expectedCasts = [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];

        // Laravel adds id to casts automatically, so we need to check our specific casts
        $casts = $user->getCasts();
        $this->assertEquals('datetime', $casts['email_verified_at']);
        $this->assertEquals('hashed', $casts['password']);
    }

    /**
     * Test user email must be unique
     */
    public function test_user_email_must_be_unique(): void
    {
        User::factory()->create(['email' => 'test@example.com']);

        $this->expectException(\Illuminate\Database\QueryException::class);
        
        User::factory()->create(['email' => 'test@example.com']);
    }

    /**
     * Test user can be soft deleted (if implemented)
     */
    public function test_user_projects_are_deleted_when_user_is_deleted(): void
    {
        $user = User::factory()->create();
        $projects = Project::factory()->count(2)->create(['user_id' => $user->id]);

        $user->delete();

        foreach ($projects as $project) {
            $this->assertDatabaseMissing('projects', ['id' => $project->id]);
        }
    }

    /**
     * Test user factory admin state
     */
    public function test_user_factory_admin_state(): void
    {
        $admin = User::factory()->admin()->create();

        $this->assertEquals('admin', $admin->role);
    }

    /**
     * Test user factory manager state
     */
    public function test_user_factory_manager_state(): void
    {
        $manager = User::factory()->manager()->create();

        $this->assertEquals('manager', $manager->role);
    }
}
