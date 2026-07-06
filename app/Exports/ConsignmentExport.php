<?php

namespace App\Exports;

use App\Models\Consignment;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ConsignmentExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    public function __construct(protected array $filters = []) {}

    public function query()
    {
        return Consignment::with(['branch', 'user', 'customer'])
            ->when(!empty($this->filters['branch_id']),
                fn($q) => $q->where('branch_id', $this->filters['branch_id']))
            ->when(!empty($this->filters['status']),
                fn($q) => $q->where('status', $this->filters['status']))
            ->when(!empty($this->filters['date_from']),
                fn($q) => $q->whereDate('created_at', '>=', $this->filters['date_from']))
            ->when(!empty($this->filters['date_to']),
                fn($q) => $q->whereDate('created_at', '<=', $this->filters['date_to']))
            ->latest();
    }

    public function headings(): array
    {
        return [
            'Reference', 'Branch', 'Recipient', 'Created By',
            'Total Value', 'Amount Paid', 'Balance Due', 'Status', 'Date',
        ];
    }

    public function map($consignment): array
    {
        return [
            $consignment->reference_no,
            $consignment->branch?->name,
            $consignment->customer?->name ?? $consignment->walkin_name ?? 'Walk-in',
            $consignment->user?->name,
            $consignment->total_value,
            $consignment->amount_paid,
            $consignment->balance_due,
            ucfirst($consignment->status),
            $consignment->created_at->format('d M Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
