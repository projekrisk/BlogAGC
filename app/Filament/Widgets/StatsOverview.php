<?php

namespace App\Filament\Widgets;

use App\Models\Blog;
use App\Models\Campaign;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Blog Terhubung', Blog::count())
                ->description('Jumlah blog di database')
                ->descriptionIcon('heroicon-m-globe-alt')
                ->color('success'),
                
            Stat::make('Campaign Aktif', Campaign::where('is_active', true)->count())
                ->description('Bot yang sedang bertugas')
                ->descriptionIcon('heroicon-m-bolt')
                ->color('primary'),
                
            Stat::make('Metode Viral (Trends)', Campaign::where('source_type', 'google_trends')->count())
                ->description('Mengejar trafik instan')
                ->descriptionIcon('heroicon-m-fire')
                ->color('danger'),
        ];
    }
}