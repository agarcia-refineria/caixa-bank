<section>
    <header>
        <h2 class="text-lg font-medium text-primary">
            {{ __('Bank Information') }}
        </h2>

        <p class="mt-1 text-sm text-secondary">
            {{ __("If there is no banks please, click update list for banks to be added.") }}
        </p>
        <p class="mt-1 mb-2 text-sm text-warning">
            Si te aparece un mensaje de error al intentar actualizar la lista de bancos, por favor, asegúrate de que tu cuenta de GoCardless esté activa y que hayas configurado correctamente la integración con Nordigen. Si el problema persiste, contacta con el soporte técnico para obtener ayuda adicional.
        </p>

        <div class="flex gap-4">
            <form method="post" action="{{ route('nordigen.institutions') }}">
                @csrf
                <x-buttons.primary-button class="mt-2">{{ __('Update List') }}</x-buttons.primary-button>
            </form>

            <x-links.nav-link href="https://bankaccountdata.gocardless.com/overview/" target="_blank">
                GoCardless {{ __('Update') }}
            </x-links.nav-link>
        </div>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.bank.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-inputs.input-label for="name" :value="__('Institution')" />
            <select data-default="{{ __('-- Select an option --') }}" id="institution" name="institution" class="select2 form-control border-third bg-main2 text-primary rounded-md shadow-sm mt-1 block w-full" required>
                <option value="" disabled selected>{{ __('Select an institution') }}</option>
                @foreach (\App\Models\Institution::all() as $institution)
                    <option value="{{ $institution->id }}" {{ $user->bank && $user->bank?->institution_id == $institution->id ? 'selected' : '' }}>
                        {{ $institution->name }}
                    </option>
                @endforeach
            </select>
            <x-inputs.input-error class="mt-2" :messages="$errors->get('institution')" />
        </div>

        <div class="flex items-center gap-4">
            <x-buttons.primary-button>{{ __('Save') }}</x-buttons.primary-button>

            @if (session('status') === 'bank-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-secondary"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
