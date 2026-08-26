<?php

namespace Modules\ProductQA\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\ProductQA\Src\QAService;

class QAController extends Controller
{
    private function tenantId(): ?int
    {
        try { return function_exists('tenant') && tenant() ? (int) tenant()->id : null; } catch (\Throwable) { return null; }
    }

    private function plugin(): mixed
    {
        return app(\App\PluginSystem\PluginManager::class)->get('product-qa');
    }

    public function index(Request $request)
    {
        $service      = new QAService($this->tenantId());
        $questions    = $service->getAll(25);
        $pendingCount = $service->getPendingCount();

        // Attach product names
        $productIds   = $questions->pluck('product_id')->unique()->all();
        $productNames = \DB::table('products')->whereIn('id', $productIds)->pluck('name', 'id');

        return view('product-qa::admin.index', compact('questions', 'pendingCount', 'productNames'));
    }

    public function answer(Request $request, int $id)
    {
        $request->validate(['answer' => 'required|string|max:2000']);

        $service = new QAService($this->tenantId());
        $service->answer($id, $request->input('answer'));

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', __('Answer published'));
    }

    public function reject(Request $request, int $id)
    {
        $service = new QAService($this->tenantId());
        $service->reject($id);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', __('Question rejected'));
    }

    public function destroy(Request $request, int $id)
    {
        $service = new QAService($this->tenantId());
        $service->delete($id);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', __('Question deleted'));
    }

    public function settings()
    {
        $plugin   = $this->plugin();
        $settings = [
            'enabled'       => $plugin->get_option('enabled', true),
            'require_login' => $plugin->get_option('require_login', false),
            'auto_approve'  => $plugin->get_option('auto_approve', false),
            'notify_admin'  => $plugin->get_option('notify_admin', true),
        ];

        return view('product-qa::admin.settings', compact('settings'));
    }

    public function saveSettings(Request $request)
    {
        $plugin = $this->plugin();
        $plugin->update_option('enabled',       (bool) $request->input('enabled', false));
        $plugin->update_option('require_login', (bool) $request->input('require_login', false));
        $plugin->update_option('auto_approve',  (bool) $request->input('auto_approve', false));
        $plugin->update_option('notify_admin',  (bool) $request->input('notify_admin', true));

        return redirect()->back()->with('success', __('Settings saved'));
    }
}
