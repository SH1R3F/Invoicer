<?php

namespace App\Exports;

use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class ProductsExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{

    private $products;
    private $row = 0;

    public function __construct($products)
    {
        $this->products = $products;
    }

    public function headings(): array
    {
        return [
            '#',
            __('Product'),
            __('Sku'),
            __('Description'),
            __('Category'),
            __('Price'),
        ];
    }

    public function map($product): array
    {
        return [
            ++$this->row,
            $product->name,
            $product->sku,
            $product->description,
            $product->category?->name ?? __('No category'),
            number_format($product->price, 2)
        ];
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                if (app()->isLocale('ar')) {
                    $event->sheet->getDelegate()->setRightToLeft(true);
                }
            },
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->products;
    }
}
