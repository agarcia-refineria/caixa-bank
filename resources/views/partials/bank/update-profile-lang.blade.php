<section>
    <header>
        <h2 class="text-lg font-medium text-primary">
            {{ __('Default Lang') }}
        </h2>

        <p class="mt-1 text-sm text-secondary">
            {{ __("Set the default lang of the web.") }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.bank.lang') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-inputs.input-label for="name" :value="__('Lang')" />
            <select data-default="{{ __('-- Select an option --') }}" id="lang" name="lang" class="select2 form-control border-third bg-main2 text-primary rounded-md shadow-sm mt-1 block w-full" required>
                <option value="" disabled selected>{{ __('Select a lang') }}</option>
                @foreach(config('app.supported_locales') as $key => $lang)
                    <option value="{{ $lang }}" {{ $user->lang == $lang ? 'selected' : '' }}>
                        {{ __('langs.' . $lang) }}
                    </option>
                @endforeach
            </select>
            <x-inputs.input-error class="mt-2" :messages="$errors->get('lang')" />
        </div>

        <div class="flex items-center gap-4">
            <x-buttons.primary-button>{{ __('Save') }}</x-buttons.primary-button>

            @if (session('status') === 'lang-updated')
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
