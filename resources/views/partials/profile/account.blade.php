@props(['user', 'account'])

<form @if (isset($account)) action="{{ route('profile.account.update') }}" data-id="{{ $account->code }}" @else action="{{ route('profile.account.create') }}" @endif method="POST" class="relative md:px-0 px-6 bg-main2 rounded-lg py-6 my-4 @if (isset($account) && $account->isManual) border-2 border-main3 !drop-shadow-primary @endif ">
    @csrf

    @if (isset($account))
        @method('patch')
        <input type="hidden" name="id" value="{{ $account->code }}" />
    @endif

    <!-- Show the bank logo and name -->
    <h2 class="flex gap-4 items-center text-lg font-medium text-primary w-full sm:px-6 lg:px-8 pb-3">
        @if (isset($account))
            <img src="{{ $account->institution->logo }}" alt="{{ $account->institution->name }}" width="32" height="32" class="h-8 w-8 mr-2">
            {{ $account->institution?->name }} - {{ $account->iban }} <span class="md:block hidden">({{ $account->type }})</span>
        @else
            {{ __('Create Manual Account') }}
        @endif
    </h2>

    @php $isApiAccount = isset($account) && $account->isApi; @endphp

    <!-- Show the account buttons -->
    <div class="grid grid-cols-2 gap-4 py-6 sm:px-6 lg:px-8 w-full">
        <x-inputs.input required="required" :errorName="isset($account) ? 'Account.'.$account->code.'.owner_name' : 'newAccount.owner_name'" :value="isset($account) ? $account->owner_name : null" type="text" name="{{ isset($account) ? 'Account['. $account->code .'][owner_name]' : 'newAccount[owner_name]' }}" :label="__('Owner name')" :disabled="$isApiAccount" />
        <div>
            @php $institutionId = isset($account) ? 'Account.'. $account->code .'.institution_id' : 'newAccount.institution'; @endphp
            @php $institutionName = isset($account) ? 'Account['. $account->code .'][institution_id]' : 'newAccount[institution_id]'; @endphp

            <x-inputs.input-label for="{{ $institutionId }}" :value="__('institution')" />
            <select data-default="{{ __('-- Select an option --') }}" id="{{ $institutionId }}" name="{{ $institutionName }}" class="select2 form-control border-third bg-main2 text-primary rounded-md shadow-sm mt-1 block w-full" @if ($isApiAccount) disabled="disabled" @endif>
                <option value="" selected disabled>{{ __('Select an institution') }}</option>
                @foreach (\App\Models\Institution::all() as $institution)
                    <option value="{{ $institution->id }}" {{ isset($account) && $institution->id == $account->institution->id ? 'selected' : '' }}>
                        {{ $institution->name }}
                    </option>
                @endforeach
            </select>
            <x-inputs.input-error class="mt-2" :messages="$errors->get($institutionId)" />
        </div>
        <x-inputs.input required="required" :errorName="isset($account) ? 'Account.'.$account->code.'.iban' : 'newAccount.iban'" :value="isset($account) ? $account->iban : null" type="text" name="{{ isset($account) ? 'Account['. $account->code .'][iban]' : 'newAccount[iban]' }}" :label="__('Iban')" :disabled="$isApiAccount" />
        <x-inputs.input :errorName="isset($account) ? 'Account.'.$account->code.'.bban' : 'newAccount.bban'" :value="isset($account) ? $account->bban : null" type="text" name="{{ isset($account) ? 'Account['. $account->code .'][bban]' : 'newAccount[bban]' }}" :label="__('bban')" :disabled="$isApiAccount" />
        <x-inputs.input :errorName="isset($account) ? 'Account.'.$account->code.'.status' : 'newAccount.status'" :value="isset($account) ? $account->status : null" type="text" name="{{ isset($account) ? 'Account['. $account->code .'][status]' : 'newAccount[status]' }}" :label="__('Status')" :disabled="$isApiAccount" />
    </div>

    @if (isset($account))
        <div class="sm:px-6 lg:px-8 flex md:flex-row flex-col justify-between">
            <div class="mt-2 flex justify-center md:justify-start gap-4">
                <x-buttons.secondary-button type="submit">
                    {{ __('Update Account') }}
                </x-buttons.secondary-button>
                <x-buttons.danger-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-account-{{ $account->code }}-deletion')">
                    {{ __('Delete Account') }}
                </x-buttons.danger-button>
            </div>

            <div class="mt-2 flex justify-center md:justify-end gap-4">
                <x-links.nav-link :href="route('profile.transaction.edit', ['id' => $account->code])">
                    {{ __('View Transactions') }}
                </x-links.nav-link>

                <x-links.nav-link :href="route('profile.balance.edit', ['id' => $account->code])">
                    {{ __('View Balances') }}
                </x-links.nav-link>
            </div>
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
    <x-ui.modal name="confirm-account-{{ $account->code }}-deletion" :show="$errors->accountDeletion->isNotEmpty() && $errors->accountDeletion->get('Account.'.$account->code.'.password')" focusable>
        <form method="post" action="{{ route('profile.account.destroy', ['id' => $account->code]) }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-primary">
                {{ __('Are you sure you want to delete your account?') }}
            </h2>

            <p class="mt-1 text-sm text-secondary">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
            </p>

            <div class="mt-6">
                <x-inputs.input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                <x-inputs.text-input
                    id="password"
                    name="Account[{{ $account->code }}][password]"
                    type="password"
                    class="mt-1 block w-3/4"
                    placeholder="{{ __('Password') }}"
                />

                <x-inputs.input-error :messages="$errors->accountDeletion->get('Account.'.$account->code.'.password')" class="mt-2" />
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
