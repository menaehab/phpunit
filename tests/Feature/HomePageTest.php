<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_check_home_page_works_fine(): void
    {
        // url "/"
        $response = $this->get(route('index'));

        // view opened successfully

        // response status 200
        $response->assertStatus(200);

        $response->assertViewIs("welcome");
        $response->assertSeeText("Watch");
    }
}
