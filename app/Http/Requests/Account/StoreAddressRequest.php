<?php

namespace App\Http\Requests\Account;

use App\Support\IranLocations;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:100'],
            'recipient_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'province' => ['required', 'string', Rule::in(IranLocations::provinceNames())],
            'city' => ['required', 'string', 'max:100'],
            'address_line' => ['required', 'string', 'max:1000'],
            'postal_code' => ['nullable', 'string', 'regex:/^\d{10}$/'],
            'is_default' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'عنوان آدرس را وارد کنید.',
            'recipient_name.required' => 'نام گیرنده را وارد کنید.',
            'phone.required' => 'شماره تماس را وارد کنید.',
            'province.required' => 'استان را انتخاب کنید.',
            'city.required' => 'شهر را انتخاب کنید.',
            'address_line.required' => 'آدرس کامل را وارد کنید.',
            'postal_code.regex' => 'کد پستی باید ۱۰ رقم باشد.',
        ];
    }
}
