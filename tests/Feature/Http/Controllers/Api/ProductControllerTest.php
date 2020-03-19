<?php

namespace Tests\Feature\Http\Controllers\Api;

use Faker\Factory;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductControllerTest extends TestCase
{
    /** @test */
    public function can_create_a_product()
    {
        // Given

        // When
        $faker = Factory::create();

        $repsonse = $this->json('POST', '/api/products', [
            'name' => $name = $faker->company,
            'slug' => str_slug($name),
            'price' => $price = random_int(10, 100)
        ]);

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
}
