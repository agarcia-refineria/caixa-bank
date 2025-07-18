@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

@if (session('warning'))
    <div class="alert alert-warning">
        {{ session('warning') }}
    </div>
@endif

@if (session('info'))
    <div class="alert alert-info">
        {{ session('info') }}
    </div>
@endif


@php
    $user = Auth::user();
    $nordigenResponse = $user->nordigen_response;
@endphp

@if ($nordigenResponse)
    @php
        $nordigenResponseArray = json_decode($nordigenResponse);

        $transactions = $nordigenResponseArray->transactions ?? [];
        $balances = $nordigenResponseArray->balances ?? [];
    @endphp
    <div class="alert alert-nordigen alert-info">
        @if (!empty($transactions))
            <div>
                <strong>{{ __('Transactions') }}:</strong>
                <div class="flex flex-col gap-[20px] pt-[15px]">
                    @foreach ($transactions as $accountId => $transaction)
                        <div class="border-main1 border-[1px] p-2">
                            {{ $accountId }}<br/>
                            {{ $transaction->original->message }}
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if (!empty($balances))
            <div>
                <strong>{{ __('Balances') }}:</strong>
                <div class="flex flex-col gap-[20px] pt-[15px]">
                    @foreach ($balances as $accountId => $balance)
                        <div class="border-main1 border-[1px] p-2">
                            {{ $accountId }}<br/>
                            {{ $balance->original->message }}
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    @php
        $user->nordigen_response = null;
        $user->save();
    @endphp
@endif
