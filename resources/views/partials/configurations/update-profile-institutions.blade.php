<section>
    <header>
        <h2 class="text-lg font-medium text-primary">
            {{ __('Institutions Information') }}
        </h2>

        <p class="mt-1 text-sm text-secondary">
            {{ __('Manage your bank institutions and their configurations.') }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.configuration.institutions') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <input type="hidden" name="institutions" value="{{ $user->institutions()->orderBy('name')->get()->pluck('id')->implode(',') }}" id="institutions">
        <div id="institutions-info" data-empty="{{ __('No institutions found. Please add an institution.') }}" style="display: none">
            @foreach ($user->institutions()->orderBy('name')->get() as $institution)
                <div id="institution-{{ $institution->id }}" data-name="{{ $institution->name }}" data-logo="{{ $institution->logo }}" data-linked="true" style="display: none;"></div>
            @endforeach
        </div>

        <div>
            <x-inputs.input-label for="country-select" :value="__('Institutions')" />
            <div class="js-institutions flex flex-col gap-2">
                @if (!$user->institutions()->orderBy('name')->get()->isEmpty())
                    @foreach ($user->institutions()->orderBy('name')->get() as $institution)
                        <div class="flex items-center justify-between p-2 bg-main2 text-white border-2 border-third rounded-md">
                            <img width="32" height="32" src="{{ $institution->logo }}" alt="{{ $institution->name }}" class="inline-block mr-2">
                            <span>{{ $institution->name }}</span>
                            <button type="button" class="text-error hover:opacity-50" onclick="removeInstitution('{{ $institution->id }}')">
                                &times;
                            </button>
                        </div>
                    @endforeach
                @else
                    <p class="text-sm text-secondary">
                        {{ __('No institutions found. Please add an institution.') }}
                    </p>
                @endif
            </div>

            <x-buttons.primary-button type="submit">
                {{ __('Save') }}
            </x-buttons.primary-button>
            <x-buttons.primary-button class="mt-4" x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-institution-countries')">
                {{ __('Add Institution') }}
            </x-buttons.primary-button>
        </div>
    </form>

    <x-ui.modal name="add-institution-countries" focusable>
        <div class="p-6">
            <h2 class="text-xl font-medium text-primary">
                {{ __('Select your country') }}
            </h2>

            <x-inputs.text-input type="text" id="search-country" name="search-country" placeholder="{{ __('Search country...') }}" class="my-4 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary" />

            <div class="mt-6 flex flex-col justify-center js-country-list">
                @foreach(\App\Models\Institution::getInstitutionsGroupedByCountry() as $country => $institutions)
                    <h3 class="text-lg text-primary py-4 border-t-[1px] border-white/50" data-searchable="{{ trans('countries.'.$country) }}">
                        <span class="flex items-center gap-4 px-10 cursor-pointer" x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-institution-fields')" onclick="setCountryInstitution('{{ $country }}')">
                            <img src="https://flagcdn.com/24x18/{{ strtolower($country) }}.png" alt="{{ $country }}"/> {{ trans('countries.'.$country) }}
                        </span>
                    </h3>
                @endforeach
            </div>

            <div class="mt-6 flex justify-end">
                <x-buttons.secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-buttons.secondary-button>
            </div>
        </div>
    </x-ui.modal>

    <x-ui.modal name="add-institution-fields" focusable>
        <div class="p-6">
            <h2 class="text-xl font-medium text-primary">
                {{ __('Select your institution') }}
            </h2>

            <x-inputs.text-input type="text" id="search-institutions" name="search-institutions" placeholder="{{ __('Search Institution...') }}" class="my-4 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary" />

            <div class="mt-6 flex flex-col justify-center js-institutions-list">
                @foreach(\App\Models\Institution::getInstitutionsGroupedByCountry() as $country => $institutions)
                    @foreach($institutions as $institution)
                        <h3 data-country="{{ $country }}" class="text-lg text-primary py-4 border-t-[1px] border-white/50" data-searchable="{{ $institution->name }}">
                            <span class="flex items-center gap-4 px-10 cursor-pointer" onclick="addInstitution('{{ $institution->id }}', '{{ $institution->name }}', '{{ $institution->logo }}')">
                                <img width="32" height="32" src="{{ $institution->logo }}" alt="{{ $institution->name }}"/> {{ $institution->name }}
                            </span>
                        </h3>
                    @endforeach
                @endforeach
            </div>

            <div class="mt-6 flex justify-end">
                <x-buttons.secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-buttons.secondary-button>
            </div>
        </div>
    </x-ui.modal>

    @vite(['resources/js/institutions.js'])
</section>
