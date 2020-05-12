<?php

namespace Tests\Feature;

use App\Models\Task;
use Tests\TestCase;
use App\Models\Project;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProjectFeatureTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */

  private $file;

	public function setUp(): void
    {
        parent::setUp();
	}
		
    public function test_tester_can_take_project()
    {
		$task = factory(Task::class)->create();

		// $this->withoutExceptionHandling();
        $this->json('post', '/api/projects/take', $this->default_input($task->id, $this->loggedInUser->id), $this->userHeaders)
          ->assertStatus(201)
          ->assertJson(['success' => true]);
        $this->assertDatabaseHas('projects', ['taskId' => $task->id, 'userId' => $this->loggedInUser->id, 'status' => 'started']);
	}
	
	public function test_admin_can_not_take_project()
    {
		$task = factory(Task::class)->create();

		$this->withoutExceptionHandling();
        $this->json('post', '/api/projects/take', $this->default_input($task->id, $this->admin->id), $this->adminHeaders)
          ->assertStatus(500)
          ->assertJson(['success' => false, 'message' => "Admins cannot apply to projects"]);
        $this->assertDatabaseMissing('projects', ['userId' => $this->loggedInUser->id, 'status' => 'started']);
	}
	
	public function test_client_can_not_take_project()
    {
		$task = factory(Task::class)->create();

		$this->withoutExceptionHandling();
        $this->json('post', '/api/projects/take', $this->default_input($task->id, $this->client->id), $this->clientHeaders)
          ->assertStatus(500)
          ->assertJson(['success' => false, 'message' => "Clients cannot apply to projects"]);
        $this->assertDatabaseMissing('projects', ['userId' => $this->loggedInUser->id, 'status' => 'started']);
	}
	
	public function test_tester_can_return_project()
    {
		$project = factory(Project::class)->create(['userId' => $this->loggedInUser->id]);
      $input = $this->default_return_input($project->id, $this->loggedInUser->id);
        $this->json('post', '/api/projects/return', $input, $this->userHeaders)
          ->assertStatus(200)
          ->assertJson(['success' => true, 'message' => "Successfuly returned project"]);
        Storage::disk('local')->assertExists('submissions/' . $project->id . '/' . $this->file->getClientOriginalName());
        $this->assertDatabaseHas('files', ['original_filename' => $this->file->getClientOriginalName()]);
        $this->assertDatabaseHas('projects', ['userId' => $this->loggedInUser->id, 'status' => 'finished']);
	}

	public function test_admin_can_return_project()
    {
		$project = factory(Project::class)->create(['userId' => $this->loggedInUser->id]);
    $input = $this->default_return_input($project->id, $this->admin->id);
        $this->json('post', '/api/projects/return', $input, $this->adminHeaders)
          ->assertStatus(200)
          ->assertJson(['success' => true, 'message' => "Successfuly returned project"]);
        $this->assertDatabaseHas('projects', ['userId' => $this->loggedInUser->id, 'status' => 'finished']);
	}
	
	public function test_client_can_not_return_project()
    {
		$project = factory(Project::class)->create(['userId' => $this->loggedInUser->id]);
    $input = $this->default_return_input($project->id, $this->client->id);
		$this->withoutExceptionHandling();
        $this->json('post', '/api/projects/return', $input, $this->clientHeaders)
          ->assertStatus(500)
          ->assertJson(['success' => false, 'message' => "Clients cannot return projects"]);
        $this->assertDatabaseMissing('projects', ['userId' => $this->loggedInUser->id, 'status' => 'finished']);
	}
	
	public function test_different_tester_can_not_return_project()
    {
		$project = factory(Project::class)->create(['userId' => $this->loggedInUser->id + 5]);
    $input = $this->default_return_input($project->id, $this->loggedInUser->id);
		$this->withoutExceptionHandling();
        $this->json('post', '/api/projects/return', $input, $this->userHeaders)
          ->assertStatus(500)
          ->assertJson(['success' => false, 'message' => "Auth id and project id are diferent"]);
        $this->assertDatabaseMissing('projects', ['userId' => $this->loggedInUser->id, 'status' => 'finished']);
    }
		
	public function default_input(int $taskId, int $userId) {
        return [
            'taskId' => $taskId,
            'userId' => $userId,
        ];
  }
  
  public function default_return_input(int $projectId, int $userId) {
    Storage::fake('local');
    $this->file = UploadedFile::fake()->image('submission.pdf');
    return [
      'projectId' => $projectId,
      'userId' => $userId,
      'file' => $this->file,
    ];
  }
}
