<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\ShopSetting;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function __invoke(): View
    {
        $settings = ShopSetting::current();
        abort_unless($settings->contact_form_enabled, 404);

        return view('shop.contact', [
            'settings' => $settings,
            'title' => $settings->contact_form_title ?: 'تماس با ما',
        ]);
    }
}