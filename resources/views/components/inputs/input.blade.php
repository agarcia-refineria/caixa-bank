@php $required = $required ?? false; @endphp
@php $disabled = $disabled ?? false; @endphp

<div class="col-span-3 md:col-span-1 flex !flex-col">
    <x-inputs.input-label for="{{ $name }}" value="{{ __($label) }}{{ $required ? '*' : '' }}" />
    <x-inputs.text-input
        type="{{ $type }}"
        name="{{ $name }}"
        class="w-full"
        step="{{ $step ?? null }}"
        maxlength="{{ $maxlength ?? null }}"
        minlength="{{ $minlength ?? null }}"
        value="{{ $value ?? null }}"
        placeholder="{{ __($label) }} {{ $required ? '*' : '' }}"
        :required="$required"
        :disabled="$disabled"/>

    @if (isset($errorBag) && $errorBag)
        <x-inputs.input-error :messages="isset($errors) ? $errors->{$errorBag}->get($errorName ?? $name) : null" class="mt-2" />
    @else
        <x-inputs.input-error :messages="isset($errors) ? $errors->get($errorName ?? $name) : null" class="mt-2" />
    @endif
</div>
