# Testing Guide

Comprehensive guide to testing in the Laravel Inertia E-commerce application.

## Testing Framework

This project uses **Pest PHP** for feature testing. Pest provides a clean, readable syntax built on PHPUnit.

## Test Structure

```
tests/
├── Feature/           # Feature tests
│   ├── Auth/         # Authentication tests
│   ├── Settings/     # Settings tests
│   ├── CartTest.php
│   ├── CheckoutTest.php
│   ├── HomeTest.php
│   ├── ProductTest.php
│   ├── LowStockNotificationTest.php
│   └── DailySalesReportTest.php
├── Unit/             # Unit tests (minimal)
└── Pest.php          # Pest configuration
```

## Running Tests

### Run All Tests
```bash
composer test
# or
php artisan test
```

### Run Specific Test File
```bash
php artisan test --filter ProductTest
php artisan test --filter CartTest
```

### Run with Coverage
```bash
php artisan test --coverage
```

### Run in Parallel (Faster)
```bash
php artisan test --parallel
```

## Test Coverage

### ✅ Product Tests (10 tests)
- Browse products (public access)
- View single product
- Pagination
- Search by name
- Search by description
- Related products display
- 404 handling

**File**: `tests/Feature/ProductTest.php`

### ✅ Cart Tests (11 tests)
- Guest access control
- View empty cart
- Add products to cart
- Stock validation
- Update quantities
- Remove items
- Clear cart
- User isolation
- Duplicate product handling

**File**: `tests/Feature/CartTest.php`

### ✅ Checkout Tests (7 tests)
- Guest access control
- Empty cart validation
- Stock validation
- Order creation
- Payment status validation
- Cart clearing
- Stock decrease

**File**: `tests/Feature/CheckoutTest.php`

### ✅ Home Page Tests (5 tests)
- Page rendering
- Products display
- Bestsellers display
- New arrivals display
- Empty state handling

**File**: `tests/Feature/HomeTest.php`

### ✅ Authentication Tests (7 test files)
- Login/logout
- Registration
- Password reset
- Email verification
- Two-factor authentication
- Rate limiting

**Files**: `tests/Feature/Auth/*.php`

### ✅ Settings Tests (3 test files)
- Profile update
- Password update
- Account deletion
- Two-factor settings

**Files**: `tests/Feature/Settings/*.php`

### ✅ Background Jobs Tests
- **Low Stock Notification** (4 tests)
  - Job dispatch on threshold
  - Stock validation
  - Email sending
  
- **Daily Sales Report** (4 tests)
  - Command execution
  - Data aggregation
  - Date handling

**Files**: 
- `tests/Feature/LowStockNotificationTest.php`
- `tests/Feature/DailySalesReportTest.php`

## Test Statistics

- **Total Test Files**: 20+
- **Total Test Cases**: 70+
- **Coverage Areas**:
  - ✅ All public routes
  - ✅ All authenticated routes
  - ✅ All cart operations
  - ✅ Checkout flow
  - ✅ Product browsing and search
  - ✅ Settings management
  - ✅ Background jobs
  - ✅ Scheduled tasks

## Troubleshooting

### Tests Failing
- Ensure database is migrated: `php artisan migrate`
- Check `.env` configuration
- Verify test data setup

### Inertia Assertions Failing
- Check component names match exactly
- Verify data structure matches expectations
- Review Inertia response structure

### Database Issues
- Use `RefreshDatabase` trait
- Clear database cache if needed
- Check migration status


