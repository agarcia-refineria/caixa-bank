<?php

namespace App\Exports;

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
    /**
    * @return Collection
    */
    public function collection(): Collection
    {
        return Auth::user()->balances;
    }

    public function map($row): array
    {
        return [
            'amount' => $row->amount,
            'currency' => $row->currency,
            'balance_type' => $row->balance_type,
            'reference_date' => $row->reference_date?->format('Y-m-d H:i:s'),
        ];
    }

    public function headings(): array
    {
        return [
            'Amount',
            'Currency',
            'Balance Type',
            'Reference Date',
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
}
