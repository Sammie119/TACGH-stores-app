<?php

namespace App\Http\Controllers;

use App\Imports\ProductsImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ProductImportController extends Controller
{
    public function index()
    {
        return view('products.import');
    }

    public function preview(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        $path = $request->file('file')->store('imports/temp', 'local');

        $import = new ProductsImport(previewOnly: true);

        try {
            Excel::import($import, storage_path('app/' . $path));
        } catch (\Exception $e) {
            return back()->with('error',
                'Failed to read file: ' . $e->getMessage());
        }

        session([
            'import_file_path' => $path,
            'import_preview'   => [
                'imported'  => $import->imported,
                'errors'    => $import->errors,  // stored as 'errors' in session is fine
            ],
        ]);

        return redirect()->route('products.import.confirm');
    }

    public function confirm()
    {
        if (!session('import_preview')) {
            return redirect()->route('products.import')
                ->with('error', 'No import file found. Please upload again.');
        }

        $preview = session('import_preview');

        return view('products.import-confirm', [
            'imported' => $preview['imported'],
            'rowErrors' => $preview['errors'],   // ← renamed from 'errors'
            'filePath' => session('import_file_path'),
        ]);
    }

    public function process(Request $request)
    {
        $filePath = session('import_file_path');

        if (!$filePath) {
            return redirect()->route('products.import')
                ->with('error', 'Import session expired. Please upload again.');
        }

        $import = new ProductsImport(previewOnly: false);

        try {
            Excel::import($import, storage_path('app/' . $filePath));
        } catch (\Exception $e) {
            return redirect()->route('products.import')
                ->with('error', 'Import failed: ' . $e->getMessage());
        }

        // Clean up temp file
        \Illuminate\Support\Facades\Storage::disk('local')
            ->delete($filePath);

        session()->forget(['import_file_path', 'import_preview']);

        $created = collect($import->imported)->where('action', 'created')->count();
        $updated = collect($import->imported)->where('action', 'updated')->count();
        $errors  = count($import->errors);

        activity()
            ->causedBy(auth()->user())
            ->log("Bulk product import: {$created} created, {$updated} updated, {$errors} errors");

        return redirect()->route('products.index')
            ->with('success',
                "Import complete — {$created} products created, {$updated} updated."
                . ($errors > 0 ? " {$errors} rows had errors." : ''));
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="product-import-template.csv"',
        ];

        $columns = [
            'name', 'sku', 'category', 'description',
            'unit', 'cost_price', 'selling_price',
            'reorder_level', 'initial_stock', 'branch',
        ];

        $sampleRows = [
            [
                'Samsung Galaxy A54', 'ELEC-001', 'Electronics',
                'Samsung smartphone', 'piece', '700', '850',
                '5', '20', 'Stores HQ',
            ],
            [
                'USB-C Cable 2m', 'ACC-001', 'Accessories',
                'High-speed charging cable', 'piece', '15', '25',
                '20', '100', '',
            ],
        ];

        $callback = function () use ($columns, $sampleRows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns);
            foreach ($sampleRows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
