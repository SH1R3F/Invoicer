<?php

namespace App\Exports;

use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class UsersExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{

    private $users;
    private $row = 0;

    public function __construct($users)
    {
        $this->users = $users;
    }

    public function headings(): array
    {
        return [
            '#',
            __('User'),
            __('Email'),
            __('Role'),
        ];
    }

    public function map($user): array
    {
        return [
            ++$this->row,
            $user->name,
            $user->email,
            $user->roles->first()?->name ?? '',
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
        return $this->users;
    }
}
