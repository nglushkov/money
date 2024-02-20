<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Testing main routes (main page, login page, user authentication, user logout, etc)
 */
class MainRoutesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function testMainPageAuthenticatedUser()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
    }

    public function testMainPageGuest()
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
    }

    public function testLoginPage()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function testLoginAuthenticatedUser()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/login');

        $response->assertRedirect('/');
    }

    public function testUserAuthentication()
    {
        $user = User::factory()->create();
        $response = $this->post('/authenticate', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/');
    }

    public function testUserAuthenticationWrongCredentials()
    {
        $response = $this->post('/authenticate', [
            'email' => 'test@example.com',
            'password' => 'wrong_password',
        ]);

        $response->assertRedirectToRoute('login');
        $response->assertSessionHasErrors();
    }

    public function testUserLogout()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/logout');
        $response->assertRedirect('/login');

        $response = $this->get('/');
        $response->assertRedirect('/login');
    }
}
