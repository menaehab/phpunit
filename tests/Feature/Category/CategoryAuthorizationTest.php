<?php

namespace Tests\Feature\Category;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CategoryAuthorizationTest extends TestCase
{
    #[Test]
    public function guest_cannot_access_categories_page()
    {
        // act
        $response = $this->get(route('categories.index'));

        // assert
        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function guest_cannot_access_create_category_page()
    {
        // act
        $response = $this->get(route('categories.create'));
        // assert
        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function guest_cannot_store_category()
    {
        // act
        $response = $this->post(route('categories.store'), []);

        // assert
        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function guest_cannot_access_edit_category_page()
    {
        // act
        $response = $this->get(route('categories.edit', ['category' => 1]));

        // assert
        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function guest_cannot_update_category()
    {
        // act
        $response = $this->put(route('categories.update', ['category' => 1]), []);

        // assert
        $response->assertRedirect(route('login'));

    }

    #[Test]
    public function guest_cannot_delete_category()
    {
        // act
        $response = $this->delete(route('categories.destroy', ['category' => 1]));

        // assert
        $response->assertRedirect(route('login'));
    }
}
