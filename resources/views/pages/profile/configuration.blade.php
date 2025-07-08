<x-app-layout>
    @include('partials.profile.navigation')

    <div class="py-6 md:px-0 px-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div id="configuration-profile-secrets" shepherd-text="{{ trans('shepherd.configuration-profile-secrets') }}" class="p-4 sm:p-8 bg-main2 shadow rounded-lg">
                @include('partials.configurations.update-profile-secret')
            </div>

            @if ($user->NORDIGEN_SECRET_KEY && $user->NORDIGEN_SECRET_ID)
                <div id="configuration-profile-institutions" shepherd-text="{{ trans('shepherd.configuration-profile-institutions') }}" class="p-4 sm:p-8 bg-main2 shadow rounded-lg">
                    @include('partials.configurations.update-profile-institutions')
                </div>

                @if ($institutions->count() > 0)
                    <div id="configuration-profile-lang" shepherd-text="{{ trans('shepherd.configuration-profile-lang') }}" class="p-4 sm:p-8 bg-main2 shadow rounded-lg">
                        @include('partials.configurations.update-profile-lang')
                    </div>

                    <div id="configuration-profile-chars" shepherd-text="{{ trans('shepherd.configuration-profile-chars') }}" class="p-4 sm:p-8 bg-main2 shadow rounded-lg">
                        @include('partials.configurations.update-profile-chars')
                    </div>

                    <div id="configuration-profile-theme" shepherd-text="{{ trans('shepherd.configuration-profile-theme') }}" class="p-4 sm:p-8 bg-main2 shadow rounded-lg">
                        @include('partials.configurations.update-profile-theme')
                    </div>

                    <div id="configuration-profile-accounts-update" shepherd-text="{{ trans('shepherd.configuration-profile-accounts-update') }}" class="p-4 sm:p-8 bg-main2 shadow rounded-lg">
                        @include('partials.configurations.update-profile-accounts')
                    </div>

                    <div id="configuration-profile-accounts-info" shepherd-text="{{ trans('shepherd.configuration-profile-accounts-info') }}" class="p-4 sm:p-8 bg-main2 shadow rounded-lg">
                        @include('partials.configurations.info-profile-accounts')
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-app-layout>
