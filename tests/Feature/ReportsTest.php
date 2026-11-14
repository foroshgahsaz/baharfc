<?php

namespace Tests\Feature;

use App\Actions\Reports\ExportOrdersCsv;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportsTest extends TestCase
{
    use RefreshDatabase;

    public function test_sales_metrics_aggregate_paid_orders(): void
    {
        $user = User::factory()->create();

        Order::query()->create([
            'number' => 'BHR-R1',
            'user_id' => $user->id,
            'status' => OrderStatus::Paid,
            'subtotal' => 100000,
            'total' => 100000,
            'recipient_name' => 'a',
            'recipient_phone' => '09120000000',
            'province' => 'تهران',
            'city' => 'تهران',
            'address_line' => 'x',
            'paid_at' => now(),
        ]);

        Order::query()->create([
            'number' => 'BHR-R2',
            'user_id' => $user->id,
            'status' => OrderStatus::PendingPayment,
            'subtotal' => 50000,
            'total' => 50000,
            'recipient_name' => 'a',
            'recipient_phone' => '09120000000',
            'province' => 'تهران',
            'city' => 'تهران',
            'address_line' => 'x',
        ]);

        $paidSum = Order::query()
            ->whereIn('status', [OrderStatus::Paid, OrderStatus::Processing, OrderStatus::Shipped, OrderStatus::Delivered])
            ->sum('total');

        $this->assertSame(100000, (int) $paidSum);
    }

    public function test_orders_csv_export_contains_header(): void
    {
        $user = User::factory()->create(['name' => 'مشتری نمونه']);
        Order::query()->create([
            'number' => 'BHR-CSV-1',
            'user_id' => $user->id,
            'status' => OrderStatus::Paid,
            'subtotal' => 1,
            'total' => 1,
            'recipient_name' => 'a',
            'recipient_phone' => '09120000000',
            'province' => 'تهران',
            'city' => 'تهران',
            'address_line' => 'x',
        ]);

        $response = app(ExportOrdersCsv::class)->download();

        ob_start();
        $response->sendContent();
        $content = ob_get_clean();

        $this->assertStringContainsString('BHR-CSV-1', $content);
        $this->assertStringContainsString('شماره', $content);
    }
}
