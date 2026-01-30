<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryRetrievalTest extends TestCase
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
    public function categories_page_opens_successfully(): void
    {
        // Act
        $response = $this->get(route('categories.index'));

        // Assert
        $response->assertOk();
        $response->assertSee('Categories');
    }

    #[Test]
    public function categories_page_displays_categories(): void
    {
        // Arrange
        $categories = Category::factory()->count(4)->create();

        // Act
        $response = $this->get(route('categories.index'));

        // Assert
        $response->assertViewHas('categories');

        $categories->each(
            fn($category) =>
            $response->assertSee($category->name)
        );
    }

    #[Test]
    public function categories_pagination_works_correctly(): void
    {
        // Arrange
        Category::factory()->count(15)->create();

        // Act & Assert (page 1)
        $this->get(route('categories.index'))
            ->assertViewHas(
                'categories',
                fn($categories) =>
                $categories->count() === 10
            );

        // Act & Assert (page 2)
        $this->get(route('categories.index', ['page' => 2]))
            ->assertViewHas(
                'categories',
                fn($categories) =>
                $categories->count() === 5
            );
    }
}
