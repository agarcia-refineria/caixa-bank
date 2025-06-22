<x-app-layout>
    @if ($user->bank)
        <x-slot name="header">
            <div class="flex gap-4 items-center">
                <img src="{{ $user->bank->institution->logo }}" alt="{{ $user->bank->institution->name }}" width="32"
                     height="32" class="h-8 w-8 mr-2">
                <h2 class="font-semibold text-xl text-primary leading-tight">
                    {{ $user->bank->institution->name }} API
                </h2>
            </div>
        </x-slot>
    @endif

    <div class="md:px-0 px-4">
        @if ($user->NordigenSecretIdReturn && $user->NordigenSecretKeyReturn)
            @if ($user->bank)
                <div class="py-6">
                    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex gap-4">
                        <div class="w-full bg-main2 px-4 md:px-0 overflow-hidden shadow-sm rounded-lg">
                            <x-box.item
                                id="configuration-accounts-update"
                                :shepherd-text="trans('shepherd.configuration-accounts-update')"
                                :title="__('Accounts update')"
                                :description="__('This will add all the accounts!')"
                                :button="__('UPDATE Accounts')"
                                :link="route('nordigen.auth')"/>
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

                    @include('partials.configuration.accounts', [
                        'user' => $user,
                        'accounts' => $accounts
                    ])
                </div>
            @else
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <x-ui.empty
                        :title="__('No banks found')"
                        :description="__('Please add a bank from profile to see the accounts.')"/>
                </div>
            @endif
        @else
            <div class="py-6">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="w-full lg:gap-16 bg-main2 px-4 md:px-0 overflow-hidden shadow-sm rounded-lg">
                        <x-ui.empty
                            :title="__('Accounts update')"
                            :description="__('You need to set the secret id and key on Profile -> Bank!')"/>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
