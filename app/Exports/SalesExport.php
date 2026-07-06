<?php

namespace App\Exports;

use App\Models\Sale;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    public function __construct(protected array $filters = []) {}

    public function query()
    {
        $query = Sale::with(['branch', 'user', 'customer'])
            ->where('status', 'completed');

        if (!empty($this->filters['branch_id'])) {
            $query->where('branch_id', $this->filters['branch_id']);
        }
        if (!empty($this->filters['date_from'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }
        if (!empty($this->filters['date_to'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }
        if (!empty($this->filters['user_id'])) {
            $query->where('user_id', $this->filters['user_id']);
        }

        return $query->latest();
    }

    public function headings(): array
    {
        return [
            'Invoice No', 'Branch', 'Customer', 'Cashier',
            'Total Amount', 'Discount', 'Amount Paid',
            'Balance Due', 'Payment Method', 'Status', 'Date',
        ];
    }

    public function map($sale): array
    {
        return [
            $sale->invoice_no,
            $sale->branch?->name,
            $sale->customer?->name ?? 'Walk-in',
            $sale->user?->name,
            $sale->total_amount,
            $sale->discount,
            $sale->amount_paid,
            $sale->balance_due,
            ucfirst($sale->payment_method),
            ucfirst($sale->status),
            $sale->created_at->format('d M Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
