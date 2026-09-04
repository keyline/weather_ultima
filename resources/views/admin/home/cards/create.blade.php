@extends ('admin.layouts.app')
@section ('title', 'Add Dimension Card')
@section ('page-title', 'Home · Add dimension card')

@section ('content')
    @include ('admin.home.cards._form', [
        'card' => null,
        'action' => route('admin.home.cards.store'),
    ])
@endsection
