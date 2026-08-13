{{-- universal Blade partial for form inputs --}}
@props([
    'name', // field name
    'label' => null, // optional label
    'type' => 'text', // input type (text, email, password, etc.)
    'value' => null, // default value
    'placeholder' => '', // placeholder text
    'required' => false, // required flag
    'classList' => '', // additional class for element
    'selectList' => [], // list of data for select, pattern ['key' => 'text', ...]
])

@php
    $class = collect([$type=='select'?'form-select':'form-control'])->merge(explode(',', $classList))->when($errors->has($name), fn($c) => $c->push('is-invalid'))->implode(' ')
@endphp

<div class="mb-3">
    @if ($label)
        <label for="{{ $name }}" class="form-label">
            @if ($required)
                <i class="bi bi-info-circle me-1 text-warning" title="Required Field"></i>
            @endif
            {{ $label }}
        </label>
    @endif

    @if ($type == 'select')

    <div class="mb-3">
        <select
            class="{{ $class }}"
            name="{{ $name }}"
            id="{{ $name }}"
        >
        @foreach ($selectList as $key => $text)
        <option value="{{ $key }}" @if ($key == old($name, $value)) selected @endif>{{ $text }}</option>
        @endforeach
        </select>
    </div>


    @else
        <input type="{{ $type }}" name="{{ $name }}" id="{{ $name }}"
            value="{{ old($name, $value) }}" placeholder="{{ $placeholder }}"
            @if ($required) required @endif
            class="{{ $class }}" />
    @endif


    {{-- @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror --}}
</div>
