<?php

namespace Tests\Feature;

use Tests\TestCase;

class ReportTest extends TestCase
{
    public const ENDPOINT = '/api/reports';

    public function testSuccessGetReport(): void
    {
        $response = $this->actingAs($this->user)
            ->get(self::ENDPOINT);

        $response->assertStatus(200);
        $response->assertJsonCount(10, 'data');
        $response->assertJsonStructure([
            'income',
            'outcome',
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
}
