<?php

namespace Tests\Feature;

use Carbon\Carbon;
use App\Models\Task;
use Tests\TestCase;
use App\Models\Company;
use App\Models\Project;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class TaskRatingFeatureTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */

	public function setUp(): void
    {
        parent::setUp();
	}

	public function test_admin_fails_to_rate_task()
    {
        $this->withoutExceptionHandling();
		
		$input = $this->default_input();

		$task = factory(Task::class)->create(['companyId' => $this->client->companies()->first()->id]);

		factory(Project::class)->create(['taskId' => $task->id, 'status' => Project::FINISHED, 'userId' => $this->loggedInUser->id]);

		$input['taskId'] = $task->id;

		$input['created_by'] = $this->client->id;

		$input['given_to'] = $this->loggedInUser->id;

		$this->fail_assertions($input, $this->adminHeaders);
	}
		
    public function test_creates_rating_with_correct_input()
    {
        $this->withoutExceptionHandling();
		
		$input = $this->default_input();

		$task = factory(Task::class)->create(['companyId' => $this->client->companies()->first()->id]);

		factory(Project::class)->create(['taskId' => $task->id, 'status' => Project::FINISHED, 'userId' => $this->loggedInUser->id]);

		$input['taskId'] = $task->id;

		$input['created_by'] = $this->client->id;

		$input['given_to'] = $this->loggedInUser->id;

		$this->success_assertions($input, $this->clientHeaders);
	}
	
	public function test_fails_when_project_is_not_started() {

		$this->withoutExceptionHandling();
		
		$input = $this->default_input();

		$task = factory(Task::class)->create(['companyId' => $this->client->companies()->first()->id]);

		factory(Project::class)->create(['taskId' => $task->id]);

		$input['taskId'] = $task->id;

		$this->fail_assertions($input, $this->clientHeaders);

	}

	public function test_fails_when_user_does_not_exist_in_task() {

		$this->withoutExceptionHandling();
		
		$input = $this->default_input();

		$task = factory(Task::class)->create(['companyId' => $this->client->companies()->first()->id]);

		factory(Project::class)->create(['taskId' => $task->id]);

		$input['taskId'] = $task->id;

		$this->fail_assertions($input, $this->clientHeaders);

	}
		
	public function default_input() {
        return [
            'rating' => '10',
            'comment' => 'random',
            'created_by' => $this->client->id,
            'given_to' => $this->loggedInUser->id,
            'taskId' => null,
        ];
	}

	public function fail_assertions(array $input, $headers) {
		$this->json('post', '/api/tasks/rate', $input, $headers)
          ->assertStatus(500)
          ->assertJson(['success' => false]);
        $this->assertDatabaseMissing('task_user_ratings', ['created_by' => $input['created_by'], 'given_to' => $input['given_to'], 'taskId' => $input['taskId']]);
	} 

	public function success_assertions(array $input, $headers) {
		$this->json('post', '/api/tasks/rate', $input, $headers)
          ->assertStatus(201)
          ->assertJson(['success' => true]);
        $this->assertDatabaseHas('task_user_ratings', ['created_by' => $input['created_by'], 'given_to' => $input['given_to'], 'taskId' => $input['taskId']]);
	}
}
