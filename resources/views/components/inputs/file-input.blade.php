@props(['disabled' => false])
@php $disabledClass = $disabled ? ' opacity-50 cursor-not-allowed' : ''; @endphp

<input
    {{ $disabled ? 'disabled' : '' }}
    class="p-4 block w-full text-sm border rounded-lg cursor-pointer mt-2
           text-primary focus:outline-none bg-main2 border-primary
           file:mr-4 file:py-2 file:px-4
           file:rounded-lg file:border-0
           file:text-sm file:font-semibold
           file:bg-main3 file:text-secondary hover:file:text-primary"
    id="file_input"
    type="file"
/>
