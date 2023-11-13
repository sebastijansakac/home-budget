<?php

namespace Tests\Feature;

use App\Enum\ExpenseType;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    public const ENDPOINT = '/api/categories';

    public function testSuccessGetCategoriesCollection(): void
    {
        $response = $this->actingAs($this->user)
            ->get(self::ENDPOINT);

        $response->assertStatus(200);
        $response->assertJsonCount(10, 'data');
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'type',
                ],
            ],
        ]);
    }

    public function testSuccessCreateCategory(): void
    {
        $response = $this->actingAs($this->user)
            ->post(self::ENDPOINT, [
                'title' => 'Salary',
                'type' => ExpenseType::Income->name,
            ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'type',
            ],
        ]);
    }
}
