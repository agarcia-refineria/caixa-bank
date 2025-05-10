<aside class="md:w-56 bg-[#1a1b1e] p-6">
    <h2 class="text-xl mb-6 font-semibold">{{ __('Dashboard') }}</h2>
    <nav class="md:block flex justify-center gap-4 space-y-3">
        @foreach($accounts as $account)
            <a href="{{ route('bank.show', ['id' => $account->code]) }}"
               class="block px-4 py-2 rounded-lg u-sidebar
               @if (isset($currentAccount) and $account->code == $currentAccount->code)
               bg-[#2b2d30]
               @endif hover:bg-[#2b2d30] text-gray-300">
                {{ __('Account') }} <br/> <span class="text-[10px]">{{ $account->iban }}</span>
            </a>
        @endforeach
    </nav>
</aside>
