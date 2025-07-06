<?php

namespace App\Exports;

use App\Models\Balance;
use Auth;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class BalancesExport implements FromCollection, WithMapping, WithHeadings, WithDrawings
{

    private Collection $collection;

    /**
    * @return Collection
    */
    public function collection(): Collection
    {
        $accounts = Auth::user()->accounts;
        $balances = Balance::whereIn('account_id', $accounts->pluck('id'))->orderBy('reference_date')->get();

        return $this->collection ?? $balances;
    }

    public function map($row): array
    {
        return [
            'id' => $row->id,
            'amount' => $row->amount,
            'currency' => $row->currency,
            'balance_type' => $row->balance_type,
            'reference_date' => $row->reference_date?->format('Y-m-d H:i:s'),
            'account_id' => $row->account_id,
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Amount',
            'Currency',
            'Balance Type',
            'Reference Date',
            'Account ID'
        ];
    }

    /**
     * @throws Exception
     */
    public function drawings(): Drawing
    {
        $drawing = new Drawing();
        $drawing->setName('My Bank');
        $drawing->setDescription('Andres Garcia Bauza - Developer');
        $drawing->setPath(public_path('/img/logo-transparent.png'));
        $drawing->setHeight(1000);
        $drawing->setWidth(1000);
        $drawing->setCoordinates('A1');

        return $drawing;
    }

    public function setCollection(Collection $collection): void
    {
        $this->collection = $collection;
    }
}
