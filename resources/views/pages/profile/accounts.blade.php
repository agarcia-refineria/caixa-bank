<x-app-layout>
    @include('partials.profile.navigation')

    <div class="py-6 md:px-0 px-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if ($user->institutions()->count() > 0)
                <div class="flex items-center justify-center md:justify-start gap-4">
                    <x-links.nav-link class="uppercase px-4 py-2 cursor-pointer"
                                      x-data=""
                                      x-on:click.prevent="$dispatch('open-modal', 'confirm-user-create')" id="profile-accounts-create-account" shepherd-text="{{ trans('shepherd.profile-accounts-create-account') }}">
                        {{ __('Create Account') }}
                    </x-links.nav-link>

                    <x-links.nav-link class="uppercase px-4 py-2" :href="route('profile.import.edit')" :active="request()->routeIs(['profile.import.edit'])" id="profile-accounts-import" shepherd-text="{{ trans('shepherd.profile-accounts-import') }}">
                        {{ __('Import') }}
                    </x-links.nav-link>

                    <x-links.nav-link class="uppercase px-4 py-2" :href="route('profile.export.edit')" :active="request()->routeIs(['profile.export.edit'])" id="profile-accounts-export" shepherd-text="{{ trans('shepherd.profile-accounts-export') }}">
                        {{ __('Export') }}
                    </x-links.nav-link>
                </div>


                <x-ui.modal name="confirm-user-create" :show="$errors->get('newAccount.owner_name') || $errors->get('newAccount.iban')" focusable>
                    @include('partials.profile.account', ['user' => $user])
                </x-ui.modal>

                <!-- Show the accounts -->
                @if (count($accounts) > 0)
                    <div id="profile-accounts-forms" shepherd-text="{{ trans('shepherd.profile-accounts-forms') }}">
                        @foreach ($accounts as $account)
                            @include('partials.profile.account', [
                                'user' => $user,
                                'account' => $account
                            ])
                        @endforeach
                    </div>
                @else
                    <x-ui.empty
                        :title="__('No accounts found')"
                        :description="__('Please add an account from update accounts.')" />
                @endif
            @else
                <x-ui.empty
                    :title="__('No institutions found')"
                    :description="__('Please add a institutions from configuration.', ['link' => route('profile.configuration.edit')])" />
            @endif
        </div>
    </div>
</x-app-layout>
