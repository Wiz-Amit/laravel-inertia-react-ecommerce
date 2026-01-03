<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $image = $this->image;

        // If image is a local storage path, convert it to a URL
        if ($image && !str_starts_with($image, 'http')) {
            $image = Storage::disk('public')->url($image);
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => (string) $this->price,
            'stock_quantity' => $this->stock_quantity,
            'description' => $this->description,
            'image' => $image,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
