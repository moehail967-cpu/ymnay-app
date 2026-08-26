<?php

namespace Modules\ProductQA\Src;

use Illuminate\Support\Facades\DB;

class QAService
{
    private ?int $tenantId;

    public function __construct(?int $tenantId)
    {
        $this->tenantId = $tenantId;
    }

    private function query()
    {
        return DB::table('product_questions')->where('tenant_id', $this->tenantId);
    }

    public function getForProduct(int $productId): array
    {
        return $this->query()
            ->where('product_id', $productId)
            ->where('status', 'answered')
            ->orderByDesc('helpful_count')
            ->orderByDesc('answered_at')
            ->get()
            ->all();
    }

    public function getAll(int $perPage = 20)
    {
        return $this->query()
            ->orderByRaw("FIELD(status, 'pending', 'answered', 'rejected')")
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function getPendingCount(): int
    {
        return $this->query()->where('status', 'pending')->count();
    }

    public function find(int $id): ?object
    {
        return $this->query()->where('id', $id)->first();
    }

    public function submit(array $data): int
    {
        return DB::table('product_questions')->insertGetId([
            'tenant_id'  => $this->tenantId,
            'product_id' => $data['product_id'],
            'user_id'    => $data['user_id'] ?? null,
            'name'       => $data['name'],
            'email'      => $data['email'],
            'question'   => $data['question'],
            'status'     => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function answer(int $id, string $answer): void
    {
        $this->query()->where('id', $id)->update([
            'answer'      => $answer,
            'status'      => 'answered',
            'answered_at' => now(),
            'updated_at'  => now(),
        ]);
    }

    public function reject(int $id): void
    {
        $this->query()->where('id', $id)->update([
            'status'     => 'rejected',
            'updated_at' => now(),
        ]);
    }

    public function markHelpful(int $id): void
    {
        $this->query()->where('id', $id)->increment('helpful_count');
    }

    public function delete(int $id): void
    {
        $this->query()->where('id', $id)->delete();
    }

    public function getProductName(int $productId): string
    {
        return DB::table('products')->where('id', $productId)->value('name') ?? '—';
    }
}
