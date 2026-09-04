@extends ('admin.layouts.app')
@section ('title', 'Add Product')
@section ('page-title', 'Add product')

@section ('content')
    @include ('admin.products._form', [
        'product' => null,
        'action' => route('admin.products.store'),
    ])
@endsection
