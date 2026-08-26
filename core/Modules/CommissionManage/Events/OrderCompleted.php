<?php

namespace Modules\CommissionManage\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class OrderCompleted
{
    use Dispatchable, SerializesModels;

    public $productOrder;
    public $tenant;

//    public function __construct(OrderProducts $productOrder, $tenant)
    public function __construct(int $productOrder, $tenant)
    {
        $this->productOrder = $productOrder;
        $this->tenant = $tenant;

        //  Fixed: $productOrder is already the ID
        Log::info('Event received data:', [
            'order_id' => $productOrder,
            'tenant_id' => $tenant->id ?? null,
        ]);
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
