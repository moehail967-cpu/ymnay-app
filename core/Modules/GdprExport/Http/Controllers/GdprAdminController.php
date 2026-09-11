<?php

namespace Modules\GdprExport\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class GdprAdminController extends Controller
{
    private function tenantId(): ?int
    {
        return function_exists('tenant') && tenant() ? (int) tenant()->id : null;
    }

    public function index()
    {
        $requests = DB::table('gdpr_export_requests')
            ->where('tenant_id', $this->tenantId())
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('gdpr-export::admin.index', compact('requests'));
    }

    public function updateStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,ready,downloaded,expired',
        ]);

        $updated = DB::table('gdpr_export_requests')
            ->where('id', $id)
            ->where('tenant_id', $this->tenantId())
            ->update([
                'status'   => $request->input('status'),
                'ready_at' => $request->input('status') === 'ready' ? now() : DB::raw('ready_at'),
            ]);

        if (!$updated) {
            return response()->json(['status' => 'error', 'message' => __('Request not found.')], 404);
        }

        return response()->json(['status' => 'success', 'message' => __('Status updated.')]);
    }
}
