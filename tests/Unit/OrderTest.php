<?php

namespace Tests\Unit;

use App\Mail\HalfStockOfIngredient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function testCheckStockForCreateOrder()
    {
        $this->seed();

        $data = [
            'products' => [
                [
                    'product_id' => 1,
                    'quantity' => 101
                ],
                [
                    'product_id' => 2,
                    'quantity' => 101
                ],
                [
                    'product_id' => 3,
                    'quantity' => 101
                ]
            ]
        ];

        $response = $this->postJson('api/order', $data);

        $response->assertStatus(200);
        $response->assertJson(['success' => false]);
    }

    public function testSaveOrderDataToDatabase()
    {
        $this->seed();

        $data = [
            'products' => [
                [
                    'product_id' => 4,
                    'quantity' => 1
                ],
                [
                    'product_id' => 5,
                    'quantity' => 2
                ]
            ]
        ];

        $response = $this->postJson('api/order', $data);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJson(['message' => "Order saved successfully."]);
    }

    public function testProductIdFieldIsRequiredInCreateOrder()
    {
        $this->seed();

        $data = [
            'products' => [
                [
                    'quantity' => 1
                ],
                [
                    'product_id' => 8,
                    'quantity' => 2
                ]
            ]
        ];

        $response = $this->postJson('api/order', $data);

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
    }

    public function testQuantityFieldIsRequiredInCreateOrder()
    {
        $this->seed();

        $data = [
            'products' => [
                [
                    'product_id' => 10,
                    'quantity' => 1
                ],
                [
                    'product_id' => 12
                ]
            ]
        ];

        $response = $this->postJson('api/order', $data);

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
    }

    public function testUpdateStock()
    {
        $this->seed();

        $data = [
            'products' => [
                [
                    'product_id' => 13,
                    'quantity' => 1
                ],
                [
                    'product_id' => 14,
                    'quantity' => 2
                ]
            ]
        ];

        $response = $this->postJson('api/order', $data);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    public function testMailSentWhenStockReachHalfLevel()
    {
        $this->seed();

        Mail::fake();

        $data = [
            'products' => [
                [
                    'product_id' => 16,
                    'quantity' => 45
                ]
            ]
        ];

        $this->postJson('api/order', $data);

        Mail::assertSent(HalfStockOfIngredient::class);
    }
}
