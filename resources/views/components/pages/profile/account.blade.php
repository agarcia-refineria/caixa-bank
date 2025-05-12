@props(['user', 'account'])

<form @if (isset($account)) action="{{ route('profile.account.update') }}" data-id="{{ $account->code }}" @else action="{{ route('profile.account.create') }}" @endif method="POST" class="relative md:px-0 px-6 dark:bg-[#1c1d20] rounded-lg py-6">
    @csrf

    @if (isset($account))
        @method('patch')
        <input type="hidden" name="id" value="{{ $account->code }}" />
    @endif

    <!-- Show the bank logo and name -->
    <h2 class="flex gap-4 items-center text-lg font-medium text-gray-900 dark:text-gray-100 w-full sm:px-6 lg:px-8 pb-3">
        <img src="{{ $user->bank->institution->logo }}" alt="{{ $user->bank->institution->name }}" width="32" height="32" class="h-8 w-8 mr-2">
        @if (isset($account))
            {{ $account->institution?->name }} - {{ $account->iban }} <span class="md:block hidden">({{ $account->type }})</span>
        @else
            {{ __('Create Manual Account') }}
        @endif
    </h2>

    <!-- Show the account buttons -->
    <div class="grid grid-cols-2 gap-4 py-6 sm:px-6 lg:px-8 w-full">
        <div class="col-span-2 lg:col-span-1">
            <x-inputs.input-label for="owner_name" :value="__('Owner name')" />
            <x-inputs.text-input
                name="owner_name"
                class="w-full"
                :placeholder="__('Owner name')"
                :value="$account->owner_name ?? ''" />
            <x-inputs.input-error :messages="$errors->get('owner_name')" class="mt-2" />
        </div>

        <div class="col-span-2 lg:col-span-1">
            <x-inputs.input-label for="institution" value="{{__('Institution')}}*" />
            <x-inputs.text-input
                name="institution"
                class="w-full"
                required
                :value="isset($account) ? $account->institution?->name : $user->bank->institution?->name"
                placeholder="{{__('Institution')}}*"
                :disabled="true" />
        </div>

        <div class="col-span-2 lg:col-span-1">
            <x-inputs.input-label for="iban" value="{{__('Iban')}}*" />
            <x-inputs.text-input
                name="iban"
                class="w-full"
                :value="$account->iban ?? ''"
                required
                placeholder="{{__('Iban')}}*"/>
            <x-inputs.input-error :messages="$errors->get('iban')" class="mt-2" />
        </div>

        <div class="col-span-2 lg:col-span-1">
            <x-inputs.input-label for="iban" :value="__('bban')" />
            <x-inputs.text-input
                name="bban"
                class="w-full"
                :placeholder="__('bban')"
                :value="$account->bban ?? ''"
                :placeholder="__('bban')" />
            <x-inputs.input-error :messages="$errors->get('bban')" class="mt-2" />
        </div>

        <div class="col-span-2 lg:col-span-1">
            <x-inputs.input-label for="status" :value="__('Status')" />
            <x-inputs.text-input
                name="status"
                class="w-full"
                :value="$account->status ?? ''"
                :placeholder="__('Status')" />
            <x-inputs.input-error :messages="$errors->get('status')" class="mt-2" />
        </div>
    </div>

    @if (isset($account))
        <div class="sm:px-6 lg:px-8">
            <x-buttons.secondary-button type="submit">
                {{ __('Update Account') }}
            </x-buttons.secondary-button>

            <x-buttons.danger-button
                x-data=""
                x-on:click.prevent="$dispatch('open-modal', 'confirm-account-{{ $account->code }}-deletion')"
            >{{ __('Delete Account') }}</x-buttons.danger-button>
        </div>
    @else
        <div class="sm:px-6 lg:px-8">
            <x-buttons.secondary-button type="submit">
                {{ __('Create Account') }}
            </x-buttons.secondary-button>
        </div>
    @endif
</form>

@if (isset($account))
    <x-ui.modal name="confirm-account-{{ $account->code }}-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.account.destroy', ['id' => $account->code]) }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('Are you sure you want to delete your account?') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
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

                <x-inputs.input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-buttons.secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-buttons.secondary-button>

                <x-buttons.danger-button class="ms-3">
                    {{ __('Delete Account') }}
                </x-buttons.danger-button>
            </div>
        </form>
    </x-ui.modal>
@endif
