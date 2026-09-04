<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        return view('products', [
            'products' => Product::query()->active()->latest()->get(),
        ]);
    }
}
