@extends ('admin.layouts.app')
@section ('title', 'Add Logo')
@section ('page-title', 'Home · Add brand logo')

@section ('content')
    @include ('admin.home.logo._form', [
        'brandLogo' => null,
        'action' => route('admin.home.logo.store'),
    ])
@endsection
