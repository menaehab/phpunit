<?php

namespace Tests\Feature\Category;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryDeletingTest extends TestCase
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
    public function delete_category()
    {
        // Arrange
        $category = Category::factory()->create();

        // Act
        $response = $this->delete(route('categories.destroy', ['category' => $category->id]));

        // Assert
        $response->assertStatus(302)
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success', 'Category deleted successfully')
            ->assertRedirect(route('categories.index'));

        $this->assertDatabaseMissing('categories', $category->toArray());
    }
}
