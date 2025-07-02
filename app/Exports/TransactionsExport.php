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

class TransactionsExport implements FromCollection, WithMapping, WithHeadings, WithDrawings
{

    private Collection $collection;

    /**
    * @return Collection
    */
    public function collection(): Collection
    {
        return $this->collection ?? Auth::user()->transactions;
    }

    public function map($row): array
    {
        return [
            'id' => $row->id,
            'entryReference' => $row->entryReference,
            'checkId' => $row->checkId,
            'bookingDate' => $row->bookingDate ? $row->bookingDate->format('Y-m-d') : null,
            'valueDate' => $row->valueDate ? $row->valueDate->format('Y-m-d') : null,
            'transactionAmount_amount' => $row->transactionAmount_amount,
            'transactionAmount_currency' => $row->transactionAmount_currency,
            'remittanceInformationUnstructured' => $row->remittanceInformationUnstructured,
            'bankTransactionCode' => $row->bankTransactionCode,
            'proprietaryBankTransactionCode' => $row->proprietaryBankTransactionCode,
            'internalTransactionId' => $row->internalTransactionId,
            'debtorName' => $row->debtorName,
            'debtorAccount' => $row->debtorAccount,
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Entry Reference',
            'Check ID',
            'Booking Date',
            'Value Date',
            'Transaction Amount (Amount)',
            'Transaction Amount (Currency)',
            'Remittance Information Unstructured',
            'Bank Transaction Code',
            'Proprietary Bank Transaction Code',
            'Internal Transaction ID',
            'Debtor Name',
            'Debtor Account',
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
