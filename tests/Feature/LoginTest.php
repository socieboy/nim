<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    public function guest_should_be_redirect_to_the_login_page()
    {
        $response = $this->get('/');
        $response->assertRedirect('/login');
    }

    public function auth_users_can_access_the_root_page()
    {
        $this->signIn();
        $response = $this->get('/login');
        $response->assertRedirect('/');
    }
}
