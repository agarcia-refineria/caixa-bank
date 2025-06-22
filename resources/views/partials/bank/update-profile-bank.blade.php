<section>
    <header>
        <h2 class="text-lg font-medium text-primary">
            {{ __('Bank Information') }}
        </h2>

        <p class="mt-1 text-sm text-secondary">
            {{ __("If there is no banks please, click update list for banks to be added.") }}
        </p>
        <p class="mt-1 mb-2 text-sm text-warning">
            Sino te aparece ningun entidad bancaria, debes de ir a <strong>GoCardless</strong> y iniciar session, despues ir a Developers -> User Secrets y crear o usar una cuenta para el Secret Id y Secret Key. Una vez hecho, vuelve a esta página y inserta estos datos en los inputs de bajado y guardalo. Ahora te aparezera el boton <strong>Actualizar lista</strong>, una vez le des click te mostrara la todas las entidades bancarias.
        </p>

        <div class="flex gap-4">
            @if ($user->NORDIGEN_SECRET_ID && $user->NORDIGEN_SECRET_KEY)
                <form method="post" action="{{ route('nordigen.institutions') }}">
                    @csrf
                    <x-buttons.primary-button class="mt-2">{{ __('Update List') }}</x-buttons.primary-button>
                </form>
            @endif

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
            <select data-default="{{ __('-- Select an option --') }}" id="institution" name="institution" class="select2 form-control border-third bg-main2 text-primary rounded-md shadow-sm mt-1 block w-full">
                <option value="" disabled selected>{{ __('Select an institution') }}</option>
                @foreach (\App\Models\Institution::all() as $institution)
                    <option value="{{ $institution->id }}" {{ $user->bank && $user->bank?->institution_id == $institution->id ? 'selected' : '' }}>
                        {{ $institution->name }}
                    </option>
                @endforeach
            </select>
            <x-inputs.input-error class="mt-2" :messages="$errors->get('institution')" />
        </div>

        <div class="grid grid-cols-2 gap-4">
            <x-inputs.input type="password" name="NORDIGEN_SECRET_ID" id="NORDIGEN_SECRET_ID" :value="$user->NORDIGEN_SECRET_ID" label="{{ __('SECRET ID') }}" />
            <x-inputs.input type="password" name="NORDIGEN_SECRET_KEY" id="NORDIGEN_SECRET_KEY" :value="$user->NORDIGEN_SECRET_KEY" label="{{ __('SECRET KEY') }}" />
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
