<?php
namespace Modules\Product\Http\Controllers;

use App\Jobs\ProcessProductImport;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Modules\Product\Entities\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Response;

class ProductImportController extends Controller
{
    public function index()
    {
        $tenantId = tenant()->id;
        $userId = auth('admin')->id();
        $importResult = cache()->get("import_result_{$userId}_{$tenantId}");
        cache()->forget("import_result_{$userId}_{$tenantId}"); // Clear cache after retrieval
        return view('product::import', compact('importResult'));
    }

    // Rest of the controller remains unchanged
    public function preview(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt'
        ]);

        $file = $request->file('csv_file');

        if (!$file) {
            return back()->with('error', 'No file uploaded!');
        }

        $tenantId = tenant()->id;
        $tenantStoragePath = storage_path("tenant{$tenantId}/app/temp_imports");
        if (!file_exists($tenantStoragePath)) {
            if (!mkdir($tenantStoragePath, 0755, true) && !is_dir($tenantStoragePath)) {
                Log::error("Failed to create directory: {$tenantStoragePath}");
                return back()->with('error', 'Failed to create tenant storage directory.');
            }
            Log::info("Created tenant storage directory: {$tenantStoragePath}");
        }

        $filename = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('temp_imports', $filename, "tenant{$tenantId}");
        $fullPath = Storage::disk("tenant{$tenantId}")->path($filePath);

        Log::info("Preview: File stored at {$fullPath} (Exists: " . (file_exists($fullPath) ? 'Yes' : 'No') . ")");

        if (!file_exists($fullPath)) {
            return back()->with('error', 'File upload failed.');
        }

        $delimiter = $this->detectDelimiter($fullPath);

        $handle = fopen($fullPath, "r");
        if (!$handle) {
            return back()->with('error', 'Unable to open the CSV file.');
        }

        $header = fgetcsv($handle, 10000, $delimiter);
        $header = array_map(function ($value) {
            return preg_replace('/^[\x{200B}\x{FEFF}]/u', '', $value);
        }, $header);

        if (empty($header)) {
            fclose($handle);
            return back()->with('error', 'CSV file appears to be empty or invalid.');
        }

        $rows = [];
        $count = 0;
        while (($data = fgetcsv($handle, 10000, $delimiter)) !== FALSE && $count < 5) {
            $rows[] = $data;
            $count++;
        }
        fclose($handle);

        $dbFields = [
            'name' => 'Product Name (Required)',
            'slug' => 'Slug (Required)',
            'summary' => 'Summary',
            'description' => 'Description (Required)',
            'status_id' => 'Status ID ',
            'sale_price' => 'Sale Price',
            'sku' => 'SKU ',
            'stock_count' => 'Stock count',
            'uom' => 'UOM ',
            'image_url' => 'Image URL',
        ];

        return view('product::mapping', compact('header', 'rows', 'dbFields', 'filePath'));
    }

    public function import(Request $request)
    {
        $mapping = $request->input('mapping');
        $filePath = $request->input('csv_file');

        if (empty($filePath)) {
            return redirect()->route('tenant.products.import')
                ->with('error', 'No file specified!');
        }

        $tenantId = tenant()->id;
        $fullPath = Storage::disk("tenant{$tenantId}")->path($filePath);
        Log::info("Import: Dispatching job with file {$fullPath} (Exists: " . (file_exists($fullPath) ? 'Yes' : 'No') . ")");

        if (!file_exists($fullPath)) {
            return redirect()->route('tenant.products.import')
                ->with('error', 'File not found before processing!');
        }

        $admin = auth('admin')->user();
        ProcessProductImport::dispatch($filePath, $mapping, $admin->id, $tenantId, $admin->email);

        return redirect()->route('tenant.products.import')
            ->with('success', 'Product import started in background. You will receive an email report when it completes.');
    }

    private function detectDelimiter($file)
    {
        if (!file_exists($file) || !is_file($file)) {
            return ',';
        }

        $delimiters = [",", ";", "\t", "|"];
        $results = [];

        $handle = fopen($file, "r");
        if (!$handle) {
            return ',';
        }

        $line = fgets($handle);
        fclose($handle);

        if (empty($line)) {
            return ',';
        }

        foreach ($delimiters as $delimiter) {
            $results[$delimiter] = count(str_getcsv($line, $delimiter));
        }

        return array_search(max($results), $results);
    }

    public function downloadSampleCSV()
    {
        try {
            $sampleData = [$this->getDefaultSampleRow(1), $this->getDefaultSampleRow(2), $this->getDefaultSampleRow(3)];

            if (empty($sampleData[0])) {
                $products = Product::latest()->take(3)->get();
                $sampleData = $products->map(function ($product) {
                    return $this->productToCSVRow($product);
                })->toArray();
            }

            $filename = 'product_import_sample_' . date('Y-m-d') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ];

            $callback = function () use ($sampleData) {
                $file = fopen('php://output', 'w');
                if (!empty($sampleData)) {
                    fputcsv($file, array_keys($sampleData[0]));
                }
                foreach ($sampleData as $row) {
                    fputcsv($file, $row);
                }
                fclose($file);
            };

            return Response::stream($callback, 200, $headers);
        } catch (\Exception $e) {
            Log::error('Sample CSV Download Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to download sample CSV: ' . $e->getMessage());
        }
    }

    public function downloadEmptyTemplate()
    {
        try {
            $headers = $this->getCSVHeaders();
            $filename = 'product_import_template_' . date('Y-m-d') . '.csv';
            $responseHeaders = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ];

            $callback = function () use ($headers) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $headers);
                fclose($file);
            };

            return Response::stream($callback, 200, $responseHeaders);
        } catch (\Exception $e) {
            Log::error('Empty Template Download Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to download template: ' . $e->getMessage());
        }
    }

    private function productToCSVRow($product)
    {
        return [
            'name' => $product->name ?? '',
            'slug' => $product->slug ?? '',
            'summary' => $product->summary ?? '',
            'description' => $product->description ?? '',
            'status_id' => $product->status_id ?? '',
            'sale_price' => $product->sale_price ?? '',
            'sku' => $product->sku ?? '',
            'stock_count' => $product->stock_count ?? '',
            'uom' => $product->uom?->uom_details?->name ?? '',
            'image_url' => '',
        ];
    }

    private function getDefaultSampleRow($index)
    {
//        $samples = [
//            1 => [
//                'name' => 'Classic Leather Backpack',
//                'slug' => 'classic-leather-backpack',
//                'summary' => 'Premium leather backpack perfect for everyday use.',
//                'description' => '<ul><li>Genuine leather</li><li>Padded straps</li><li>Multiple compartments</li><li>Durable construction</li><li>Suitable for travel & work</li></ul>',
//                'status_id' => '1',
//                'sale_price' => '1250',
//                'sku' => 'BACKPACK-001',
//                'stock_count' => '80',
//                'uom' => 'piece',
//                'image_url' => 'https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f',
//            ],
//            2 => [
//                'name' => 'Stylish White Sneakers',
//                'slug' => 'stylish-white-sneakers',
//                'summary' => 'Comfortable white sneakers great for all occasions.',
//                'description' => '<ul><li>Canvas upper</li><li>Rubber sole</li><li>Lace-up closure</li><li>Cushioned footbed</li><li>Breathable design</li></ul>',
//                'status_id' => '1',
//                'sale_price' => '980',
//                'sku' => 'SNEAKERS-002',
//                'stock_count' => '120',
//                'uom' => 'piece',
//                'image_url' => 'https://images.unsplash.com/photo-1512436991641-6745cdb1723f',
//            ],
//            3 => [
//                'name' => 'Minimalist Wrist Watch',
//                'slug' => 'minimalist-wrist-watch',
//                'summary' => 'Elegant wrist watch with minimalist design.',
//                'description' => '<ul><li>Stainless steel case</li><li>Leather strap</li><li>Quartz movement</li><li>Water resistant</li><li>Sleek dial</li></ul>',
//                'status_id' => '1',
//                'sale_price' => '1550',
//                'sku' => 'WATCH-003',
//                'stock_count' => '60',
//                'uom' => 'piece',
//                'image_url' => 'https://images.unsplash.com/photo-1529626455594-4ff0802cfb7e',
//            ],
//        ];

        $samples = [
            1 => [
                'name' => 'Classic Leather Backpack',
                'slug' => 'classic-leather-backpack',
                'summary' => 'Premium leather backpack perfect for everyday use.',
                'description' => '<ul><li>Genuine leather</li><li>Padded straps</li><li>Multiple compartments</li><li>Durable construction</li><li>Suitable for travel & work</li></ul>',
                'status_id' => '1',
                'sale_price' => '1250',
                'sku' => 'BACKPACK-001',
                'stock_count' => '80',
                'uom' => 'piece',
                'image_url' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62',
            ],
            2 => [
                'name' => 'Stylish White Sneakers',
                'slug' => 'stylish-white-sneakers',
                'summary' => 'Comfortable white sneakers great for all occasions.',
                'description' => '<ul><li>Canvas upper</li><li>Rubber sole</li><li>Lace-up closure</li><li>Cushioned footbed</li><li>Breathable design</li></ul>',
                'status_id' => '1',
                'sale_price' => '980',
                'sku' => 'SNEAKERS-002',
                'stock_count' => '120',
                'uom' => 'piece',
                'image_url' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff',
            ],
            3 => [
                'name' => 'Minimalist Wrist Watch',
                'slug' => 'minimalist-wrist-watch',
                'summary' => 'Elegant wrist watch with minimalist design.',
                'description' => '<ul><li>Stainless steel case</li><li>Leather strap</li><li>Quartz movement</li><li>Water resistant</li><li>Sleek dial</li></ul>',
                'status_id' => '1',
                'sale_price' => '1550',
                'sku' => 'WATCH-003',
                'stock_count' => '60',
                'uom' => 'piece',
                'image_url' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30',
            ],
        ];
        return $samples[$index];
    }

    private function getCSVHeaders()
    {
        return [
            'name',
            'slug',
            'summary',
            'description',
            'status_id',
            'sale_price',
            'sku',
            'stock_count',
            'uom',
            'image_url',
        ];
    }
}
