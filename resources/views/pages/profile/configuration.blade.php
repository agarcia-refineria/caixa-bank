<x-app-layout>
    @include('partials.profile.navigation')

    <div class="py-6 md:px-0 px-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div id="bank-profile-bank" shepherd-text="{{ trans('shepherd.bank-profile-bank') }}" class="p-4 sm:p-8 bg-main2 shadow rounded-lg">
                @include('partials.configurations.update-profile-institutions')
            </div>

            @if ($institutions->count() > 0 && $user->NORDIGEN_SECRET_KEY && $user->NORDIGEN_SECRET_ID)
                <div id="bank-profile-lang" shepherd-text="{{ trans('shepherd.bank-profile-lang') }}" class="p-4 sm:p-8 bg-main2 shadow rounded-lg">
                    @include('partials.configurations.update-profile-lang')
                </div>

                <div id="bank-profile-chars" shepherd-text="{{ trans('shepherd.bank-profile-chars') }}" class="p-4 sm:p-8 bg-main2 shadow rounded-lg">
                    @include('partials.configurations.update-profile-chars')
                </div>

                <div id="bank-profile-theme" shepherd-text="{{ trans('shepherd.bank-profile-theme') }}" class="p-4 sm:p-8 bg-main2 shadow rounded-lg">
                    @include('partials.configurations.update-profile-theme')
                </div>

                <div id="bank-profile-accounts-update" shepherd-text="{{ trans('shepherd.bank-profile-accounts-update') }}" class="p-4 sm:p-8 bg-main2 shadow rounded-lg">
                    @include('partials.configurations.update-profile-accounts')
                </div>

                <div id="bank-profile-accounts-info" shepherd-text="{{ trans('shepherd.bank-profile-accounts-info') }}" class="p-4 sm:p-8 bg-main2 shadow rounded-lg">
                    @include('partials.configurations.info-profile-accounts')
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
