<form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="relative md:px-0 px-6 bg-main2 rounded-lg py-6">
    @csrf

    <!-- Show the bank logo and name -->
    <h2 class="flex gap-4 items-center text-lg font-medium text-primary w-full sm:px-6 lg:px-8">
        {{ __('Import') }} {{ $type }}
    </h2>

    <!-- Show the account buttons -->
    <div class="grid grid-cols-2 gap-4 py-6 sm:px-6 lg:px-8 w-full">
        <div class="col-span-2 lg:col-span-1">
            <x-inputs.input-label for="file_{{ $typeField }}" value="{{ $type }}" />
            <x-inputs.file-input
                name="file_{{ $typeField }}"
                class="w-full"
                placeholder="{{ $type }}" />
            <x-inputs.input-error :messages="$errors->get('file_'.$typeField)" class="mt-2" />
        </div>
    </div>

    <div class="sm:px-6 lg:px-8">
        <x-buttons.secondary-button type="submit">
            {{ __('Import') }} {{ $type }}
        </x-buttons.secondary-button>
    </div>

    <div class="sm:px-6 lg:px-8 pt-6">
        <x-links.nav-link x-data=""
                                    x-on:click.prevent="$dispatch('open-modal', 'example-{{ strtolower($type) }}-import')">
            {{ __('Example import') }}
        </x-links.nav-link>
    </div>

    <x-ui.modal name="example-{{ strtolower($type) }}-import" focusable maxWidth="full" margin="sm:px-[50px]">
        <div class="text-white p-6 lg:mx-12">
            {{ $slot }}
        </div>
    </x-ui.modal>
</form>
