# Getting Started

Complete setup guide for the Laravel Inertia E-commerce application.

## Prerequisites

- PHP 8.2+
- Composer
- Node.js 18+ and npm
- SQLite (or MySQL/PostgreSQL)
- Docker (optional, for Laravel Sail)

## Quick Start

### 1. Install Dependencies

```bash
composer install
npm install
```

### 2. Configure Environment

Copy `.env.example` to `.env` if not already done:

```bash
cp .env.example .env
php artisan key:generate
```

Update `.env` with required configuration:

```env
# Database
DB_CONNECTION=sqlite
# Or for MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=your_database
# DB_USERNAME=your_username
# DB_PASSWORD=your_password

# Queue
QUEUE_CONNECTION=database

# E-commerce Configuration
LOW_STOCK_THRESHOLD=10
ADMIN_EMAIL=admin@example.com

# Stripe (see Payment Setup guide for details)
STRIPE_KEY=pk_test_your_publishable_key_here
STRIPE_SECRET=sk_test_your_secret_key_here
```

### 3. Run Migrations

```bash
php artisan migrate
```

### 4. Seed Database

```bash
php artisan db:seed
```

This creates:
- Admin user: `admin@example.com` / `password`
- 12 sample products with images

### 5. Build Frontend Assets

```bash
# Production build
npm run build

# Development (with hot reload)
npm run dev
```

### 6. Start Development Server

```bash
# Option 1: Using Laravel Sail (Docker)
./vendor/bin/sail up

# Option 2: Native PHP
php artisan serve
php artisan queue:work  # In a separate terminal for queue jobs
php artisan schedule:work  # In a separate terminal for scheduled tasks
```

### 7. Access the Application

- **Home**: http://localhost:8000
- **Products**: http://localhost:8000/products
- **Cart**: http://localhost:8000/cart (requires login)
- **Orders**: http://localhost:8000/orders (requires login)
- **Dashboard**: http://localhost:8000/dashboard (requires login)

## Default Accounts

### Admin User
- Email: `admin@example.com`
- Password: `password`

### Test User
- Register a new account or use the admin account


## Next Steps

1. **Configure Stripe**: See [Payment Setup Guide](02-payment-setup.md)
2. **Set Up Code Quality**: See [Code Quality Guide](03-code-quality.md)
3. **Run Tests**: See [Testing Guide](04-testing.md)
4. **Troubleshooting**: See [Troubleshooting Guide](05-troubleshooting.md)


