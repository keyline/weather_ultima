@extends ('admin.layouts.app')
@section ('title', 'Edit Core Value')
@section ('page-title', 'Home · Edit core value')

@section ('content')
    @include ('admin.home.core-values._form', [
        'action' => route('admin.home.core-values.update', $coreValue),
        'method' => 'PUT',
    ])
@endsection
