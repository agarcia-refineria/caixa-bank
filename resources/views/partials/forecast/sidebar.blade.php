<aside class="relative md:w-56 bg-main2 p-6">
    <div class="sticky z-20 top-[20px] left-0">
        <h2 class="text-xl mb-6 font-semibold">{{ __('Accounts') }}</h2>
        <nav class="md:block flex justify-center gap-4 space-y-3 flex-wrap">
            @foreach($accounts as $account)
                <a href="{{ route('dashboard.forecastShow', ['id' => $account->code]) }}"
                   class="block px-4 py-2 rounded-lg u-sidebar @if (isset($currentAccount) and $account->code == $currentAccount->code) bg-main3 @endif hover:bg-main3 text-primary">
                    {{ $account->institution ? $account->institution->name : __('Account')  }}
                    <br/>
                    <span class="text-[10px]">{{ $account->iban }}</span>
                </a>
            @endforeach
        </nav>
    </div>
</aside>
