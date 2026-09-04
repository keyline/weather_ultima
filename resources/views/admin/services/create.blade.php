@extends ('admin.layouts.app')
@section ('title', 'Add Service')
@section ('page-title', 'Add service')

@section ('content')
    <div class="mx-auto max-w-3xl">
        @include ('admin.services._form', [
            'service' => null,
            'action' => route('admin.services.store'),
        ])
    </div>
@endsection
