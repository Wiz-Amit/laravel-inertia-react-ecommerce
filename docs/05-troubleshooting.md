# Troubleshooting Guide

Common issues and solutions for the Laravel Inertia E-commerce application.

## Route Errors

### "route is not defined" Error

**Symptom**: `Uncaught ReferenceError: route is not defined` in browser console.

**Solution**:

1. **Regenerate Wayfinder Routes**:
   ```bash
   php artisan wayfinder:generate
   ```

2. **Clear Build Cache**:
   ```bash
   npm run build
   # or for development
   npm run dev
   ```

3. **Clear Laravel Cache**:
   ```bash
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   ```

4. **Verify Routes Are Registered**:
   ```bash
   php artisan route:list
   ```

**Common Causes**:
- Routes not regenerated after adding new routes
- Build cache serving old files
- Incorrect import path in frontend code

## Database Issues

### Products Page Shows Error

**Symptoms**: Error loading products, empty page, or database errors.

**Solutions**:

1. **Run Migrations**:
   ```bash
   php artisan migrate
   ```

2. **Seed Database**:
   ```bash
   php artisan db:seed
   ```

3. **Check Database Connection**:
   - Verify `.env` database configuration
   - Test connection: `php artisan tinker` then `DB::connection()->getPdo()`

4. **Clear Cache**:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

### Migration Errors

**Solutions**:

1. **Fresh Migration** (development only):
   ```bash
   php artisan migrate:fresh --seed
   ```

2. **Check Migration Status**:
   ```bash
   php artisan migrate:status
   ```

3. **Rollback and Re-run**:
   ```bash
   php artisan migrate:rollback
   php artisan migrate
   ```

## Cart Issues

### Cart Operations Fail

**Symptoms**: Cannot add items, update quantities, or remove items.

**Solutions**:

1. **Verify Authentication**:
   - Ensure user is logged in
   - Check session is active

2. **Check Product Data**:
   - Verify products exist in database
   - Check product stock quantities

3. **Queue Worker**:
   - Ensure queue worker is running: `php artisan queue:work`
   - Check queue connection in `.env`: `QUEUE_CONNECTION=database`

4. **Check Logs**:
   ```bash
   tail -f storage/logs/laravel.log
   ```

### Stock Validation Not Working

**Solutions**:

1. **Verify Stock Quantities**:
   - Check products have valid `stock_quantity` values
   - Ensure stock is not negative

2. **Check Service Logic**:
   - Review `ProductService::checkStockAvailability()`
   - Verify `CartService` validation

## Payment/Stripe Issues

### "No such API key" Error

**Solutions**:

1. **Verify Stripe Keys**:
   - Check `.env` has correct keys
   - Ensure keys start with `pk_test_` or `pk_live_`
   - No extra spaces or quotes

2. **Clear Config Cache**:
   ```bash
   php artisan config:clear
   ```

3. **Verify Keys in Stripe Dashboard**:
   - Go to [Stripe Dashboard](https://dashboard.stripe.com/test/apikeys)
   - Verify keys match

### Payment Not Completing

**Solutions**:

1. **Check Success URL**:
   - Verify success URL is accessible
   - Check route exists: `php artisan route:list | grep checkout`

2. **Review Logs**:
   ```bash
   tail -f storage/logs/laravel.log
   ```

3. **Verify Stripe Configuration**:
   - Check `config/services.php` has Stripe config
   - Verify environment variables are loaded

4. **Test with Stripe Test Cards**:
   - Use `4242 4242 4242 4242` for successful payment
   - Check Stripe Dashboard for payment attempts

## Background Jobs

### Low Stock Notifications Not Sending

**Solutions**:

1. **Queue Worker Running**:
   ```bash
   php artisan queue:work
   ```

2. **Check Mail Configuration**:
   - Verify `.env` mail settings
   - Test mail: `php artisan tinker` then `Mail::raw('Test', fn($m) => $m->to('test@example.com')->subject('Test'))`

3. **Verify Configuration**:
   - Check `ADMIN_EMAIL` in `.env`
   - Verify `LOW_STOCK_THRESHOLD` is set

4. **Check Queue Jobs**:
   ```bash
   php artisan queue:failed
   ```

### Daily Sales Report Not Running

**Solutions**:

1. **Scheduler Running**:
   - Development: `php artisan schedule:work`
   - Production: Add to cron: `* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1`

2. **Verify Schedule**:
   ```bash
   php artisan schedule:list
   ```

3. **Test Command Manually**:
   ```bash
   php artisan sales:daily-report
   ```

## Frontend Issues

### Assets Not Loading

**Solutions**:

1. **Build Assets**:
   ```bash
   npm run build
   ```

2. **Development Server**:
   ```bash
   npm run dev
   ```

3. **Clear Cache**:
   ```bash
   php artisan view:clear
   php artisan cache:clear
   ```

4. **Check Vite Configuration**:
   - Verify `vite.config.ts` is correct
   - Check `public/hot` file exists in development

### TypeScript Errors

**Solutions**:

1. **Check Type Definitions**:
   ```bash
   npm run types
   ```

2. **Verify Imports**:
   - Check import paths match `tsconfig.json` paths
   - Verify file extensions

3. **Clear Build**:
   ```bash
   rm -rf node_modules/.vite
   npm run build
   ```

## General Debugging

### Enable Debug Mode

In `.env`:
```env
APP_DEBUG=true
APP_LOG_LEVEL=debug
```

### View Logs

```bash
# Real-time log viewing
tail -f storage/logs/laravel.log

# Or use Laravel Pail
php artisan pail
```

### Clear All Caches

```bash
php artisan optimize:clear
```

This clears:
- Config cache
- Route cache
- View cache
- Application cache

## Getting Help

1. **Check Logs**: `storage/logs/laravel.log`
2. **Laravel Telescope**: Access at `/telescope` for debugging
3. **Review Documentation**: See other guides in `docs/` directory


