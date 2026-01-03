<?php

namespace App\Http\Resources;

use App\Enums\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'subtotal' => (string) $this->subtotal,
            'tax' => (string) $this->tax,
            'total' => (string) $this->total,
            'status' => $this->status instanceof OrderStatus ? $this->status->value : (string) $this->status,
            'created_at' => $this->created_at?->toISOString(),
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
