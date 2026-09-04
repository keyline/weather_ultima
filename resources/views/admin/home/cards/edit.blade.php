@extends ('admin.layouts.app')
@section ('title', 'Edit Dimension Card')
@section ('page-title', 'Home · Edit dimension card')

@section ('content')
    @include ('admin.home.cards._form', [
        'action' => route('admin.home.cards.update', $card),
        'method' => 'PUT',
    ])
@endsection
