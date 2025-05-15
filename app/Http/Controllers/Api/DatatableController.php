<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Balance;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DatatableController extends Controller
{
    public function accounts(Request $request): JsonResponse
    {
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search', '');
        $orderDir = $request->input('order_dir', 'desc');

        $validOrderColumns = ['iban', 'owner_name', 'balance'];
        $orderBy = $request->input('order_by', 'iban');

        if (!in_array($orderBy, $validOrderColumns)) {
            $orderBy = 'iban';
        }

        $query = Account::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('iban', 'like', "%{$search}%")
                    ->orWhere('owner_name', 'like', "%{$search}%");
            });
        }

        $recordsTotal = Account::count();
        $recordsFiltered = $query->count();

        $data = $query->orderBy($orderBy, $orderDir)
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data->map(function ($account) {
                $lastInstance = $account->balances()->balanceTypeForward()->lastInstance()->first();
                $color = $lastInstance && $lastInstance->amount > 0 ? 'green' : 'red';

                return [
                    'iban' => $account->iban,
                    'owner_name' => $account->owner_name,
                    'balance' => $this::toColor(number_format($lastInstance ? $lastInstance->amount : 0, 2, ',', '.') . ' €', $color),
                ];
            }),
            'totalAmount' => number_format($data->sum(function ($account) {
                return $account->balances()->balanceTypeForward()->lastInstance()->first() ? $account->balances()->balanceTypeForward()->lastInstance()->first()->amount : 0;
            }), 2, ',', '.') . ' €',
        ]);
    }

    public function balances(Request $request, string $id = null): JsonResponse
    {
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search', '');
        $orderDir = $request->input('order_dir', 'desc');

        $validOrderColumns = ['iban', 'reference_date', 'balance_type', 'amount', 'balance'];
        $orderBy = $request->input('order_by', 'reference_date');

        if (!in_array($orderBy, $validOrderColumns)) {
            $orderBy = 'reference_date';
        }

        if ($id) {
            $query = Balance::where('account_id', $id);
        } else {
            $query = Balance::query();
        }

        if (!empty($search)) {
            $query->leftJoin('accounts', 'accounts.id', '=', 'balances.account_id');

            $query->where(function ($q) use ($search) {
                $q->where('accounts.iban', 'like', "%{$search}%")
                    ->orWhere('reference_date', 'like', "%{$search}%")
                    ->orWhere('balance_type', 'like', "%{$search}%")
                    ->orWhere('amount', 'like', "%{$search}%")
                    ->orWhere('currency', 'like', "%{$search}%");
            });
        }

        $recordsTotal = Balance::count();
        $recordsFiltered = $query->count();

        $data = $query->orderBy($orderBy, $orderDir)
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data->map(function ($balance) {
                $color = $balance->amount > 0 ? 'green' : 'red';

                return [
                    'iban' => $balance->account->iban,
                    'reference_date' => $balance->reference_date->format('d-m-Y'),
                    'balance_type' => $balance->balance_type,
                    'amount' => $this::toColor(number_format($balance->amount, 2, ',', '.') . ' €', $color),
                    'currency' => $balance->currency,
                    'actions' => view('partials.datatable.balance-actions', [
                        'balance' => $balance,
                        'account' => $balance->account,
                        'user' => $balance->account->user,
                    ])->render(),
                ];
            }),
            'totalAmount' => number_format($data->sum('amount'), 2, ',', '.') . ' €',
        ]);
    }

    public function transactions(Request $request, string $id = null): JsonResponse
    {
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search', '');
        $orderDir = $request->input('order_dir', 'desc');

        $validOrderColumns = ['iban', 'bookingDate', 'debtorName', 'remittanceInformationUnstructured', 'transactionAmount_amount'];
        $orderBy = $request->input('order_by', 'bookingDate');

        if (!in_array($orderBy, $validOrderColumns)) {
            $orderBy = 'bookingDate';
        }

        if ($id) {
            $query = Transaction::where('account_id', $id);
        } else {
            $query = Transaction::query();
        }

        if (!empty($search)) {
            $query->leftJoin('accounts', 'accounts.id', '=', 'transactions.account_id');

            $query->where(function ($q) use ($search) {
                $q->where('accounts.iban', 'like', "%{$search}%")
                    ->orWhere('bookingDate', 'like', "%{$search}%")
                    ->orWhere('debtorName', 'like', "%{$search}%")
                    ->orWhere('remittanceInformationUnstructured', 'like', "%{$search}%")
                    ->orWhere('transactionAmount_amount', 'like', "%{$search}%");
            });
        }

        $recordsTotal = Transaction::count();
        $recordsFiltered = $query->count();

        $data = $query->orderBy($orderBy, $orderDir)
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data->map(function ($transaction) {
                $color = $transaction->transactionAmount_amount > 0 ? 'green' : 'red';

                return [
                    'iban' => $transaction->account->iban,
                    'bookingDate' => $transaction->bookingDate->format('d-m-Y'),
                    'debtorName' => $transaction->debtorNameFormat,
                    'remittanceInformationUnstructured' => json_decode($transaction->remittanceInformationUnstructured) ? json_decode($transaction->remittanceInformationUnstructured)[0] : '--',
                    'transactionAmount_amount' => $this::toColor(number_format($transaction->transactionAmount_amount, 2, ',', '.') . ' €', $color),
                    'actions' => view('partials.datatable.transaction-actions', [
                        'transaction' => $transaction,
                        'account' => $transaction->account,
                        'user' => $transaction->account->user,
                    ])->render(),
                ];
            }),
            'totalAmount' => number_format($data->sum('transactionAmount_amount'), 2, ',', '.') . ' €',
        ]);
    }

    /**
     * Applies a color to the text.
     *
     * @param string $text The text to color.
     * @param string $color The color to apply.
     * @return string The colored text.
     */
    public static function toColor(string $text, string $color): string
    {
        return "<span style='color: {$color};'>{$text}</span>";
    }
}
