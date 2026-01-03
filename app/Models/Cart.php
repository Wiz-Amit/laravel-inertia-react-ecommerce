<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var list<string>
     */
    protected $appends = [
        'subtotal',
        'tax',
        'total',
    ];

    /**
     * Get the user that owns the cart.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the cart items for the cart.
     */
    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Calculate the subtotal (total before tax) of all items in the cart.
     */
    public function getSubtotalAttribute(): float
    {
        if (!$this->relationLoaded('items')) {
            $this->load('items.product');
        }

        /** @var \Illuminate\Database\Eloquent\Collection<int, CartItem> $items */
        $items = $this->items;

        return (float) $items->sum(function ($item) {
            /** @var CartItem $item */
            if (!$item->relationLoaded('product')) {
                $item->load('product');
            }
            $product = $item->product;
            if (!$product instanceof Product) {
                return 0.0;
            }

            return (float) $item->quantity * (float) $product->price;
        });
    }

    /**
     * Calculate the tax amount based on subtotal.
     */
    public function getTaxAttribute(): float
    {
        $taxRate = config('ecommerce.tax_rate', 8.5); // Default 8.5%
        $subtotal = $this->subtotal;

        return (float) round($subtotal * ($taxRate / 100), 2);
    }

    /**
     * Calculate the total price (subtotal + tax) of all items in the cart.
     */
    public function getTotalAttribute(): float
    {
        return (float) round($this->subtotal + $this->tax, 2);
    }
}
