<?php

namespace Tests\Feature\Category;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryCreatingTest extends TestCase
{
    use RefreshDatabase;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_check_if_create_category_page_opens_successfully()
    {
        // Act
        $response = $this->get(route('categories.create'));

        // Assert
        $response->assertStatus(200)
            ->assertSee('Categories')
            ->assertSeeText('Name')
            ->assertSeeText('Description');
    }

    public function test_create_category()
    {
        // Arrange
        $category = Category::factory()->make();

        // Act
        $response = $this->post(route('categories.store'), $category->toArray());

        // Assert
        $response->assertStatus(302)
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success', 'Category created successfully')
            ->assertRedirect(route('categories.index'));

        $this->assertDatabaseHas('categories', [
            'name' => $category->name,
            'description' => $category->description,
        ]);
    }

    public function test_category_name_is_required()
    {
        // Arrange
        $category = Category::factory()->make(['name' => '']);

        // Act
        $response = $this->post(route('categories.store'), $category->toArray());

        // Assert
        $response->assertStatus(302)
            ->assertSessionHasErrors(['name' => 'The name field is required.']);

        $this->assertDatabaseMissing('categories', [
            'description' => $category->description,
        ]);
    }

    public function test_category_name_length_must_be_less_than_3_and_not_more_than_255()
    {
        // Arrange
        $categoryShortName = Category::factory()->make(['name' => 'ab']);
        $categoryLongName = Category::factory()->make(['name' => str_repeat('a', 256)]);

        // Act
        $responseShortName = $this->post(route('categories.store'), $categoryShortName->toArray());
        $responseLongName = $this->post(route('categories.store'), $categoryLongName->toArray());

        // Assert
        $responseShortName->assertStatus(302)
            ->assertSessionHasErrors('name');
        $responseLongName->assertStatus(302)
            ->assertSessionHasErrors('name');
    }

    public function test_category_description_is_optional()
    {
        // Arrange
        $category = Category::factory()->make(['description' => '']);

        // Act
        $response = $this->post(route('categories.store'), $category->toArray());

        // Assert
        $response->assertStatus(302)
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('categories', [
            'name' => $category->name,
        ]);
    }

    public function test_category_description_length_must_be_not_more_than_1000()
    {
        // Arrange
        $category = Category::factory()->make(['description' => str_repeat('a', 1001)]);

        // Act
        $response = $this->post(route('categories.store'), $category->toArray());

        // Assert
        $response->assertStatus(302)
            ->assertSessionHasErrors('description');
    }
}
