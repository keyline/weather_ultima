@extends ('admin.layouts.app')
@section ('title', 'Add Testimonial')
@section ('page-title', 'Add testimonial')

@section ('content')
    @include ('admin.testimonials._form', [
        'testimonial' => null,
        'action' => route('admin.testimonials.store'),
    ])
@endsection
