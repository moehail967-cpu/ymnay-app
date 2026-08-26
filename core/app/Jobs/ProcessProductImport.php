<?php

namespace App\Jobs;

use App\Mail\ProductImportReportMail;
use App\Models\StaticOption;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Modules\Product\Entities\Product;
use App\Models\Tenant;
use App\Http\Services\HandleImageUploadService;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ProcessProductImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600;
    public $tries = 1;

    protected $filePath;
    protected $mapping;
    protected $userId;
    protected $tenantId;
    protected $userEmail;

    // Tracking counters
    protected $totalRows = 0;
    protected $successCount = 0;
    protected $failedCount = 0;
    protected $skippedCount = 0;
    protected $failureReasons = [];

    public function __construct($filePath, $mapping, $userId, $tenantId, $userEmail = null)
    {
        $this->filePath = $filePath;
        $this->mapping = $mapping;
        $this->userId = $userId;
        $this->tenantId = $tenantId;
        $this->userEmail = $userEmail;
    }

    public function handle()
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(0);

        Log::info("Product Import: Job started for tenant '{$this->tenantId}', user '{$this->userId}'");

        // Initialize tenant context
        try {
            $tenant = Tenant::find($this->tenantId);
            if (!$tenant) {
                $this->addFailure(0, "Tenant '{$this->tenantId}' not found");
                $this->finalizeImport("Import failed: Tenant not found");
                return;
            }
            tenancy()->initialize($tenant);
            Log::info("Product Import: Tenant context initialized for '{$this->tenantId}', DB: " . DB::connection()->getDatabaseName());
        } catch (\Exception $e) {
            Log::error("Product Import: Failed to initialize tenant context: " . $e->getMessage());
            $this->addFailure(0, "Could not initialize tenant - " . $e->getMessage());
            $this->finalizeImport("Import failed: Tenant initialization error");
            return;
        }

        // Load tenant SMTP config (middleware doesn't run in queue context)
        $this->configureTenantMail();

        // Resolve admin email if not passed
        if (empty($this->userEmail)) {
            try {
                $admin = DB::table('admins')->where('id', $this->userId)->first();
                $this->userEmail = $admin->email ?? null;
            } catch (\Exception $e) {
                Log::warning("Product Import: Could not resolve admin email: " . $e->getMessage());
            }
        }

        // Validate file exists
        $fullPath = Storage::disk("tenant{$this->tenantId}")->path($this->filePath);
        if (!file_exists($fullPath)) {
            $this->addFailure(0, "CSV file not found at expected location");
            $this->finalizeImport("Import failed: File not found");
            return;
        }

        // Detect delimiter and open file
        $delimiter = $this->detectDelimiter($fullPath);
        $handle = fopen($fullPath, "r");
        if (!$handle) {
            $this->addFailure(0, "Unable to open CSV file");
            $this->finalizeImport("Import failed: Unable to open file");
            return;
        }

        // Read and clean header row
        $header = fgetcsv($handle, 0, $delimiter);
        if (empty($header)) {
            fclose($handle);
            $this->addFailure(0, "CSV file is empty or has invalid headers");
            $this->finalizeImport("Import failed: Empty or invalid CSV");
            return;
        }

        $header = array_map(function ($value) {
            return preg_replace('/^[\x{200B}\x{FEFF}]/u', '', trim($value));
        }, $header);

        Log::info("Product Import: CSV headers detected", ['headers' => $header]);

        // Process each row
        $rowNumber = 1; // Start from 1 (after header)
        while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
            $rowNumber++;
            $this->totalRows++;

            try {
                // Skip completely empty rows
                if ($this->isEmptyRow($data)) {
                    $this->skippedCount++;
                    $this->addFailure($rowNumber, "Empty row - skipped");
                    continue;
                }

                // Handle column count mismatch
                if (count($data) !== count($header)) {
                    $this->failedCount++;
                    $this->addFailure($rowNumber, "Column count mismatch (expected " . count($header) . ", got " . count($data) . ")");
                    continue;
                }

                $row = array_combine($header, $data);
                $productData = $this->mapRow($row);

                if (!$productData) {
                    $this->skippedCount++;
                    $this->addFailure($rowNumber, "Missing required field: product name");
                    continue;
                }

                // Validate the mapped data
                $validation = $this->validateRow($productData, $rowNumber);
                if ($validation !== true) {
                    $this->failedCount++;
                    $this->addFailure($rowNumber, $validation);
                    continue;
                }

                // Attempt to create the product
                $result = $this->createProduct($productData, $rowNumber);
                if ($result === true) {
                    $this->successCount++;
                } else {
                    $this->failedCount++;
                    $this->addFailure($rowNumber, $result);
                }

                // Periodic memory cleanup
                if ($this->totalRows % 500 === 0) {
                    $this->updateProgress($this->totalRows);
                    gc_collect_cycles();
                    Log::info("Product Import: Progress - {$this->totalRows} rows processed, {$this->successCount} imported, {$this->failedCount} failed, {$this->skippedCount} skipped");
                }

            } catch (\Exception $e) {
                $this->failedCount++;
                $this->addFailure($rowNumber, "Unexpected error - " . $e->getMessage());
                Log::error("Product Import: Row {$rowNumber} exception: " . $e->getMessage());
            }
        }

        fclose($handle);

        // Clean up the uploaded file
        try {
            Storage::disk("tenant{$this->tenantId}")->delete($this->filePath);
        } catch (\Exception $e) {
            Log::warning("Product Import: Failed to delete temp file: " . $e->getMessage());
        }

        Log::info("Product Import: Completed", [
            'total' => $this->totalRows,
            'success' => $this->successCount,
            'failed' => $this->failedCount,
            'skipped' => $this->skippedCount,
        ]);

        $this->finalizeImport();
    }

    /**
     * Validate a single row of product data.
     */
    private function validateRow(array $data, int $rowNumber): bool|string
    {
        $rules = [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'description' => 'required|string|min:10',
            'sale_price' => 'required|numeric|min:0',
        ];

        $messages = [
            'name.required' => 'Product name is required',
            'name.max' => 'Product name must not exceed 255 characters',
            'slug.required' => 'Slug is required',
            'description.required' => 'Description is required',
            'description.min' => 'Description must be at least 10 characters',
            'sale_price.required' => 'Sale price is required',
            'sale_price.numeric' => 'Sale price must be a number',
            'sale_price.min' => 'Sale price must be 0 or greater',
        ];

        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return implode('; ', $errors);
        }

        return true;
    }

    /**
     * Map CSV row to product data using the user-defined field mapping.
     */
    private function mapRow(array $row): ?array
    {
        $productData = [];

        foreach ($this->mapping as $dbField => $csvField) {
            if (empty($csvField) || !isset($row[$csvField])) {
                continue;
            }

            $value = $row[$csvField];
            if ($value === '' || $value === null) {
                continue;
            }

            if (in_array($dbField, ['price', 'sale_price', 'cost'])) {
                $productData[$dbField] = floatval($value);
            } elseif (in_array($dbField, ['badge_id', 'brand_id', 'status_id', 'product_type', 'sold_count', 'min_purchase', 'max_purchase'])) {
                $productData[$dbField] = $value !== '' ? intval($value) : null;
            } elseif (in_array($dbField, ['is_refundable', 'is_in_house', 'is_inventory_warn_able'])) {
                $productData[$dbField] = intval($value);
            } else {
                $productData[$dbField] = trim($value);
            }
        }

        if (empty($productData['name'])) {
            return null;
        }

        // Set defaults
        $productData['status_id'] = $productData['status_id'] ?? 1;
        $productData['is_in_house'] = $productData['is_in_house'] ?? 1;

        // Generate slug
        $productData['slug'] = !empty($productData['slug'])
            ? Str::slug($productData['slug'])
            : Str::slug($productData['name']);

        return $productData;
    }

    /**
     * Create a product record with inventory, slug, and UOM inside a transaction.
     */
    private function createProduct(array $productData, int $rowNumber): bool|string
    {
        try {
            $currentDb = DB::connection()->getDatabaseName();

            // Check for duplicate by name
            if (Product::where('name', $productData['name'])->exists()) {
                return "Product '{$productData['name']}' already exists - skipped";
            }

            // Check for duplicate slug
            $slug = $productData['slug'];
            if (DB::table('slugs')->where('slug', $slug)->exists()) {
                $slug = $slug . '-' . Str::random(4);
            }

            $stockCount = $productData['stock_count'] ?? null;
            $sku = $productData['sku'] ?? null;
            $uom = $productData['uom'] ?? null;
            $imageUrl = $productData['image_url'] ?? null;

            unset($productData['stock_count'], $productData['sku'], $productData['uom'], $productData['image_url']);

            // Download image before transaction (external HTTP call shouldn't be in transaction)
            if (!empty($imageUrl)) {
                $mediaId = $this->downloadAndStoreImage($imageUrl);
                if ($mediaId) {
                    $productData['image_id'] = $mediaId;
                } else {
                    Log::warning("Product Import: Row {$rowNumber} - Image download failed for '{$imageUrl}', continuing without image");
                }
            }

            DB::transaction(function () use ($productData, $slug, $stockCount, $sku, $uom) {
                $product = Product::create($productData);

                DB::table('slugs')->insert([
                    'morphable_type' => Product::class,
                    'morphable_id' => $product->id,
                    'slug' => $slug,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                if ($stockCount !== null || $sku !== null) {
                    DB::table('product_inventories')->insert([
                        'product_id' => $product->id,
                        'sku' => $sku ?? Str::upper(Str::random(8)),
                        'stock_count' => $stockCount ?? 0,
                    ]);
                }

                $unitId = null;
                if ($uom) {
                    $unitId = DB::table('units')->where('name', $uom)->value('id');
                }
                if (!$unitId) {
                    $unitId = DB::table('units')->value('id');
                }
                if ($unitId) {
                    DB::table('product_uom')->insert([
                        'product_id' => $product->id,
                        'unit_id' => $unitId,
                        'quantity' => 1,
                    ]);
                }
            });

            Log::info("Product Import: Row {$rowNumber} - '{$productData['name']}' created in DB '{$currentDb}'");
            return true;

        } catch (\Exception $e) {
            Log::error("Product Import: Row {$rowNumber} - Creation failed for '{$productData['name']}': " . $e->getMessage());
            return "Database error - " . $e->getMessage();
        }
    }

    /**
     * Download an image from URL and store it using the media upload service.
     */
    private function downloadAndStoreImage(string $url): ?int
    {
        try {
            if (!filter_var($url, FILTER_VALIDATE_URL) || !preg_match('/^https?:\/\//i', $url)) {
                Log::warning("Product Import: Invalid image URL: {$url}");
                return null;
            }

            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS => 5,
                CURLOPT_TIMEOUT => 60,
                CURLOPT_CONNECTTIMEOUT => 15,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                CURLOPT_HTTPHEADER => [
                    'Accept: image/webp,image/apng,image/*,*/*;q=0.8',
                ],
            ]);
            $imageContent = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($imageContent === false || $httpCode !== 200) {
                Log::warning("Product Import: Image download failed from '{$url}' (HTTP {$httpCode}, {$curlError})");
                return null;
            }

            $pathInfo = pathinfo(parse_url($url, PHP_URL_PATH));
            $extension = $pathInfo['extension'] ?? 'jpg';
            $extension = preg_replace('/[^a-zA-Z0-9]/', '', $extension);
            if (!in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $extension = 'jpg';
            }

            $tempDir = 'assets/uploads/temp-images';
            if (!\File::exists($tempDir)) {
                @mkdir($tempDir, 0777, true);
            }

            $uniqueFilename = uniqid('import-') . '.' . $extension;
            $storePath = $tempDir . '/' . $uniqueFilename;

            file_put_contents($storePath, $imageContent);

            if (!file_exists($storePath)) {
                Log::warning("Product Import: Failed to save temp image: {$storePath}");
                return null;
            }

            $imageObject = new UploadedFile($storePath, $uniqueFilename);
            $folderPath = global_assets_path('assets/tenant/uploads/media-uploader/' . $this->tenantId . '/');

            if (!\Auth::guard('admin')->check()) {
                \Auth::guard('admin')->loginUsingId($this->userId);
            }

            $request = (object)[
                'file' => $imageObject,
                'user_type' => 'admin',
            ];

            $mediaId = HandleImageUploadService::handle_image_upload(
                $uniqueFilename,
                $imageObject,
                $uniqueFilename,
                $folderPath,
                $request,
                true,
                $tempDir . '/'
            );

            @unlink($storePath);

            return $mediaId ?: null;

        } catch (\Exception $e) {
            Log::error("Product Import: Image download error for '{$url}': " . $e->getMessage());
            return null;
        }
    }

    /**
     * Check if a CSV row is completely empty.
     */
    private function isEmptyRow(array $data): bool
    {
        foreach ($data as $value) {
            if ($value !== '' && $value !== null) {
                return false;
            }
        }
        return true;
    }

    /**
     * Track a failure reason with row number.
     */
    private function addFailure(int $rowNumber, string $reason): void
    {
        $this->failureReasons[] = [
            'row' => $rowNumber,
            'reason' => $reason,
        ];
    }

    /**
     * Detect CSV delimiter by analyzing the first line.
     */
    private function detectDelimiter(string $file): string
    {
        if (!file_exists($file) || !is_file($file)) {
            return ',';
        }

        $handle = fopen($file, "r");
        if (!$handle) {
            return ',';
        }

        $line = fgets($handle);
        fclose($handle);

        if (empty($line)) {
            return ',';
        }

        $delimiters = [",", ";", "\t", "|"];
        $results = [];
        foreach ($delimiters as $delimiter) {
            $results[$delimiter] = count(str_getcsv($line, $delimiter));
        }

        return array_search(max($results), $results);
    }

    /**
     * Update progress in cache for live monitoring.
     */
    private function updateProgress(int $count): void
    {
        cache()->put("import_progress_{$this->userId}_{$this->tenantId}", [
            'processed' => $count,
            'imported' => $this->successCount,
            'failed' => $this->failedCount,
            'skipped' => $this->skippedCount,
        ], 3600);
    }

    /**
     * Finalize the import: store results in cache and send email notification.
     */
    private function finalizeImport(?string $criticalError = null): void
    {
        // Determine status
        if ($criticalError) {
            $status = 'error';
            $message = $criticalError;
        } elseif ($this->successCount > 0 && $this->failedCount === 0 && $this->skippedCount === 0) {
            $status = 'success';
            $message = "All {$this->successCount} product(s) imported successfully.";
        } elseif ($this->successCount > 0) {
            $status = 'partial';
            $message = "{$this->successCount} product(s) imported successfully. {$this->failedCount} failed, {$this->skippedCount} skipped.";
        } else {
            $status = 'failed';
            $message = "Import failed. {$this->failedCount} product(s) failed, {$this->skippedCount} skipped. No products were imported.";
        }

        // Build report data
        $reportData = [
            'status' => $status,
            'message' => $message,
            'total_rows' => $this->totalRows,
            'imported' => $this->successCount,
            'failed' => $this->failedCount,
            'skipped' => $this->skippedCount,
            'reasons' => $this->failureReasons,
        ];

        Log::info("Product Import: Final Report", $reportData);

        // Store in cache for UI display (backward compatible)
        // Format reasons as simple strings for cache (UI compatibility)
        $cacheReasons = array_map(function ($item) {
            return "Row {$item['row']}: {$item['reason']}";
        }, $this->failureReasons);

        cache()->put("import_result_{$this->userId}_{$this->tenantId}", [
            'status' => $status,
            'message' => $message,
            'imported' => $this->successCount,
            'skipped' => $this->failedCount + $this->skippedCount,
            'reasons' => $cacheReasons,
        ], 3600);

        // Send email notification
        $this->sendEmailReport($reportData);
    }

    /**
     * Configure tenant SMTP mail settings from static_options table.
     * Replicates TenantConfigMiddleware logic since middleware doesn't run in queue context.
     */
    private function configureTenantMail(): void
    {
        try {
            $smtpSettings = StaticOption::select(['option_name', 'option_value'])
                ->whereIn('option_name', [
                    'site_smtp_driver',
                    'site_smtp_host',
                    'site_smtp_port',
                    'site_smtp_username',
                    'site_smtp_password',
                    'site_smtp_encryption',
                    'site_global_email',
                ])->get()->pluck('option_value', 'option_name')->toArray();

            if (empty($smtpSettings)) {
                Log::warning("Product Import: No tenant SMTP settings found, using default mail config");
                return;
            }

            $mailer = $smtpSettings['site_smtp_driver'] ?? 'smtp';

            Config::set([
                "mail.mailers.{$mailer}.transport" => $smtpSettings['site_smtp_driver'] ?? Config::get('mail.mailers.smtp.transport'),
                "mail.mailers.{$mailer}.host" => $smtpSettings['site_smtp_host'] ?? Config::get('mail.mailers.smtp.host'),
                "mail.mailers.{$mailer}.port" => $smtpSettings['site_smtp_port'] ?? Config::get('mail.mailers.smtp.port'),
                "mail.mailers.{$mailer}.username" => $smtpSettings['site_smtp_username'] ?? Config::get('mail.mailers.smtp.username'),
                "mail.mailers.{$mailer}.password" => $smtpSettings['site_smtp_password'] ?? Config::get('mail.mailers.smtp.password'),
                "mail.mailers.{$mailer}.encryption" => $smtpSettings['site_smtp_encryption'] ?? Config::get('mail.mailers.smtp.encryption'),
                "mail.mailers.{$mailer}.timeout" => null,
                "mail.mailers.{$mailer}.auth_mode" => null,
                "mail.mailers.{$mailer}.verify_peer" => false,
                "mail.mailers.{$mailer}.verify_peer_name" => false,
                "mail.mailers.{$mailer}.allow_self_signed" => true,
                "mail.from.address" => $smtpSettings['site_global_email'] ?? Config::get('mail.from.address'),
            ]);

            // Purge cached mailer so Laravel rebuilds it with new config
            Mail::purge($mailer);

            Log::info("Product Import: Tenant SMTP configured - host: {$smtpSettings['site_smtp_host']}, port: {$smtpSettings['site_smtp_port']}, from: {$smtpSettings['site_global_email']}");

        } catch (\Exception $e) {
            Log::error("Product Import: Failed to configure tenant SMTP: " . $e->getMessage());
        }
    }

    /**
     * Send email report to the admin who initiated the import.
     */
    private function sendEmailReport(array $reportData): void
    {
        if (empty($this->userEmail)) {
            Log::warning("Product Import: No email address available for user {$this->userId}, skipping email notification");
            return;
        }

        try {
            Mail::to($this->userEmail)->send(new ProductImportReportMail($reportData));
            Log::info("Product Import: Email report sent to {$this->userEmail}");
        } catch (\Exception $e) {
            Log::error("Product Import: Failed to send email report to {$this->userEmail}: " . $e->getMessage());
        }
    }

    /**
     * Handle job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Product Import: Job completely failed: " . $exception->getMessage(), [
            'tenant' => $this->tenantId,
            'user' => $this->userId,
            'file' => $this->filePath,
        ]);

        $reportData = [
            'status' => 'error',
            'message' => 'The import job failed unexpectedly. Please try again or contact support.',
            'total_rows' => $this->totalRows,
            'imported' => $this->successCount,
            'failed' => $this->failedCount,
            'skipped' => $this->skippedCount,
            'reasons' => [['row' => 0, 'reason' => 'Job failed: ' . $exception->getMessage()]],
        ];

        cache()->put("import_result_{$this->userId}_{$this->tenantId}", [
            'status' => 'error',
            'message' => 'Import job failed unexpectedly: ' . $exception->getMessage(),
            'imported' => 0,
            'skipped' => 0,
            'reasons' => [],
        ], 3600);

        if (!empty($this->userEmail)) {
            try {
                Mail::to($this->userEmail)->send(new ProductImportReportMail($reportData));
            } catch (\Exception $e) {
                Log::error("Product Import: Failed to send failure email: " . $e->getMessage());
            }
        }
    }
}
