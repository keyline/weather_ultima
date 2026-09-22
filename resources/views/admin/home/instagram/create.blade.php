@extends ('admin.layouts.app')
@section ('title', 'Add Instagram Photo')
@section ('page-title', 'Home · Add Instagram photo')

@section ('content')
    @include ('admin.home.instagram._form', [
        'post' => null,
        'action' => route('admin.home.instagram.store'),
    ])
@endsection
