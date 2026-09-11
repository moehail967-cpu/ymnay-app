<?php

namespace App\Observers;

use App\Mail\BasicMail;
use App\Models\StockNotification;
use Illuminate\Support\Facades\Mail;
use Modules\Product\Entities\ProductInventory;

class StockNotificationObserver
{
    public function updated(ProductInventory $inventory): void
    {
        // Only fire when stock goes from 0 → positive (restock event)
        if ($inventory->isDirty('stock_count')
            && $inventory->stock_count > 0
            && $inventory->getOriginal('stock_count') <= 0
        ) {
            $this->notifySubscribers($inventory->product_id);
        }
    }

    private function notifySubscribers(int $product_id): void
    {
        $pending = StockNotification::where('product_id', $product_id)
            ->where('notified', false)
            ->get();

        foreach ($pending as $sub) {
            try {
                $product_name = $sub->product?->name ?? 'the product';
                $data = [
                    'subject' => __('Back in Stock: :name', ['name' => $product_name]),
                    'message' => __('Good news, :name! :product is back in stock and ready to order.', [
                        'name'    => $sub->name ?? 'there',
                        'product' => $product_name,
                    ]),
                ];
                Mail::to($sub->email)->send(new BasicMail($data));
                $sub->update(['notified' => true, 'notified_at' => now()]);
            } catch (\Throwable) {
                // don't fail the restock on email error
            }
        }
    }
}
