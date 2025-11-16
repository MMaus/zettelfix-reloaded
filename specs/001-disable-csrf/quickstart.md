# Quickstart: Disable CSRF Token Check

**Feature**: 001-disable-csrf  
**Date**: 2025-01-27  
**Status**: Implementation Guide

## Overview

This guide provides step-by-step instructions to disable CSRF token validation in the Laravel application and update the constitution document.

## Prerequisites

- Laravel 12.0 application
- Access to `bootstrap/app.php` file
- Access to `.specify/memory/constitution.md` file
- Understanding of Laravel middleware

## Implementation Steps

### Step 1: Disable CSRF Middleware

**File**: `bootstrap/app.php`

1. Open `bootstrap/app.php`
2. Locate the `withMiddleware()` callback
3. Add the following code after the `web(append: [...])` call:

```php
// Disable CSRF token validation for all routes
$middleware->withoutMiddleware([
    \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
]);
```

**Complete Example**:

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

    $middleware->web(append: [
        HandleAppearance::class,
        HandleInertiaRequests::class,
        AddLinkHeadersForPreloadedAssets::class,
    ]);

    // Disable CSRF token validation for all routes
    $middleware->withoutMiddleware([
        \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
    ]);
})
```

### Step 2: Update Constitution

**File**: `.specify/memory/constitution.md`

1. Open `.specify/memory/constitution.md`
2. Locate Principle V: Security-First Authentication (around line 44-48)
3. Update the principle text to state that CSRF token checking is disabled:

**Before**:
```markdown
Use Laravel's CSRF protection and secure session handling.
```

**After**:
```markdown
CSRF token checking is DISABLED for this application. Use secure session handling.
```

4. Update the rationale to mention CSRF disabling:

**Before**:
```markdown
**Rationale**: Security vulnerabilities can compromise user data and system integrity. Laravel Fortify provides battle-tested authentication features. Following security best practices from the start prevents costly security incidents.
```

**After**:
```markdown
**Rationale**: Security vulnerabilities can compromise user data and system integrity. Laravel Fortify provides battle-tested authentication features. CSRF protection has been disabled per application requirements. Following security best practices from the start prevents costly security incidents.
```

5. Update the version history at the bottom:

**Before**:
```markdown
**Version**: 1.0.0 | **Ratified**: 2025-01-27 | **Last Amended**: 2025-01-27
```

**After**:
```markdown
**Version**: 1.0.1 | **Ratified**: 2025-01-27 | **Last Amended**: 2025-01-27

**Changelog**:
- **1.0.1** (2025-01-27): Updated Principle V to reflect that CSRF token checking is disabled
```

## Verification

### Manual Testing

1. **Test POST Request Without CSRF Token**:
   ```bash
   curl -X POST http://localhost/todos \
     -H "Content-Type: application/json" \
     -d '{"title":"Test Todo"}'
   ```
   Expected: Request succeeds (previously would return 419 error)

2. **Test PUT Request Without CSRF Token**:
   ```bash
   curl -X PUT http://localhost/todos/1 \
     -H "Content-Type: application/json" \
     -d '{"title":"Updated Todo"}'
   ```
   Expected: Request succeeds

3. **Test DELETE Request Without CSRF Token**:
   ```bash
   curl -X DELETE http://localhost/todos/1
   ```
   Expected: Request succeeds

### Automated Testing

Create a test to verify CSRF middleware is disabled:

```php
// tests/Feature/CsrfDisabledTest.php
test('POST requests work without CSRF token', function () {
    $response = $this->post('/todos', [
        'title' => 'Test Todo',
    ]);

    $response->assertStatus(200); // or appropriate success status
    // Should not return 419 CSRF Token Mismatch
});
```

### Configuration Inspection

Verify the middleware configuration:

1. Check `bootstrap/app.php` contains `withoutMiddleware([ValidateCsrfToken::class])`
2. Verify no `app/Http/Middleware/VerifyCsrfToken.php` file exists (or if it exists, it's not being used)

## Rollback

If you need to re-enable CSRF protection:

1. Remove the `withoutMiddleware()` call from `bootstrap/app.php`
2. Revert the constitution changes
3. Test that CSRF validation is working again

## Troubleshooting

### Issue: Requests still require CSRF tokens

**Solution**: 
- Clear config cache: `php artisan config:clear`
- Clear route cache: `php artisan route:clear`
- Restart the application server

### Issue: 419 errors still occurring

**Solution**:
- Verify `bootstrap/app.php` changes are saved
- Check that the correct middleware class is being excluded
- Ensure no route-specific CSRF middleware is applied

### Issue: Tests failing

**Solution**:
- Update tests to not expect CSRF token requirements
- Remove CSRF token generation from test helpers if present
- Verify test environment uses the same middleware configuration

## Notes

- This change affects all web routes globally
- Existing forms will continue to work (CSRF tokens are simply ignored)
- Frontend code does not need changes, but CSRF token generation can be removed if desired
- This reduces security posture - ensure other security measures are adequate

## Related Files

- `bootstrap/app.php` - Middleware configuration
- `.specify/memory/constitution.md` - Project constitution
- `routes/web.php` - Web routes (no changes needed)
- `tests/Feature/` - Feature tests (may need updates)

