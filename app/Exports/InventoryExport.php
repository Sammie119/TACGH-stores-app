<?php

namespace App\Exports;

use App\Models\BranchStock;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InventoryExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    public function __construct(protected array $filters = []) {}

    public function query()
    {
        $query = BranchStock::with(['product.category', 'branch'])
            ->join('products', 'branch_stock.product_id', '=', 'products.id')
            ->select('branch_stock.*')
            ->whereNull('products.deleted_at');

        if (!empty($this->filters['branch_id'])) {
            $query->where('branch_stock.branch_id', $this->filters['branch_id']);
        }
        if (!empty($this->filters['category_id'])) {
            $query->where('products.category_id', $this->filters['category_id']);
        }
        if (($this->filters['stock_status'] ?? null) === 'low') {
            $query->whereColumn('branch_stock.quantity', '<=', 'products.reorder_level')
                ->where('branch_stock.quantity', '>', 0);
        } elseif (($this->filters['stock_status'] ?? null) === 'out') {
            $query->where('branch_stock.quantity', '<=', 0);
        }

        return $query->orderBy('products.name');
    }

    public function headings(): array
    {
        return [
            'Product', 'SKU', 'Category', 'Branch',
            'Quantity', 'Unit', 'Reorder Level',
            'Cost Price', 'Selling Price',
            'Cost Value', 'Selling Value', 'Status',
        ];
    }

    public function map($item): array
    {
        $isLow = $item->quantity <= $item->product->reorder_level && $item->quantity > 0;
        $isOut = $item->quantity <= 0;

        return [
            $item->product->name,
            $item->product->sku,
            $item->product->category?->name,
            $item->branch->name,
            $item->quantity,
            $item->product->unit,
            $item->product->reorder_level,
            $item->product->cost_price,
            $item->product->selling_price,
            $item->quantity * $item->product->cost_price,
            $item->quantity * $item->product->selling_price,
            $isOut ? 'Out of stock' : ($isLow ? 'Low stock' : 'In stock'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
