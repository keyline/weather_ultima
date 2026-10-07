<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateFooterMenuOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'menu_order' => ['required', 'array:home,about,products,services,contact,blog', 'size:6'],
            'menu_order.*' => ['required', 'integer', 'between:1,6', 'distinct:strict'],
        ];
    }
}
