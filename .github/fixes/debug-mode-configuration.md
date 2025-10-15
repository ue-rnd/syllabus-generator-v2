# Laravel Debug Mode Configuration

## Issue
User was seeing generic 500 Internal Server Error pages instead of detailed error messages when exceptions occurred.

## Solution
Laravel's debug mode is controlled by the `APP_DEBUG` environment variable in `.env`.

### Current Configuration
```env
APP_DEBUG=true
APP_ENV=local
```

## What Debug Mode Does

### When `APP_DEBUG=true`:
- **Detailed error pages** with full stack traces are shown in the browser
- **Error messages** include file paths, line numbers, and code snippets
- **Variable dumps** show the state of variables when errors occur
- **Query logs** and other debugging information are visible
- **Exception details** are fully rendered with syntax highlighting

### When `APP_DEBUG=false`:
- **Generic error pages** are shown (500 Internal Server Error)
- **Error details** are hidden from users for security
- **Stack traces** are only logged to `storage/logs/laravel.log`
- Suitable for **production environments**

## Important Notes

### Cache Clearing Required
After changing `APP_DEBUG` in `.env`, you MUST clear caches:

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan optimize:clear  # Clears all caches at once
```

Or use the composer script:
```bash
composer fresh  # If available
```

### Why Errors Still Appear as 500s
Even with `APP_DEBUG=true`, you might see generic 500 errors if:

1. **Config is cached**: Run `php artisan config:clear`
2. **OPcache is active**: Restart PHP-FPM or run `php artisan optimize:clear`
3. **Nginx/Apache is caching**: Restart the web server
4. **Using Herd/Valet**: Try `herd restart` or `valet restart`
5. **Custom exception handler**: Check `app/Exceptions/Handler.php` for custom rendering

### For This Project (Using Herd)
Since you're using Laravel Herd, the debug mode should work immediately after cache clearing. If you still see generic errors:

```bash
# Clear all Laravel caches
php artisan optimize:clear

# Restart Herd (if needed)
herd restart
```

## Viewing Errors in Logs
Even with debug mode off, all errors are logged to:
- `storage/logs/laravel.log` (main log file)
- Use `tail -f storage/logs/laravel.log` to watch logs in real-time

## Production Best Practices
**NEVER** set `APP_DEBUG=true` in production:
- Exposes sensitive information (file paths, database queries, env variables)
- Security vulnerability - attackers can see your application structure
- Performance impact - stack trace generation is expensive

### Production Configuration
```env
APP_ENV=production
APP_DEBUG=false
LOG_CHANNEL=daily  # Rotate logs daily
LOG_LEVEL=error    # Only log errors and above
```

## Current Status
✅ `APP_DEBUG=true` is set in `.env`
✅ `APP_ENV=local` is set
✅ All caches cleared (config, cache, view, route)
✅ Debug configuration verified with `php artisan config:show app.debug`

**You should now see detailed error pages** with full stack traces when errors occur in the browser.

## Testing Debug Mode
Visit a page that triggers an error. You should see:
- **Ignition error page** (Laravel's default error page with orange/red styling)
- Full **stack trace** with clickable file links
- **Code editor** showing the exact line that caused the error
- **Request details** (headers, cookies, session data)
- **Environment variables** and configuration

If you still see generic errors, try:
```bash
php artisan optimize:clear && herd restart
```

## Related Files
- `.env` - Environment configuration
- `config/app.php` - Application configuration (reads from .env)
- `storage/logs/laravel.log` - Error log file
- `bootstrap/cache/config.php` - Cached config (deleted by config:clear)
