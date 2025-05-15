<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Balance;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DatatableController extends Controller
{
    public function accounts(Request $request): JsonResponse
    {
        // Obtener parámetros
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search', '');
        $orderDir = $request->input('order_dir', 'desc');

        $validOrderColumns = ['iban', 'owner_name', 'balance'];
        $orderBy = $request->input('order_by', 'iban');

        if (!in_array($orderBy, $validOrderColumns)) {
            $orderBy = 'iban'; // columna por defecto segura
        }

        // Consulta base
        $query = Account::query();

        // Búsqueda (ajusta los campos según tu modelo)
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('iban', 'like', "%{$search}%")
                    ->orWhere('owner_name', 'like', "%{$search}%");
            });
        }

        // Total antes de filtrar
        $recordsTotal = Account::count();

        // Total después de filtrar
        $recordsFiltered = $query->count();

        // Ordenamiento y paginación
        $data = $query->orderBy($orderBy, $orderDir)
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        // Respuesta en formato DataTables
        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data->map(function ($account) {
                $lastInstance = $account->balances()->balanceTypeForward()->lastInstance()->first();
                return [
                    'iban' => $account->iban,
                    'owner_name' => $account->owner_name,
                    'balance' => number_format($lastInstance ? $lastInstance->amount : 0, 2, ',', '.') . ' €',
                ];
            })
        ]);
    }

    public function balances(Request $request): JsonResponse
    {
        // Obtener parámetros
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search', '');
        $orderDir = $request->input('order_dir', 'desc');

        $validOrderColumns = ['iban', 'reference_date', 'balance_type', 'amount', 'balance'];
        $orderBy = $request->input('order_by', 'reference_date');

        if (!in_array($orderBy, $validOrderColumns)) {
            $orderBy = 'reference_date'; // columna por defecto segura
        }

        $query = Balance::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('iban', 'like', "%{$search}%")
                    ->orWhere('reference_date', 'like', "%{$search}%")
                    ->orWhere('balance_type', 'like', "%{$search}%")
                    ->orWhere('amount', 'like', "%{$search}%")
                    ->orWhere('currency', 'like', "%{$search}%");
            });
        }

        // Total antes de filtrar
        $recordsTotal = Balance::count();

        // Total después de filtrar
        $recordsFiltered = $query->count();

        // Ordenamiento y paginación
        $data = $query->orderBy($orderBy, $orderDir)
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        // Respuesta en formato DataTables
        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data->map(function ($balance) {
                return [
                    'iban' => $balance->account->iban,
                    'reference_date' => $balance->reference_date->format('d-m-Y'),
                    'balance_type' => $balance->balance_type,
                    'amount' => number_format($balance->amount, 2, ',', '.') . ' €',
                    'currency' => $balance->currency,
                ];
            })
        ]);
    }

    public function transactions(Request $request): JsonResponse
    {
        // Obtener parámetros
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search', '');
        $orderDir = $request->input('order_dir', 'desc');

        $validOrderColumns = ['iban', 'bookingDate', 'debtorNameFormat', 'remittanceInformationUnstructured', 'transactionAmount_amount'];
        $orderBy = $request->input('order_by', 'bookingDate');

        if (!in_array($orderBy, $validOrderColumns)) {
            $orderBy = 'reference_date'; // columna por defecto segura
        }

        $query = Transaction::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('iban', 'like', "%{$search}%")
                    ->orWhere('bookingDate', 'like', "%{$search}%")
                    ->orWhere('debtorNameFormat', 'like', "%{$search}%")
                    ->orWhere('remittanceInformationUnstructured', 'like', "%{$search}%")
                    ->orWhere('transactionAmount_amount', 'like', "%{$search}%");
            });
        }

        // Total antes de filtrar
        $recordsTotal = Transaction::count();

        // Total después de filtrar
        $recordsFiltered = $query->count();

        // Ordenamiento y paginación
        $data = $query->orderBy($orderBy, $orderDir)
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        // Respuesta en formato DataTables
        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data->map(function ($transaction) {
                return [
                    'iban' => $transaction->account->iban,
                    'bookingDate' => $transaction->bookingDate->format('d-m-Y'),
                    'debtorNameFormat' => $transaction->debtorNameFormat,
                    'remittanceInformationUnstructured' => json_decode($transaction->remittanceInformationUnstructured) ? json_decode($transaction->remittanceInformationUnstructured)[0] : '--',
                    'transactionAmount_amount' => number_format($transaction->transactionAmount_amount, 2, ',', '.') . ' €',
                ];
            })
        ]);
    }
}
