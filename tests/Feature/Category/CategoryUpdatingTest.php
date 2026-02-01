<?php

namespace Tests\Feature\Category;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryUpdatingTest extends TestCase
{
    use RefreshDatabase;
    protected User $user;
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    #[Test]
    public function check_if_category_edit_page_contains_expected_content()
    {
        // Arrange
        $category = Category::factory()->create();

        // Act
        $response = $this->get(route('categories.edit', ['category' => $category->id]));

        // Assert
        $response->assertStatus(200)
            ->assertSee($category->name)
            ->assertSee($category->description)
            ->assertViewHas('category', $category);
    }

    #[Test]
    public function update_category()
    {
        // Arrange
        $category = Category::factory()->create();
        $updatedData = [
            'name' => 'Updated Category Name',
            'description' => 'Updated Category Description',
        ];

        // Act
        $response = $this->put(route('categories.update', ['category' => $category->id]), $updatedData);

        // Assert
        $response->assertStatus(302)
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success', 'Category updated successfully')
            ->assertRedirect(route('categories.index'));
        $this->assertDatabaseMissing('categories', $category->toArray());
        $this->assertDatabaseHas('categories', $updatedData);
    }

    #[Test]
    public function category_name_is_required()
    {
        $category = Category::factory()->create();
        $updatedData = [
            'name' => '',
            'description' => 'Updated Category Description',
        ];

        // Act
        $response = $this->put(route('categories.update', ['category' => $category->id]), $updatedData);

        // Assert
        $response->assertStatus(302)
            ->assertSessionHasErrors(['name' => 'The name field is required.']);
    }

    #[Test]
    public function category_description_can_be_null()
    {
        $category = Category::factory()->create();
        $updatedData = [
            'name' => 'Updated Category Name',
            'description' => null,
        ];

        // Act
        $response = $this->put(route('categories.update', ['category' => $category->id]), $updatedData);

        // Assert
        $response->assertStatus(302)
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success', 'Category updated successfully')
            ->assertRedirect(route('categories.index'));
        $this->assertDatabaseHas('categories', $updatedData);
    }
}
