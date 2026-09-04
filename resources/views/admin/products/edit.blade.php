@extends ('admin.layouts.app')
@section ('title', 'Edit Product')
@section ('page-title', 'Edit product')

@section ('content')
    @include ('admin.products._form', [
        'action' => route('admin.products.update', $product),
        'method' => 'PUT',
    ])
@endsection
