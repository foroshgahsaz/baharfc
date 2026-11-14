<?php

namespace App\Actions\Reports;

use App\Models\Order;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportOrdersCsv
{
    public function download(): StreamedResponse
    {
        $filename = 'orders-'.now()->format('Y-m-d-His').'.csv';

        return response()->streamDownload(function (): void {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($handle, ['شماره', 'وضعیت', 'مبلغ', 'مشتری', 'تاریخ']);

            Order::query()
                ->with('user:id,name')
                ->latest()
                ->chunkById(200, function ($orders) use ($handle): void {
                    foreach ($orders as $order) {
                        fputcsv($handle, [
                            $order->number,
                            $order->status->label(),
                            $order->total,
                            $order->user?->name,
                            $order->created_at?->format('Y-m-d H:i'),
                        ]);
                    }
                });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
