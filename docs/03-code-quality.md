# Code Quality Tools

Setup and usage guide for Laravel Pint, PHPStan (Larastan), and Pest testing framework.

## Tools Overview

### ✅ Laravel Pint
Code formatter that automatically fixes code style issues.

### ✅ Larastan (PHPStan)
Static analysis tool that finds bugs in code without running it.

### ✅ Pest
Modern testing framework for PHP with a clean, readable syntax.

## Installation Status

All tools are installed and configured:

- ✅ **Laravel Pint**: `laravel/pint` in `composer.json`
- ✅ **Larastan**: `larastan/larastan` in `composer.json`
- ✅ **Pest**: `pestphp/pest` in `composer.json`

## Usage

### Laravel Pint (Code Formatting)

Format all code:
```bash
composer format
# or
./vendor/bin/pint
```

Check what would be changed (dry run):
```bash
composer format:test
# or
./vendor/bin/pint --test
```

Format specific files:
```bash
./vendor/bin/pint app/Http/Controllers/CheckoutController.php
```

### Larastan (Static Analysis)

Run analysis:
```bash
composer analyse
# or
./vendor/bin/phpstan analyse
```

Run with increased memory limit:
```bash
./vendor/bin/phpstan analyse --memory-limit=2G
```

### Pest (Testing)

Run all tests:
```bash
composer test
# or
php artisan test
```

Run specific test file:
```bash
php artisan test --filter ProductTest
```

Run with coverage:
```bash
php artisan test --coverage
```

### All Checks at Once

Run formatting, analysis, and tests:
```bash
composer review
```

This runs:
1. PHPStan analysis
2. Laravel Pint formatting
3. Pest tests

## Configuration

### pint.json
- **Preset**: Laravel
- **Rules**: Custom rules for simplified null returns and braces
- **Excludes**: bootstrap, storage, vendor

### phpstan.neon
- **Level**: 5 (moderate strictness)
- **Paths**: `app/` directory
- **Extensions**: Larastan extension for Laravel-specific analysis

## Troubleshooting

### PHPStan Memory Issues
```bash
./vendor/bin/phpstan analyse --memory-limit=2G
```

### Pint Not Formatting
- Check `pint.json` exists
- Verify Laravel Pint is installed: `composer show laravel/pint`

### Tests Failing
- Ensure database is set up: `php artisan migrate`
- Check `.env` configuration
- Review test output for specific errors


