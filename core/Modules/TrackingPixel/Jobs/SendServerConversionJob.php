<?php

namespace Modules\TrackingPixel\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\TrackingPixel\Models\TrackingPixelEventLog;

class SendServerConversionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        private readonly mixed  $order,
        private readonly ?string $tenantId
    ) {}

    public function handle(): void
    {
        $plugin = app(\App\PluginSystem\PluginManager::class)->get('tracking-pixel');
        if (!$plugin) return;

        $order  = $this->order;
        $total  = (float) ($order->total_amount ?? $order->grand_total ?? 0);
        $email  = $order->user?->email ?? '';
        $phone  = $order->billing_phone ?? $order->user?->phone ?? '';
        $currency = strtoupper($order->currency_code ?? 'USD');
        $orderId  = (int) $order->id;
        $items    = $this->buildItemsPayload($order);

        // GA4 Measurement Protocol
        if ($plugin->get_option('ga4_server_enabled') && $plugin->get_option('ga4_measurement_id') && $plugin->get_option('ga4_server_api_secret')) {
            $this->sendGa4($plugin, $order, $total, $currency, $orderId, $items);
        }

        // Meta Conversions API
        if ($plugin->get_option('meta_server_enabled') && $plugin->get_option('meta_pixel_id') && $plugin->get_option('meta_access_token')) {
            $this->sendMeta($plugin, $order, $total, $currency, $orderId, $email, $phone);
        }

        // TikTok Events API
        if ($plugin->get_option('tiktok_server_enabled') && $plugin->get_option('tiktok_pixel_id') && $plugin->get_option('tiktok_access_token')) {
            $this->sendTikTok($plugin, $order, $total, $currency, $orderId, $email);
        }
    }

    private function sendGa4($plugin, $order, float $total, string $currency, int $orderId, array $items): void
    {
        $measurementId = $plugin->get_option('ga4_measurement_id');
        $apiSecret     = $plugin->get_option('ga4_server_api_secret');
        $eventId       = "ga4_purchase_{$orderId}";

        if ($this->alreadySent('ga4', $orderId, $eventId)) return;

        $payload = [
            'client_id' => 'server_' . $orderId,
            'events'    => [[
                'name'   => 'purchase',
                'params' => [
                    'transaction_id' => (string) $orderId,
                    'value'          => $total,
                    'currency'       => $currency,
                    'items'          => $items,
                ],
            ]],
        ];

        try {
            $response = Http::timeout(10)
                ->post("https://www.google-analytics.com/mp/collect?measurement_id={$measurementId}&api_secret={$apiSecret}", $payload);

            $this->log('ga4', 'Purchase', $orderId, $response->status(), $response->body(), $eventId);
        } catch (\Throwable $e) {
            Log::channel('single')->error('[TrackingPixel:ga4] ' . $e->getMessage());
        }
    }

    private function sendMeta($plugin, $order, float $total, string $currency, int $orderId, string $email, string $phone): void
    {
        $pixelId    = $plugin->get_option('meta_pixel_id');
        $token      = $plugin->get_option('meta_access_token');
        $testCode   = $plugin->get_option('meta_test_event_code');
        $eventId    = "meta_purchase_{$orderId}";

        if ($this->alreadySent('meta', $orderId, $eventId)) return;

        $userData = array_filter([
            'em'  => $email ? hash('sha256', strtolower(trim($email))) : null,
            'ph'  => $phone ? hash('sha256', preg_replace('/\D/', '', $phone)) : null,
            'client_ip_address' => request()->ip(),
            'client_user_agent' => request()->userAgent(),
        ]);

        $payload = [
            'data' => [[
                'event_name'       => 'Purchase',
                'event_time'       => time(),
                'event_id'         => $eventId,
                'action_source'    => 'website',
                'user_data'        => $userData,
                'custom_data'      => [
                    'value'    => $total,
                    'currency' => $currency,
                    'order_id' => $orderId,
                ],
            ]],
        ];

        if ($testCode) {
            $payload['test_event_code'] = $testCode;
        }

        try {
            $response = Http::timeout(10)
                ->post("https://graph.facebook.com/v18.0/{$pixelId}/events?access_token={$token}", $payload);

            $this->log('meta', 'Purchase', $orderId, $response->status(), $response->body(), $eventId);
        } catch (\Throwable $e) {
            Log::channel('single')->error('[TrackingPixel:meta] ' . $e->getMessage());
        }
    }

    private function sendTikTok($plugin, $order, float $total, string $currency, int $orderId, string $email): void
    {
        $pixelId  = $plugin->get_option('tiktok_pixel_id');
        $token    = $plugin->get_option('tiktok_access_token');
        $eventId  = "tiktok_purchase_{$orderId}";

        if ($this->alreadySent('tiktok', $orderId, $eventId)) return;

        $payload = [
            'pixel_code' => $pixelId,
            'event'      => 'PlaceAnOrder',
            'event_id'   => $eventId,
            'timestamp'  => date('c'),
            'context'    => [
                'user' => array_filter([
                    'email' => $email ? hash('sha256', strtolower(trim($email))) : null,
                    'ip'    => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]),
                'page' => ['url' => url()->current()],
            ],
            'properties' => [
                'currency'  => $currency,
                'value'     => $total,
                'order_id'  => $orderId,
            ],
        ];

        try {
            $response = Http::timeout(10)
                ->withToken($token)
                ->post('https://business-api.tiktok.com/open_api/v1.3/event/track/', $payload);

            $this->log('tiktok', 'PlaceAnOrder', $orderId, $response->status(), $response->body(), $eventId);
        } catch (\Throwable $e) {
            Log::channel('single')->error('[TrackingPixel:tiktok] ' . $e->getMessage());
        }
    }

    private function buildItemsPayload($order): array
    {
        $items = [];
        foreach ($order->items ?? [] as $item) {
            $items[] = [
                'item_id'   => (string) ($item->product_id ?? ''),
                'item_name' => $item->product?->name ?? $item->product_name ?? '',
                'price'     => (float) ($item->unit_price ?? $item->price ?? 0),
                'quantity'  => (int) ($item->quantity ?? 1),
            ];
        }
        return $items;
    }

    private function alreadySent(string $channel, int $orderId, string $eventId): bool
    {
        return TrackingPixelEventLog::where('channel', $channel)
            ->where('order_id', $orderId)
            ->where('event_id', $eventId)
            ->exists();
    }

    private function log(string $channel, string $eventType, int $orderId, int $status, string $response, string $eventId): void
    {
        // Keep only last 100 events per tenant
        $tenantId = $this->tenantId;
        $count = TrackingPixelEventLog::where('tenant_id', $tenantId)->count();
        if ($count >= 100) {
            $oldest = TrackingPixelEventLog::where('tenant_id', $tenantId)
                ->orderBy('fired_at')
                ->first();
            $oldest?->delete();
        }

        TrackingPixelEventLog::create([
            'tenant_id'        => $tenantId,
            'channel'          => $channel,
            'event_type'       => $eventType,
            'order_id'         => $orderId,
            'http_status'      => $status,
            'response_snippet' => substr($response, 0, 500),
            'event_id'         => $eventId,
            'fired_at'         => now(),
        ]);
    }
}
