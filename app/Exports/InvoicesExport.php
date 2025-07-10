<?php


namespace App\Exports;

use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class InvoicesExport implements FromCollection, WithHeadings, WithEvents
{
    protected $invoices;

    public function __construct($invoices)
    {
        $this->invoices = $invoices;
    }

    public function collection()
    {
        return $this->invoices->map(function ($invoice) {
            return [
                $invoice->invoice_no,
                $invoice->customer->name ?? '',
                $invoice->date_of_supply,
                $invoice->time_of_supply,
                $invoice->items->sum('value_of_goods'),
                $invoice->items->sum('amount_of_saleTax'),
                $invoice->items->sum('extra_tax'),
                $invoice->items->sum('further_tax'),
                $invoice->items->sum('total'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Invoice No',
            'Customer',
            'Date of Supply',
            'Time of Supply',
            'Total Before Tax',
            'Amount of Sale Tax',
            'Extra Tax',
            'Further Tax',
            'Grand Total',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $rowCount = $this->invoices->count() + 1; // +1 for heading row

                // Total row data
                $totalBeforeTax = $this->invoices->sum(fn ($i) => $i->items->sum('value_of_goods'));
                $amountOfSaleTax = $this->invoices->sum(fn ($i) => $i->items->sum('amount_of_saleTax'));
                $extraTax = $this->invoices->sum(fn ($i) => $i->items->sum('extra_tax'));
                $furtherTax = $this->invoices->sum(fn ($i) => $i->items->sum('further_tax'));
                $grandTotal = $this->invoices->sum(fn ($i) => $i->items->sum('total'));

                // Set totals in the next row
                $event->sheet->setCellValue('E' . ($rowCount + 1), $totalBeforeTax);
                $event->sheet->setCellValue('F' . ($rowCount + 1), $amountOfSaleTax);
                $event->sheet->setCellValue('G' . ($rowCount + 1), $extraTax);
                $event->sheet->setCellValue('H' . ($rowCount + 1), $furtherTax);
                $event->sheet->setCellValue('I' . ($rowCount + 1), $grandTotal);

                // Add "TOTAL" label
                $event->sheet->setCellValue('D' . ($rowCount + 1), 'TOTAL');
            }
        ];
    }
}
