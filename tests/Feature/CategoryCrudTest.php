<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $actingUser;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::firstOrCreate(['name' => 'super-admin', 'display_name' => "Super Admin"]);
        
        $this->actingUser  = User::factory()->create([
            'role_id' => $adminRole->id,
        ]);
    }

    // ==========================================
    // READ
    // ==========================================
    public function test_list_all_categories()
    {
        Category::factory()->count(3)->create();

        $response = $this->actingAs($this->actingUser, 'sanctum')->getJson('/api/categories');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'description', 'created_at', 'updated_at']
                ]
            ]);
    }

    public function test_show_a_single_category()
    {
        $category = Category::factory()->create([
            'name' => 'Furniture',
            'description' => 'Home and office furniture'
        ]);

        $response = $this->actingAs($this->actingUser, 'sanctum')->getJson("/api/categories/{$category->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Category detail found',
                'data' => [
                    'id' => $category->id,
                    'name' => 'Furniture',
                    'description' => 'Home and office furniture'
                ]
            ]);
    }

    public function test_returns_404_when_category_not_found()
    {
        $response = $this->actingAs($this->actingUser, 'sanctum')->getJson('/api/categories/999');

        $response->assertStatus(404);
    }

    // ==========================================
    // CREATE
    // ==========================================

    public function test_create_a_category()
    {
        $categoryData = [
            'name' => 'Electronics',
            'description' => 'Electronic items and gadgets'
        ];

        $response = $this->actingAs($this->actingUser, 'sanctum')->postJson('/api/categories', $categoryData);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Category created successfully',
                'data' => [
                    'name' => 'Electronics',
                    'description' => 'Electronic items and gadgets'
                ]
            ]);

        $this->assertDatabaseHas('categories', $categoryData);
    }

    public function test_requires_name_when_creating_category()
    {
        $response = $this->actingAs($this->actingUser, 'sanctum')->postJson('/api/categories', [
            'description' => 'Some description'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_create_category_without_description()
    {
        $response = $this->actingAs($this->actingUser, 'sanctum')->postJson('/api/categories', [
            'name' => 'Books'
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('categories', [
            'name' => 'Books',
            'description' => null
        ]);
    }

    // ==========================================
    // UPDATE
    // ==========================================

    public function test_update_a_category()
    {
        $category = Category::factory()->create([
            'name' => 'Old Name',
            'description' => 'Old Description'
        ]);

        $updateData = [
            'name' => 'New Name',
            'description' => 'New Description'
        ];

        $response = $this->actingAs($this->actingUser, 'sanctum')->putJson("/api/categories/{$category->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Category updated successfully',
                'data' => [
                    'name' => 'New Name',
                    'description' => 'New Description'
                ]
            ]);

        $this->assertDatabaseHas('categories', $updateData);
        $this->assertDatabaseMissing('categories', [
            'name' => 'Old Name'
        ]);
    }

    public function test_update_only_name()
    {
        $category = Category::factory()->create([
            'name' => 'Original',
            'description' => 'Original Description'
        ]);

        $response = $this->actingAs($this->actingUser, 'sanctum')->putJson("/api/categories/{$category->id}", [
            'name' => 'Updated Name'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated Name',
            'description' => 'Original Description'
        ]);
    }

    public function test_requires_name_when_updating_category()
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->actingUser, 'sanctum')-> putJson("/api/categories/{$category->id}", [
            'description' => 'Only description'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    // ==========================================
    // DELETE
    // ==========================================

    public function test_delete_a_category()
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->actingUser, 'sanctum')->deleteJson("/api/categories/{$category->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Category deleted successfully',
                'data' => null
            ]);

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id
        ]);
    }

    public function test_delete_returns_404_when_deleting_non_existent_category()
    {
        $response = $this->actingAs($this->actingUser, 'sanctum')->deleteJson('/api/categories/999');

        $response->assertStatus(404);
    }
}
