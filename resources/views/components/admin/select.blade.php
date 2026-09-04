@props([
    'name',
    'label' => null,
    'options' => [],
    'selected' => null,
    'placeholder' => null,
    'hint' => null,
    'required' => false,
])

@php
    $error = $errors->first($name);
    $current = old($name, $selected);
@endphp

<div>
    @if ($label)
        <label for="{{ $name }}" @class(['admin-label', 'admin-required' => $required])>{{ $label }}</label>
    @endif

    <select
        id="{{ $name }}"
        name="{{ $name }}"
        @if ($required) required @endif
        {{ $attributes->class(['admin-select', 'admin-select--invalid' => $error]) }}
    >
        @if ($placeholder !== null)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach ($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" @selected((string) $current === (string) $optionValue)>{{ $optionLabel }}</option>
        @endforeach
        {{ $slot }}
    </select>

    @if ($hint && ! $error)
        <p class="admin-hint">{{ $hint }}</p>
    @endif
    @if ($error)
        <p class="admin-error"><i class="fa-solid fa-circle-exclamation mt-0.5 text-xs"></i> {{ $error }}</p>
    @endif
</div>
