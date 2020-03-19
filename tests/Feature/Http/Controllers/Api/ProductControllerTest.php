<?php

namespace Tests\Feature\Http\Controllers\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    /** @test */
    public function can_create_a_product()
    {
        // Given

        // When
        $repsonse = $this->json('POST', '/api/products', [

        ]);

        // Then
        $repsonse->assertStatus(201);

    }
}
