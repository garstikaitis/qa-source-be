<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Company;

class CompanyFeatureTest extends TestCase
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

	public function test_admin_can_create_company() {

		$this->json('post', '/api/companies/create', $this->default_input(), $this->adminHeaders)
			->assertStatus(201)
			->assertJson(['success' => true]);
		
		$this->assertDatabaseHas('companies', ['name' => 'Company 42']);
	}

	public function test_admin_can_update_company() {

		$company = factory(Company::class)->create();
		$this->json('post', '/api/companies/update', ['companyId' => $company->id, 'credits_remaining' => 1000], $this->adminHeaders)
			->assertStatus(200)
			->assertJson(['success' => true]);
		
		$this->assertDatabaseHas('companies', ['id' => $company->id, 'credits_remaining' => 1000]);
	}

	public function test_non_admin_can_not_update_company() {

		$company = factory(Company::class)->create();
		$this->json('post', '/api/companies/update', ['companyId' => $company->id, 'credits_remaining' => 1000], $this->userHeaders)
			->assertStatus(500)
			->assertJson(['success' => false, 'message' => 'Access denied']);
		
		$this->assertDatabaseMissing('companies', ['id' => $company->id, 'credits_remaining' => 1000]);
	}

	public function test_non_admin_can_not_create_company() {

		$this->json('post', '/api/companies/create', $this->default_input(), $this->userHeaders)
			->assertStatus(500)
			->assertJson(['success' => false, 'message' => 'Access denied']);
		
		$this->assertDatabaseMissing('companies', ['name' => 'Company 42']);
	}

	public function test_non_admin_can_not_get_companies() {

		$this->json('get', '/api/companies', [], $this->userHeaders)
			->assertStatus(500)
			->assertJson(['success' => false, 'message' => 'Access denied']);
		
		$this->assertDatabaseMissing('companies', ['name' => 'Company 42']);
	}

	public function test_admin_can_get_all_companies() {
		for ($i = 0; $i < 5; $i++) {
		  factory(Company::class)->create();
		}
  
		$response = $this->json('get', '/api/companies', [], $this->adminHeaders)
		  ->assertStatus(200);
		$count = count($response->original['data']);
		$this->assertTrue($count === 6);
	}

	public function test_admin_can_add_user_to_company() {
		$company = factory(Company::class)->create();
		$this->json('post', '/api/companies/add-user', ['companyId' => $company->id, 'userId' => $this->rawUser->id], $this->adminHeaders)
			->assertStatus(201)
			->assertJson(['success' => true, 'message' => 'Successfuly added user to company']);

		$this->assertDatabaseHas('company_user', ['companyId' => $company->id, 'userId' => $this->rawUser->id]);
	}

	public function test_user_can_not_add_user_to_company() {
		$company = factory(Company::class)->create();
		$this->json('post', '/api/companies/add-user', ['companyId' => $company->id, 'userId' => $this->rawUser->id], $this->userHeaders)
			->assertStatus(500)
			->assertJson(['success' => false, 'message' => 'Access denied']);
		
		$this->assertDatabaseMissing('company_user', ['companyId' => $company->id, 'userId' => $this->rawUser->id]);
	}

	public function test_add_user_fails_with_wrong_input() {
		$this->json('post', '/api/companies/add-user', [], $this->adminHeaders)
			->assertStatus(500)
			->assertJson(['success' => false]);
		
		$this->assertDatabaseMissing('company_user', ['companyId' => 1, 'userId' => $this->rawUser->id]);
	}
		
	public function default_input() {
		return [
			'name' => 'Company 42',
			'slug' => 'company-42',
			'credits_remaining' => 20,
		];
	}
}
