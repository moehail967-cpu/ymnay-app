<?php

namespace Modules\GdprExport\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GdprFrontendController extends Controller
{
    public function index()
    {
        $userId   = auth()->id();
        $tenantId = $this->tenantId();

        $requests = DB::table('gdpr_export_requests')
            ->where('user_id', $userId)
            ->where('tenant_id', $tenantId)
            ->orderByDesc('created_at')
            ->get();

        return view('gdpr-export::frontend.my-data', compact('requests'));
    }

    public function request(Request $request)
    {
        $userId   = auth()->id();
        $tenantId = $this->tenantId();

        $existing = DB::table('gdpr_export_requests')
            ->where('user_id', $userId)
            ->where('tenant_id', $tenantId)
            ->whereIn('status', ['pending', 'processing'])
            ->exists();

        if ($existing) {
            return back()->with('error', __('You already have a pending export request.'));
        }

        DB::table('gdpr_export_requests')->insert([
            'user_id'    => $userId,
            'tenant_id'  => $tenantId,
            'status'     => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->processLatestRequest($userId, $tenantId);

        return back()->with('success', __('Your data export is ready. Download it below.'));
    }

    public function download(string $token)
    {
        $req = DB::table('gdpr_export_requests')
            ->where('download_token', $token)
            ->where('user_id', auth()->id())
            ->where('status', 'ready')
            ->first();

        if (!$req || ($req->expires_at && now()->isAfter($req->expires_at))) {
            abort(404, __('Download link expired or not found.'));
        }

        if (!$req->file_path || !Storage::exists($req->file_path)) {
            abort(404, __('Export file no longer exists. Please request a new export.'));
        }

        DB::table('gdpr_export_requests')->where('id', $req->id)->update(['status' => 'downloaded']);

        return Storage::download($req->file_path, 'my-data-export.json');
    }

    private function processLatestRequest(int $userId, ?int $tenantId): void
    {
        $req = DB::table('gdpr_export_requests')
            ->where('user_id', $userId)
            ->where('tenant_id', $tenantId)
            ->where('status', 'pending')
            ->latest()
            ->first();

        if (!$req) return;

        DB::table('gdpr_export_requests')->where('id', $req->id)->update(['status' => 'processing', 'updated_at' => now()]);

        try {
            $user   = DB::table('users')->where('id', $userId)->first();
            $orders = DB::table('tbl_orders')
                ->where('user_id', $userId)
                ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
                ->get();

            $json     = json_encode(['profile' => (array) $user, 'orders' => $orders->toArray()], JSON_PRETTY_PRINT);
            $filename = "gdpr-exports/{$tenantId}/user-{$userId}-" . now()->format('Ymd-His') . '.json';

            Storage::put($filename, $json);

            DB::table('gdpr_export_requests')->where('id', $req->id)->update([
                'status'         => 'ready',
                'file_path'      => $filename,
                'download_token' => Str::random(64),
                'ready_at'       => now(),
                'expires_at'     => now()->addHours(48),
                'updated_at'     => now(),
            ]);
        } catch (\Throwable $e) {
            DB::table('gdpr_export_requests')->where('id', $req->id)->update(['status' => 'pending', 'updated_at' => now()]);
        }
    }

    private function tenantId(): ?int
    {
        try {
            return function_exists('tenant') && tenant() ? (int) tenant()->id : null;
        } catch (\Throwable) {
            return null;
        }
    }
}
