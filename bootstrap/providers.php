<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\JalaliServiceProvider;

return [
    AppServiceProvider::class,
    JalaliServiceProvider::class,
    AdminPanelProvider::class,
];
