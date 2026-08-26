<?php

namespace Modules\ProductQA\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\ProductQA\Src\QAService;

class QAFrontendController extends Controller
{
    private function tenantId(): ?int
    {
        try { return function_exists('tenant') && tenant() ? (int) tenant()->id : null; } catch (\Throwable) { return null; }
    }

    private function plugin(): mixed
    {
        return app(\App\PluginSystem\PluginManager::class)->get('product-qa');
    }

    public function submit(Request $request)
    {
        $plugin = $this->plugin();

        if ($plugin->get_option('require_login', false) && ! auth('web')->check()) {
            return response()->json(['status' => 'error', 'msg' => __('Please log in to ask a question.')], 401);
        }

        $request->validate([
            'product_id' => 'required|integer',
            'name'       => 'required|string|max:120',
            'email'      => 'required|email|max:120',
            'question'   => 'required|string|max:1000',
        ]);

        $service = new QAService($this->tenantId());

        $data = [
            'product_id' => $request->input('product_id'),
            'user_id'    => auth('web')->id(),
            'name'       => $request->input('name'),
            'email'      => $request->input('email'),
            'question'   => $request->input('question'),
        ];

        $id = $service->submit($data);

        // Auto-approve if configured
        if ($plugin->get_option('auto_approve', false)) {
            $service->answer($id, __('(Auto-approved — awaiting admin answer)'));
        }

        return response()->json([
            'status' => 'success',
            'msg'    => __('Your question has been submitted and will appear once answered.'),
        ]);
    }

    public function helpful(Request $request)
    {
        $request->validate(['id' => 'required|integer']);

        $service = new QAService($this->tenantId());
        $q       = $service->find($request->input('id'));

        if (! $q || $q->status !== 'answered') {
            return response()->json(['status' => 'error'], 404);
        }

        $service->markHelpful($q->id);

        return response()->json(['status' => 'success', 'count' => $q->helpful_count + 1]);
    }
}
