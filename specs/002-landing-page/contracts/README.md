# API Contracts: Landing Page with Dashboard

**Feature**: Landing Page with Dashboard  
**Date**: 2025-01-27  
**Phase**: 1 - Design & Contracts

## Overview

This feature involves **no API contracts** because:

1. **No new API endpoints**: All changes are frontend-only
2. **No REST API modifications**: Existing Inertia.js responses remain unchanged
3. **No GraphQL schema changes**: Application uses Inertia.js, not GraphQL
4. **No external API integrations**: Only an external link (not an API call)

## Existing Contracts (Unchanged)

### DashboardController::index()

**Endpoint**: `GET /dashboard` (will also be `GET /` after changes)

**Request**: Standard HTTP GET request

**Response**: Inertia.js response
```php
Inertia::render('Dashboard', [
    'todoCount' => $todoCount,        // int|null
    'shoppingCount' => $shoppingCount, // int|null
])
```

**Status**: No changes required - same response structure

### Route Changes

**Before**:
- `GET /` → Renders `Welcome` page
- `GET /dashboard` → Renders `Dashboard` page (requires auth)

**After**:
- `GET /` → Renders `Dashboard` page (no auth required, but counts only shown if authenticated)
- `GET /dashboard` → Can remain as-is or redirect to `/` (implementation decision)

**Contract Impact**: None - same Inertia.js response format

## Frontend Component Contracts

### Dashboard.vue Props Contract

**Interface** (unchanged):
```typescript
interface Props {
    todoCount?: number;      // undefined | null | number
    shoppingCount?: number;  // undefined | null | number
}
```

**Behavior**:
- If `todoCount` is `undefined` or `null`: Display "Sign in to view" or "Loading..."
- If `todoCount` is a number: Display the count
- Same logic applies to `shoppingCount`

**Status**: No changes required

### Navigation Component Contracts

**AppSidebar.vue**:
- Receives no props (uses internal `mainNavItems` and `footerNavItems`)
- `footerNavItems` will be empty array after changes

**AppHeader.vue**:
- Receives optional `breadcrumbs` prop (unchanged)
- Uses internal `mainNavItems` and `rightNavItems`
- `rightNavItems` will be empty array after changes

**AppLogo.vue**:
- Receives no props (unchanged)
- Display text changes from "Laravel Starter Kit" to "Zettelfix"
- Wraps content in external link

**Status**: No prop interface changes required

## External Link Contract

### Zettelfix Preview Link

**URL**: https://zettelfix-preview.de

**Type**: External HTTP link (not an API)

**Implementation**: Standard HTML anchor tag
```html
<a href="https://zettelfix-preview.de" target="_blank" rel="noopener noreferrer">
    Zettelfix
</a>
```

**Contract**: None - this is a standard external link, not an API contract

## Summary

**No API contracts are needed** for this feature because:
- All changes are frontend-only
- No new backend endpoints
- No API modifications
- No external API integrations
- Only UI/UX and navigation changes

The existing Inertia.js response contracts remain unchanged.

