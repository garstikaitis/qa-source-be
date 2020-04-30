<?php

namespace Tests\Feature;

use Carbon\Carbon;
use App\Model\Task;
use Tests\TestCase;
use App\Models\Company;

class TaskFeatureTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    private $company;
		public function setUp(): void
    {
        parent::setUp();
		}
		
    public function test_creates_task_with_correct_input_passes()
    {
        $this->withoutExceptionHandling();
        $this->json('post', '/api/tasks/create', $this->default_input(100), $this->userHeaders)
          ->assertStatus(201)
          ->assertJson(['success' => true]);
        $this->assertDatabaseHas('tasks', ['name' => 'Task 1']);
        $this->assertDatabaseHas('companies', ['id' => $this->company->id, 'credits_remaining' => 92]);
    }

    public function test_create_task_fails_when_not_enough_credits()
    {
        $this->withoutExceptionHandling();
        $this->json('post', '/api/tasks/create', $this->default_input(17), $this->userHeaders)
          ->assertStatus(500)
          ->assertJson(['success' => false, 'message' => "Insufficient credits"]);
        $this->assertDatabaseMissing('tasks', ['name' => 'Task 1']);
    }
    
    public function test_creates_task_with_incorrect_input_fails()
    {
        $this->json('post', '/api/tasks/create', [], $this->userHeaders)
          ->assertStatus(500)
          ->assertJson(['success' => false]);
      $this->assertDatabaseMissing('tasks', ['name' => 'Task 1']);
    }
    
    public function test_get_all_tasks() {
      $this->withoutExceptionHandling();
      for ($i = 0; $i < 5; $i++) {
        factory(Task::class)->create();
      }

      $response = $this->json('get', '/api/tasks', [], $this->userHeaders)
        ->assertStatus(200);
      $count = count($response->original['data']);
      // dd($count);
      $this->assertTrue($count === 5);
    }
		
		public function default_input(int $credits_remaining) {
        $this->company = factory(Company::class)->create(['credits_remaining' => $credits_remaining]);
        return [
            'name' => 'Task 1',
            'description' => 'random',
            'companyId' => $this->company->id,
            'deadline' => Carbon::now()->addDays(1)->toDateTimeString(),
        ];
		}
}
