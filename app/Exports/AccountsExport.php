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

class AccountsExport implements FromCollection, WithMapping, WithHeadings,WithDrawings
{

    private Collection $collection;

    /**
    * @return Collection
    */
    public function collection(): Collection
    {
        return $this->collection ?? Auth::user()->accounts()->orderBy('order')->get();
    }


    public function map($row): array
    {
        return [
            'id' => $row->code,
            'name' => $row->name,
            'iban' => $row->iban,
            'bban' => $row->bban,
            'status' => $row->status,
            'owner_name' => $row->owner_name,
            'created' => $row->created_at?->format('Y-m-d H:i:s'),
            'last_accessed' => $row->last_accessed ? $row->last_accessed->format('Y-m-d H:i:s') : null,
            'institution_id' => $row->institution_id
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'IBAN',
            'BBAN',
            'Status',
            'Owner Name',
            'Created At',
            'Last Accessed',
            'Institution ID'
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
