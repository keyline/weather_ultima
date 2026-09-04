@props([
    'name',
    'label' => null,
    'value' => null,
    'hint' => null,
    'required' => false,
    'rows' => 4,
])

@php $error = $errors->first($name); @endphp

<div>
    @if ($label)
        <label for="{{ $name }}" @class(['admin-label', 'admin-required' => $required])>{{ $label }}</label>
    @endif

    <textarea
        id="{{ $name }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        @if ($required) required @endif
        {{ $attributes->class(['admin-textarea', 'admin-textarea--invalid' => $error]) }}
    >{{ old($name, $value) }}</textarea>

    @if ($hint && ! $error)
        <p class="admin-hint">{{ $hint }}</p>
    @endif
    @if ($error)
        <p class="admin-error"><i class="fa-solid fa-circle-exclamation mt-0.5 text-xs"></i> {{ $error }}</p>
    @endif
</div>
