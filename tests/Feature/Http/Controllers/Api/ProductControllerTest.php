<?php

namespace Tests\Feature\Http\Controllers\Api;

use Faker\Factory;
use Tests\TestCase;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase; // restart db after each test

    /** @test */
    public function can_create_a_product()
    {
        // $this->withoutExceptionHandling();

        // Given

        // When
        $faker = Factory::create();

        $repsonse = $this->actingAs($this->create('User', [], false), 'api')->json('POST', '/api/products', [
            'name' => $name = $faker->company,
            'slug' => str_slug($name),
            'price' => $price = random_int(10, 100)
        ]);
        Log::info(1, [$repsonse->getContent()]);

        // Then
        $repsonse->assertStatus(201)
            ->assertJsonStructure(['id', 'name', 'slug', 'price', 'created_at'])
            ->assertJson([
                'name' => $name,
                'slug' => str_slug($name),
                'price' => $price
            ]);
        $this->assertDatabaseHas('products', [
            'name' => $name,
            'slug' => str_slug($name),
            'price' => $price
        ]);
    }

    /** @test */
    public function can_return_a_product()
    {
        // Given
        $product = $this->create('Product');

        // When
        $repsonse = $this->actingAs($this->create('User', [], false), 'api')->json('GET', "api/products/$product->id");

        // Then
        $repsonse->assertStatus(200)
            ->assertExactJson([
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => $product->price,
                'created_at' => (string) $product->created_at,
            ]);
    }

    /** @test */
    public function will_fail_with_a_404_if_product_is_not_found()
    {
        $repsonse = $this->actingAs($this->create('User', [], false), 'api')->json('GET', "api/products/-1");

        // Then
        $repsonse->assertStatus(404);
    }

    /** @test */
    public function will_fail_with_a_404_if_product_we_want_to_update_is_an_avaiable()
    {
        $repsonse = $this->actingAs($this->create('User', [], false), 'api')->json('PUT', "api/products/-1");

        // Then
        $repsonse->assertStatus(404);
    }

    /** @test */
    public function can_update_product()
    {
        // Given
        $product = $this->create('Product');

        // When
        $repsonse = $this->actingAs($this->create('User', [], false), 'api')->json('PUT', "api/products/$product->id", [
            'name' => $product->name . '_updated',
            'slug' => str_slug($product->name . '_updated'),
            'price' => $product->price + 10
        ]);

        // Then
        $repsonse->assertStatus(200)
            ->assertExactJson([
                'id' => $product->id,
                'name' => $product->name . '_updated',
                'slug' => str_slug($product->name . '_updated'),
                'price' => $product->price + 10,
                'created_at' => (string) $product->created_at
            ]);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => $product->name . '_updated',
            'slug' => str_slug($product->name . '_updated'),
            'price' => $product->price + 10,
            'created_at' => (string) $product->created_at
        ]);
    }

    /** @test */
    public function will_fail_with_a_404_if_product_we_want_to_delete_is_an_avaiable()
    {
        $repsonse = $this->actingAs($this->create('User', [], false), 'api')->json('DELETE', "api/products/-1");

        // Then
        $repsonse->assertStatus(404);
    }

    /** @test */
    public function can_delete_a_product()
    {
        $product = $this->create('Product');

        $repsonse = $this->actingAs($this->create('User', [], false), 'api')->json('DELETE', "api/products/$product->id");

        $repsonse->assertStatus(204)
            ->assertSee(null);

        $this->assertDatabaseMissing('products', [
            'id' => $product->id
        ]);
    }

    /** @test */
    public function can_return_a_collection_of_paginated_products()
    {
        $product1 = $this->create('Product');
        $product2 = $this->create('Product');
        $product3 = $this->create('Product');

        $repsonse = $this->actingAs($this->create('User', [], false), 'api')->json('GET', "api/products");

        Log::info($repsonse->content());

        $repsonse->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'slug', 'price', 'created_at']
                ]
            ]);
    }

    /** @test */
    public function non_authenticated_users_cannot_access_the_follwoing_end_points_for_the_products_api()
    {
        $index = $this->json('GET', '/api/products');
        $index->assertStatus(401);
        
        $store = $this->json('POST', '/api/products');
        $store->assertStatus(401);

        $shpw = $this->json('GET', '/api/products/-1');
        $shpw->assertStatus(401);

        $update = $this->json('PUT', '/api/products/-1');
        $update->assertStatus(401);

        $destroy = $this->json('DELETE', '/api/products/-1');
        $destroy->assertStatus(401);

    }
}
