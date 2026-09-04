@extends ('admin.layouts.app')
@section ('title', 'Add Core Value')
@section ('page-title', 'Home · Add core value')

@section ('content')
    @include ('admin.home.core-values._form', [
        'coreValue' => null,
        'action' => route('admin.home.core-values.store'),
    ])
@endsection
