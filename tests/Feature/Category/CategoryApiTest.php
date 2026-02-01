<?php

namespace Tests\Feature\Category;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_prevent_unauthorized_user_from_listing_categories()
    {
        // Act
        $response = $this->getJson(route('api.categories.index'));

        // Assert
        $response->assertStatus(401);
    }

    public function test_list_all_categories()
    {
        // Arrange
        $this->authenticateUser();
        Category::factory()->count(5)->create();

        // Act
        $response = $this->getJson(route('api.categories.index'));

        // Assert
        $response->assertStatus(200)
            ->assertJsonCount(5, 'data');
    }

    public function test_create_category()
    {
        // Arrange
        $category = Category::factory()->make();

        // Act
        $this->authenticateUser();
        $response = $this->postJson(route('api.categories.store'), $category->toArray());
        // Assert
        $response->assertStatus(201)
            ->assertJsonFragment([
                'name' => $category->name,
                'description' => $category->description,
            ]);
        $this->assertDatabaseHas('categories', [
            'name' => $category->name,
            'description' => $category->description,
        ]);
    }

    public function test_show_category()
    {
        // Arrange
        $category = Category::factory()->create();

        // Act
        $this->authenticateUser();
        $response = $this->getJson(route('api.categories.show', $category->id));
        // Assert
        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => $category->name,
                'description' => $category->description,
            ]);
        $this->assertDatabaseHas('categories', [
            'name' => $category->name,
            'description' => $category->description,
        ]);
    }

    public function test_update_category()
    {
        // Arrange
        $category = Category::factory()->create();
        $updatedData = [
            'name' => 'Updated Category Name',
            'description' => 'Updated Category Description',
        ];

        // Act
        $this->authenticateUser();
        $response = $this->putJson(route('api.categories.update', $category->id), $updatedData);
        // Assert
        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => $updatedData['name'],
                'description' => $updatedData['description'],
            ]);
        $this->assertDatabaseHas('categories', [
            'name' => $updatedData['name'],
            'description' => $updatedData['description'],
        ]);
    }

    public function test_delete_category()
    {
        // Arrange
        $category = Category::factory()->create();

        // Act
        $this->authenticateUser();
        $response = $this->deleteJson(route('api.categories.destroy', $category->id));
        // Assert
        $response->assertStatus(204);
        $this->assertDatabaseMissing('categories', [
            'name' => $category->name,
            'description' => $category->description,
        ]);
    }

    private function authenticateUser(): User
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        return $user;
    }
}
