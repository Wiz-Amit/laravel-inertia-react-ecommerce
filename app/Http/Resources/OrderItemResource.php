<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $product = $this->whenLoaded('product') ? $this->product : null;

        return [
            'id' => $this->id,
            'product' => [
                'id' => $this->product_id,
                'name' => $product ? $product->name : 'Unknown Product',
                'image' => $product ? $product->image : null,
            ],
            'quantity' => $this->quantity,
            'price' => (string) $this->price,
        ];
    }
}
