@extends ('admin.layouts.app')
@section ('title', 'Edit Instagram Photo')
@section ('page-title', 'Home · Edit Instagram photo')

@section ('content')
    @include ('admin.home.instagram._form', [
        'action' => route('admin.home.instagram.update', $post),
        'method' => 'PUT',
    ])
@endsection
