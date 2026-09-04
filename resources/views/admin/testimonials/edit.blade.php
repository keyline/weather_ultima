@extends ('admin.layouts.app')
@section ('title', 'Edit Testimonial')
@section ('page-title', 'Edit testimonial')

@section ('content')
    @include ('admin.testimonials._form', [
        'action' => route('admin.testimonials.update', $testimonial),
        'method' => 'PUT',
    ])
@endsection
