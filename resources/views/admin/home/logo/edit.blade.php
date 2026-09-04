@extends ('admin.layouts.app')
@section ('title', 'Edit Logo')
@section ('page-title', 'Home · Edit brand logo')

@section ('content')
    @include ('admin.home.logo._form', [
        'action' => route('admin.home.logo.update', $brandLogo),
        'method' => 'PUT',
    ])
@endsection
