<x-app-layout>
    <x-site.top-bar :text="__('Requests') . ' API'" />

    <div class="md:px-0 px-4">
        @if ($user->NORDIGEN_SECRET_ID && $user->NORDIGEN_SECRET_KEY)
            <div class="py-6">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex gap-4">
                    <div class="w-full bg-main2 px-4 md:px-0 overflow-hidden shadow-sm rounded-lg">
                        <div>
                            <div class="{{ $class ?? 'max-w-7xl mx-auto sm:px-6 lg:px-8 pt-4' }} ">
                                <h2 class="text-lg font-medium text-gray-100">
                                    {{ __('Accounts update') }}
                                </h2>

                                <p class="mt-1 text-sm text-gray-400">
                                    {{ __('This will add all the accounts!') }}
                                </p>
                            </div>

                            <x-ui.modal name="select-institution-import" focusable>
                                @include('partials.requests.select-institution', [
                                    'user' => $user
                                ])
                            </x-ui.modal>

                            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 pb-4 pt-4">
                                <x-links.nav-link class="cursor-pointer inline-flex items-center px-4 py-1 bg-primary text-third  hover:text-primary hover:bg-secondary hover:border-secondary border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest transition ease-in-out duration-150"
                                                  x-data=""
                                                  x-on:click.prevent="$dispatch('open-modal', 'select-institution-import')">
                                    {{ __('UPDATE Accounts') }}
                                </x-links.nav-link>
                            </div>
                        </div>
                    </div>

                    @if ($showUpdateAccounts)
                        <div class="w-full bg-main2 px-4 md:px-0 overflow-hidden shadow-sm rounded-lg">
                            <x-box.item
                                id="configuration-update-all"
                                :shepherd-text="trans('shepherd.configuration-update-all')"
                                :title="__('Transactions and balances update')"
                                :description="__('This will add all the accounts balances and trasactions!')"
                                :button="__('UPDATE ALL')"
                                :link="route('nordigen.all_accounts')"/>
                        </div>
                    @endif
                </div>
            </div>

            <div id="sortable-accounts" shepherd-text="{{trans('shepherd.sortable-accounts')}}"
                 class="max-w-7xl mx-auto sm:px-6 lg:px-8 pb-4">
                <p class="mt-1 text-sm text-secondary">
                    {{ __("You can reorder the accounts!") }}
                </p>

                @include('partials.requests.accounts', [
                    'user' => $user,
                    'accounts' => $accounts
                ])
            </div>
        @else
            <div class="py-6">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="w-full lg:gap-16 bg-main2 px-4 md:px-0 overflow-hidden shadow-sm rounded-lg">
                        <x-ui.empty
                            :title="__('Accounts update')"
                            :description="__('You need to set the secret id and key on Configuration!', [
                                'link' => route('profile.configuration.edit'),
                            ])"/>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
