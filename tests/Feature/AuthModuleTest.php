<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class testAuthModule extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public $token;

    public function testFailedLogin1()
    {
        $data = array(
            "email" => "pass@mail.com",
            "password"=> "pass"
        );
        $response = $this->post('/api/auth/login', $data);

        $response->assertStatus(422);
    }
   
}
