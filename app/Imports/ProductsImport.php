<?php

namespace App\Imports;

use App\Models\Branch;
use App\Models\BranchStock;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class ProductsImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    public array  $errors    = [];
    public array  $imported  = [];
    public int    $skipped   = 0;
    public bool   $previewOnly;

    public function __construct(bool $previewOnly = false)
    {
        $this->previewOnly = $previewOnly;
    }

    public function collection(Collection $rows)
    {
        $branches   = Branch::all()->keyBy('name');
        $categories = ProductCategory::all()->keyBy('name');
        $rowNumber  = 2; // starts at row 2 because row 1 is heading

        foreach ($rows as $row) {
            $data = $this->normalizeRow($row->toArray());

            // Validate row
            $validator = Validator::make($data, [
                'name'           => 'required|string|max:255',
                'sku'            => 'required|string|max:100',
                'category'       => 'nullable|string',
                'unit'           => 'required|string|max:50',
                'cost_price'     => 'required|numeric|min:0',
                'selling_price'  => 'required|numeric|min:0',
                'reorder_level'  => 'nullable|numeric|min:0',
                'initial_stock'  => 'nullable|numeric|min:0',
                'branch'         => 'nullable|string',
            ]);

            if ($validator->fails()) {
                $this->errors[] = [
                    'row'     => $rowNumber,
                    'name'    => $data['name'] ?? '—',
                    'sku'     => $data['sku']  ?? '—',
                    'errors'  => $validator->errors()->all(),
                ];
                $rowNumber++;
                continue;
            }

            // Resolve category
            $categoryId = null;
            if (!empty($data['category'])) {
                $cat = $categories->get($data['category'])
                    ?? ProductCategory::firstOrCreate(
                        ['name' => $data['category']],
                        ['is_active' => true]
                    );
                $categoryId = $cat->id;
                $categories->put($data['category'], $cat);
            }

            // Check for duplicate SKU
            $existing = Product::where('sku', $data['sku'])->first();

            if ($existing) {
                if (!$this->previewOnly) {
                    // Update existing product
                    $existing->update([
                        'name'          => $data['name'],
                        'category_id'   => $categoryId,
                        'unit'          => $data['unit'],
                        'cost_price'    => $data['cost_price'],
                        'selling_price' => $data['selling_price'],
                        'reorder_level' => $data['reorder_level'] ?? 10,
                    ]);
                }

                $this->imported[] = [
                    'row'    => $rowNumber,
                    'name'   => $data['name'],
                    'sku'    => $data['sku'],
                    'action' => 'updated',
                    'stock'  => $data['initial_stock'] ?? 0,
                    'branch' => $data['branch'] ?? null,
                ];
            } else {
                if (!$this->previewOnly) {
                    $product = Product::create([
                        'name'          => $data['name'],
                        'sku'           => $data['sku'],
                        'category_id'   => $categoryId,
                        'unit'          => $data['unit'],
                        'cost_price'    => $data['cost_price'],
                        'selling_price' => $data['selling_price'],
                        'reorder_level' => $data['reorder_level'] ?? 10,
                        'is_active'     => true,
                        'description'   => $data['description'] ?? null,
                    ]);

                    // Set initial stock
                    if (!empty($data['initial_stock']) && $data['initial_stock'] > 0) {
                        $branch = null;

                        if (!empty($data['branch'])) {
                            $branch = $branches->get($data['branch']);
                        }

                        // Default to all branches if none specified
                        $targetBranches = $branch
                            ? collect([$branch])
                            : Branch::where('is_active', true)->get();

                        foreach ($targetBranches as $b) {
                            BranchStock::updateOrCreate(
                                [
                                    'branch_id'  => $b->id,
                                    'product_id' => $product->id,
                                ],
                                [
                                    'quantity'     => $data['initial_stock'],
                                    'last_updated' => now(),
                                ]
                            );
                        }
                    } else {
                        // Create zero stock for all branches
                        foreach (Branch::where('is_active', true)->get() as $b) {
                            BranchStock::firstOrCreate(
                                [
                                    'branch_id'  => $b->id,
                                    'product_id' => $product->id,
                                ],
                                ['quantity' => 0, 'last_updated' => now()]
                            );
                        }
                    }
                }

                $this->imported[] = [
                    'row'    => $rowNumber,
                    'name'   => $data['name'],
                    'sku'    => $data['sku'],
                    'action' => 'created',
                    'stock'  => $data['initial_stock'] ?? 0,
                    'branch' => $data['branch'] ?? 'All branches',
                ];
            }

            $rowNumber++;
        }
    }

    private function normalizeRow(array $row): array
    {
        // Normalize keys — handle different header formats
        $normalized = [];
        foreach ($row as $key => $value) {
            $cleanKey = strtolower(trim(preg_replace('/[\s_]+/', '_', $key)));
            $normalized[$cleanKey] = is_string($value) ? trim($value) : $value;
        }

        // Map common aliases
        $aliases = [
            'product_name'   => 'name',
            'item_name'      => 'name',
            'product_sku'    => 'sku',
            'code'           => 'sku',
            'product_code'   => 'sku',
            'cat'            => 'category',
            'category_name'  => 'category',
            'uom'            => 'unit',
            'price'          => 'selling_price',
            'sale_price'     => 'selling_price',
            'cost'           => 'cost_price',
            'purchase_price' => 'cost_price',
            'reorder'        => 'reorder_level',
            'min_stock'      => 'reorder_level',
            'opening_stock'  => 'initial_stock',
            'qty'            => 'initial_stock',
            'quantity'       => 'initial_stock',
            'stock'          => 'initial_stock',
        ];

        foreach ($aliases as $alias => $canonical) {
            if (isset($normalized[$alias]) && !isset($normalized[$canonical])) {
                $normalized[$canonical] = $normalized[$alias];
            }
        }

        return $normalized;
    }
}
