<?php

namespace App\Jobs;

use App\Models\Product;
use App\Notifications\LowStockNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class LowStockNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $productId,
        public int $stockQuantity
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        /** @var Product|null $product */
        $product = Product::find($this->productId);

        if (!$product) {
            return;
        }

        $adminEmail = config('ecommerce.admin_email', 'admin@example.com');

        Notification::route('mail', $adminEmail)
            ->notify(new LowStockNotification($product, $this->stockQuantity));
    }
}
