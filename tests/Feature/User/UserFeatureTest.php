<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;

class UserFeatureTest extends TestCase
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
		
    public function test_registers_user_with_correct_input()
    {
        $this->json('post', '/api/register', $this->defaultInput())
          ->assertStatus(201)
		  ->assertJson(['success' => true])
		  ->assertJsonStructure(['data' => ['api_token']]);
        $this->assertDatabaseHas('users', ['name' => 'User 1']);
	}

	public function test_register_fails_user_with_wrong_input()
    {
        $this->json('post', '/api/register', ['name' => 'User fail'])
          ->assertStatus(403);
        $this->assertDatabaseMissing('users', ['name' => 'User fail']);
	}
	
	public function test_logins_with_correct_input() {

		factory(User::class)->create([
            'email' => 'testlogin@user.com',
            'password' => bcrypt('toptal123'),
        ]);

        $payload = ['email' => 'testlogin@user.com', 'password' => 'toptal123'];

		$this->json('post', '/api/login', $payload)
          ->assertStatus(200)
		  ->assertJson(['success' => true])
		  ->assertJsonStructure(['data' => ['api_token']]);
	}

	public function test_login_fails_with_no_data()
    {
        $this->json('POST', 'api/login')
            ->assertStatus(403);
	}
	
	public function test_user_can_logout()
    {
		
		$this->loginAsTester();

        $this->json('get', '/api/tasks', [], $this->userHeaders)->assertStatus(200);
        $this->json('post', '/api/logout', [], $this->userHeaders)->assertStatus(200);

        $user = User::find($this->loggedInUser->id);

        $this->assertEquals(null, $user->api_token);
	}
	
	public function test_logged_out_user_can_not_get_tasks()
    {
        $this->loginAsTester();

		$this->logout($this->loggedInUser);

        $this->json('get', '/api/tasks', [], $this->userHeaders)->assertStatus(401);
    }
    
		
	public function defaultInput() {
		return [
			'name' => 'User 1',
			'email' => 'test@test.com',
			'password' => 'password',
		];
	}
}
