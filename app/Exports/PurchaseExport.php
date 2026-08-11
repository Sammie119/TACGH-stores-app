<?php

namespace App\Exports;

use App\Models\PurchaseOrder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PurchaseExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    public function __construct(protected array $filters = []) {}

    public function query()
    {
        return PurchaseOrder::with(['branch', 'supplier', 'createdBy'])
            ->when(!empty($this->filters['branch_id']),
                fn($q) => $q->where('branch_id', $this->filters['branch_id']))
            ->when(!empty($this->filters['supplier_id']),
                fn($q) => $q->where('supplier_id', $this->filters['supplier_id']))
            ->when(!empty($this->filters['status']),
                fn($q) => $q->where('status', $this->filters['status']))
            ->when(!empty($this->filters['date_from']),
                fn($q) => $q->whereDate('order_date', '>=', $this->filters['date_from']))
            ->when(!empty($this->filters['date_to']),
                fn($q) => $q->whereDate('order_date', '<=', $this->filters['date_to']))
            ->latest();
    }

    public function headings(): array
    {
        return [
            'PO Number', 'Branch', 'Supplier', 'Ordered By',
            'Total Amount', 'Amount Paid', 'Balance Due', 'Status', 'Order Date',
        ];
    }

    public function map($po): array
    {
        return [
            $po->po_number,
            $po->branch?->name,
            $po->supplier?->name,
            $po->createdBy?->name,
            $po->total_amount,
            $po->amount_paid,
            $po->balance_due,
            ucfirst($po->status),
            $po->order_date?->format('d M Y'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
