<?php

namespace Database\Seeders;
use App\Models\OrderStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrderStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            'Pending',
            'Processing',
            'Completed',
            'Cancelled',
            'Refunded',
            'Failed',
            'Payment Pending',
            'Payment Failed',
            'Shipped',
            'Delivered',
        ];

        foreach ($statuses as $status) {
            OrderStatus::updateOrCreate(
                ['slug' => Str::slug($status)],
                [
                    'name'   => $status,
                    'status' => 1
                ]
            );
        }
    }
}
