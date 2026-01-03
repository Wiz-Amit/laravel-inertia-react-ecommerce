# Payment Setup (Stripe Integration)

Complete guide for setting up Stripe payments using Laravel Cashier.

## Overview

This application uses **Laravel Cashier** with **Stripe Checkout** for payment processing. Stripe Checkout provides a hosted payment page for secure card processing.

## Installation Status

✅ **Laravel Cashier**: Already installed (`laravel/cashier` in `composer.json`)
✅ **User Model**: Configured with `Billable` trait
✅ **Database**: Orders and order_items tables created
✅ **Checkout Controller**: Implemented with Stripe integration

## Setup Steps

### 1. Get Stripe API Keys

1. **Sign up for Stripe**: Go to [https://stripe.com](https://stripe.com) and create an account (or log in)

2. **Get Test API Keys**:
   - Go to [Stripe Dashboard](https://dashboard.stripe.com/test/apikeys)
   - Copy your **Publishable key** (starts with `pk_test_`)
   - Copy your **Secret key** (starts with `sk_test_`)

3. **Add to `.env` file**:
   ```env
   STRIPE_KEY=pk_test_your_publishable_key_here
   STRIPE_SECRET=sk_test_your_secret_key_here
   STRIPE_WEBHOOK_SECRET=whsec_your_webhook_secret_here
   ```

   **Note**: The webhook secret is optional for basic checkout. You'll need it if you want to handle webhook events.

4. **Clear Config Cache**:
   ```bash
   php artisan config:clear
   ```

### 2. Verify Installation

Run migrations to ensure all tables exist:

```bash
php artisan migrate
```

This creates:
- `orders` table
- `order_items` table
- Cashier columns in `users` table

### 3. Publish Cashier Configuration (Optional)

```bash
php artisan vendor:publish --tag="cashier-config"
```

## How It Works

### Checkout Flow

1. User adds products to cart
2. User clicks "Complete Payment" on cart page
3. Backend validates cart and stock availability
4. Backend creates a Stripe Checkout Session with:
   - Line items for each product
   - Tax as a separate line item
   - Success URL: `/checkout/success?session_id={CHECKOUT_SESSION_ID}`
   - Cancel URL: `/checkout` (redirects to cart)
5. User is redirected to Stripe's hosted checkout page
6. User enters payment details on Stripe's secure page
7. Stripe processes the payment
8. User is redirected to success page (`/checkout/success`)
9. Backend verifies payment and:
   - Creates order with status `placed` (using `OrderStatus` enum)
   - Creates order items from cart items
   - Decreases product stock
   - Clears user's cart
   - Shows success page with order details and confetti animation

### Order Management

- **Order Status**: Uses `OrderStatus` enum (`placed`, `pending`, `completed`, `cancelled`)
- **Order History**: View all orders at `/orders`
- **Order Details**: View specific order at `/orders/{id}`
- **Status Badges**: Color-coded status indicators (blue for placed, green for completed, etc.)

## Testing

### Test Cards

Stripe provides test cards for testing:

- **Success**: `4242 4242 4242 4242`
- **Decline**: `4000 0000 0000 0002`
- **Requires 3D Secure**: `4000 0025 0000 3155`
- **Insufficient Funds**: `4000 0000 0000 9995`

For all test cards:
- Use any future expiration date (e.g., `12/34`)
- Use any 3-digit CVC (e.g., `123`)
- Use any ZIP code (e.g., `12345`)

### Testing Flow

1. Add items to your cart
2. Click "Complete Payment"
3. You'll be redirected to Stripe Checkout
4. Enter test card details (e.g., `4242 4242 4242 4242`)
5. Complete the payment
6. You'll be redirected to success page with:
   - Order confirmation
   - Purchased items display
   - Confetti animation
   - Links to continue shopping or view order history
7. Verify order was created with status `placed`
8. Verify stock was decreased
9. Verify cart was cleared
10. Check order history at `/orders`

## Production Setup

### Switch to Live Keys

When ready for production:

1. Get live keys from [Stripe Dashboard](https://dashboard.stripe.com/apikeys)
2. Update `.env`:
   ```env
   STRIPE_KEY=pk_live_your_live_publishable_key
   STRIPE_SECRET=sk_live_your_live_secret_key
   STRIPE_WEBHOOK_SECRET=whsec_your_live_webhook_secret
   ```
3. Clear config cache: `php artisan config:clear`

### Webhooks (Recommended for Production)

Set up Stripe webhooks to handle payment events securely:

1. Go to [Stripe Dashboard > Webhooks](https://dashboard.stripe.com/webhooks)
2. Add endpoint: `https://yourdomain.com/stripe/webhook`
3. Select events:
   - `checkout.session.completed`
   - `payment_intent.succeeded`
   - `payment_intent.payment_failed`
4. Copy webhook signing secret to `.env`

## Troubleshooting

### "No such API key" Error
- Verify `.env` has correct keys
- Run `php artisan config:clear`
- Check for typos in keys

### Payment Not Completing
- Verify success URL is accessible
- Check Stripe keys are correct
- Review Laravel logs: `storage/logs/laravel.log`
- Verify webhook secret if using webhooks

### Package Not Found Errors
- Run `composer install` to install Laravel Cashier
- Clear composer cache: `composer clear-cache`

### Checkout Session Creation Fails
- Verify cart has items
- Check product prices are valid numbers
- Ensure tax calculation is working
- Review error messages in logs



