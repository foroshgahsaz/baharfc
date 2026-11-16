<?php

namespace App\Actions\Reports;

use App\Enums\TrafficEntityType;
use App\Models\TrafficDailyStat;
use App\Services\Traffic\TrafficStatsService;
use Carbon\CarbonInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportTrafficStatsCsv
{
    public function __construct(
        private TrafficStatsService $stats,
    ) {}

    public function download(CarbonInterface $from, CarbonInterface $to): StreamedResponse
    {
        $filename = 'traffic-'.$from->format('Y-m-d').'_'.$to->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($from, $to): void {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($handle, ['تاریخ', 'نوع', 'شناسه', 'عنوان', 'بازدید']);

            TrafficDailyStat::query()
                ->whereBetween('stat_date', [$from->toDateString(), $to->toDateString()])
                ->orderBy('stat_date')
                ->orderBy('entity_type')
                ->lazy()
                ->each(function (TrafficDailyStat $row) use ($handle): void {
                    $type = $row->entity_type;
                    if (! $type instanceof TrafficEntityType) {
                        return;
                    }

                    $label = $this->stats->labelFor($type, $row->entity_key);

                    fputcsv($handle, [
                        $row->stat_date->format('Y-m-d'),
                        $type->label(),
                        $row->entity_key,
                        $label,
                        $row->views,
                    ]);
                });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}