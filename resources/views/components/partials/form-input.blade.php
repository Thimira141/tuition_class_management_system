{{-- universal Blade partial for form inputs --}}
@props([
    'name',             // field name
    'label' => null,    // optional label
    'type' => 'text',   // input type (text, email, password, etc.)
    'value' => null,    // default value
    'placeholder' => '',// placeholder text
    'required' => false,// required flag
    'classList' => ''   // additional class for element
])

<div class="mb-3">
    @if($label)
        <label for="{{ $name }}" class="form-label">
            @if($required)<i class="bi bi-info-circle me-1 text-warning" title="Required Field"></i>@endif
            {{ $label }}
        </label>
    @endif

    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        @if($required) required @endif
        class="{{ collect(['form-control'])
                    ->merge(explode(',', $classList))
                    ->when($errors->has($name), fn($c) => $c->push('is-invalid'))
                    ->implode(' ') }}"
    />

    {{-- @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror --}}
</div>
