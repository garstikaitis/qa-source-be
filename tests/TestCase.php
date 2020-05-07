<?php

namespace Tests;

use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, DatabaseTransactions;

    public function setUp() :void
    {
        parent::setUp();

        $this->setDefaultUsers();

    }

    public function setDefaultUsers() {

        $this->rawUser = factory(User::class)->create(['name' => 'User 1']);

        $this->loggedInUser = $this->loginAsTester();

        $this->admin = $this->loginAsAdmin();

        $this->client = $this->loginAsClient();

        $this->userHeaders = ['Authorization' => "Bearer " . $this->loggedInUser->api_token];

        $this->adminHeaders = ['Authorization' => 'Bearer ' . $this->admin->api_token];

        $this->clientHeaders = ['Authorization' => "Bearer " . $this->client->api_token];

    }

    public function loginAsAdmin() {

        $admin = factory(User::class)->create(['is_admin' => 1]);

        $admin->generateToken();

        return $admin;
    }

    public function loginAsClient() {

        $client = factory(User::class)->create();

        $client->generateToken();

        $company = factory(Company::class)->create();

        $company->users()->sync([$client->id], false);

        return $client;

    }

    public function loginAsTester() {

        $user = factory(User::class)->create(['name' => 'User 2']);

        $user->generateToken();

        return $user;

    }

    public function logout($user) {

        $user->api_token = null;

        $user->save();

    }

}
