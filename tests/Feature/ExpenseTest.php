<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExpenseTest extends TestCase
{
    public const ENDPOINT = '/api/expenses';

    public function testSuccessGetExpensesCollection(): void
    {
        $response = $this->actingAs($this->user)
            ->get(self::ENDPOINT);

        $response->assertStatus(200);
        $response->assertJsonCount(10, 'data');
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'description',
                    'amount',
                    'category' => [
                        'id',
                        'title',
                        'type',
                    ],
                ],
            ],
        ]);
    }

    public function testSuccessGetOneExpense(): void
    {
        $response = $this->actingAs($this->user)
            ->get(sprintf('%s/%s', self::ENDPOINT, $this->expenses->last()->id));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'description',
                'amount',
                'category' => [
                    'id',
                    'title',
                    'type',
                ],
            ],
        ]);
    }

    public function testSuccessCreateExpense(): void
    {
        $response = $this->actingAs($this->user)
            ->post(self::ENDPOINT, [
                'description' => 'Bill for food',
                'amount' => 123.45,
                'category_id' => 1
            ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'description',
                'amount',
            ],
        ]);
    }
}
