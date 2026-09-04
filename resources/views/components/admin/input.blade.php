@props([
    'name',
    'label' => null,
    'type' => 'text',
    'value' => null,
    'hint' => null,
    'required' => false,
])

@php $error = $errors->first($name); @endphp

<div>
    @if ($label)
        <label for="{{ $name }}" @class(['admin-label', 'admin-required' => $required])>{{ $label }}</label>
    @endif

    <input
        id="{{ $name }}"
        name="{{ $name }}"
        type="{{ $type }}"
        value="{{ old($name, $value) }}"
        @if ($required) required @endif
        {{ $attributes->class(['admin-input', 'admin-input--invalid' => $error]) }}
    />

    @if ($hint && ! $error)
        <p class="admin-hint">{{ $hint }}</p>
    @endif
    @if ($error)
        <p class="admin-error"><i class="fa-solid fa-circle-exclamation mt-0.5 text-xs"></i> {{ $error }}</p>
    @endif
</div>
