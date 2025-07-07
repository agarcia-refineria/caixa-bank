<section>
    <header>
        <h2 class="text-lg font-medium text-primary">
            {{ __('Bank Information') }}
        </h2>

        <p class="mt-1 text-sm text-secondary">
            {!! __('Sino te aparece ningun entidad bancaria, debes de ir a <strong>GoCardless</strong> y iniciar session, despues ir a Developers -> User Secrets y crear o usar una cuenta para el Secret Id y Secret Key. Una vez hecho, vuelve a esta página y inserta estos datos en los inputs de bajado y guardalo. Ahora te aparezera el boton <strong>Actualizar lista</strong>, una vez le des click te mostrara la todas las entidades bancarias.') !!}
        </p>

        <div class="flex gap-4 mt-4">
            @if ($user->NORDIGEN_SECRET_ID && $user->NORDIGEN_SECRET_KEY)
                <form method="post" action="{{ route('nordigen.institutions') }}">
                    @csrf
                    <x-buttons.primary-button class="mt-2">{{ __('Update List') }}</x-buttons.primary-button>
                </form>
            @endif

            <x-links.nav-link href="https://bankaccountdata.gocardless.com/overview/" target="_blank">
                GoCardless
            </x-links.nav-link>
        </div>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.configuration.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-inputs.input-label for="institutions" :value="__('Institutions')" />
            <select data-default="{{ __('-- Select an option --') }}" id="institutions" name="institutions[]" multiple class="select2 form-control border-third bg-main2 text-primary rounded-md shadow-sm mt-1 block w-full">
                <option value="" disabled>{{ __('Select an institution') }}</option>
                @foreach (\App\Models\Institution::all() as $institution)
                    <option value="{{ $institution->id }}" {{ in_array($institution->id, $user->institutions->pluck('id')->toArray()) ? 'selected' : '' }}>
                        {{ $institution->name }}
                    </option>
                @endforeach
            </select>
            <x-inputs.input-error class="mt-2" :messages="$errors->get('institutions')" />
        </div>

        <div class="grid grid-cols-2 gap-4">
            @php
                $NORDIGEN_SECRET_ID = $user->NORDIGEN_SECRET_ID ? '✅' : '❌';
                $NORDIGEN_SECRET_KEY = $user->NORDIGEN_SECRET_KEY ? '✅' : '❌';
            @endphp
            <x-inputs.input :type="session('secret_id') ? 'text' : 'password'" value="{{ session('secret_id') }}" name="NORDIGEN_SECRET_ID" id="NORDIGEN_SECRET_ID" label="{{ __('SECRET ID') }} ({{ $NORDIGEN_SECRET_ID }})" />
            <x-inputs.input :type="session('secret_key') ? 'text' : 'password'" value="{{ session('secret_key') }}" name="NORDIGEN_SECRET_KEY" id="NORDIGEN_SECRET_KEY" label="{{ __('SECRET KEY') }} ({{ $NORDIGEN_SECRET_KEY }})" />
        </div>

        <div class="flex items-center gap-4">
            <x-buttons.primary-button>{{ __('Save') }}</x-buttons.primary-button>
            <x-buttons.primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-view-api-keys')">{{ __('View') }} {{ __('SECRET') }}</x-buttons.primary-button>

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

    <x-ui.modal name="confirm-view-api-keys" :show="$errors->apiKeysBag->isNotEmpty()" focusable>
        <form id="confirm-view-api-keys" method="POST" action="{{ route('profile.configuration.viewApi') }}" class="p-6">
            @csrf

            <h2 class="text-lg font-medium text-primary">
                {{ __('View api keys') }}
            </h2>

            <p class="mt-1 text-sm text-secondary">
                {{ __('To ensure protected data you need to insert your user password.') }}
            </p>

            <div class="mt-6">
                <x-inputs.input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                <x-inputs.text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4"
                    placeholder="{{ __('Password') }}"
                />

                <x-inputs.input-error :messages="$errors->downloadBag->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-buttons.secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-buttons.secondary-button>

                <x-buttons.primary-button class="ms-3">
                    {{ __('DOWNLOAD') }}
                </x-buttons.primary-button>
            </div>
        </form>
    </x-ui.modal>
</section>
