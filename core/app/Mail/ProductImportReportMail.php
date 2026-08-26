<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProductImportReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reportData;

    public function __construct(array $reportData)
    {
        $this->reportData = $reportData;
    }

    public function build()
    {
        $status = $this->reportData['status'];

        if ($status === 'success') {
            $subject = 'Product Import Completed Successfully';
        } elseif ($status === 'partial') {
            $subject = 'Product Import Completed with Some Errors';
        } else {
            $subject = 'Product Import Failed';
        }

        $fromEmail = is_null(tenant())
            ? get_static_option_central('site_global_email')
            : get_static_option('tenant_site_global_email');

        $siteName = get_static_option('site_title') ?? config('app.name');

        return $this->from($fromEmail, $siteName)
            ->subject($subject)
            ->view('emails.product_import_report')
            ->with(['report' => $this->reportData]);
    }
}
