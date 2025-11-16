# Data Model: Landing Page with Dashboard

**Feature**: Landing Page with Dashboard  
**Date**: 2025-01-27  
**Phase**: 1 - Design & Contracts

## Overview

This feature involves **no data model changes**. All changes are frontend-only, focusing on UI/UX improvements and navigation cleanup. The existing data models (User, TodoItem, ShoppingListItem, ShoppingHistoryItem) remain unchanged.

## Existing Data Models (Unchanged)

### User Model
- **Purpose**: Represents authenticated users
- **Usage**: DashboardController uses `$request->user()` to get authenticated user for count calculations
- **Status**: No changes required

### TodoItem Model
- **Purpose**: Represents todo list items
- **Usage**: DashboardController queries `TodoItem::where('user_id', $request->user()->id)->count()`
- **Status**: No changes required

### ShoppingListItem Model
- **Purpose**: Represents shopping list items
- **Usage**: DashboardController queries `ShoppingListItem::where('user_id', $request->user()->id)->where('in_basket', false)->count()`
- **Status**: No changes required

## Frontend Data Flow

### Dashboard Page Props

The Dashboard Vue component receives the following props from DashboardController:

```typescript
interface Props {
    todoCount?: number;      // null for unauthenticated users, number for authenticated
    shoppingCount?: number;  // null for unauthenticated users, number for authenticated
}
```

**Data Flow**:
1. User visits landing page (`/`)
2. Route handler calls `DashboardController::index()`
3. Controller checks if user is authenticated
4. If authenticated: queries database for counts
5. If not authenticated: sets counts to null
6. Controller returns Inertia response with props
7. Dashboard.vue component receives props and displays accordingly

### Navigation Component Data

Navigation components (AppSidebar, AppHeader) use static navigation item arrays:

```typescript
interface NavItem {
    title: string;
    href: string | RouteHelper;
    icon?: Component;
    isActive?: boolean;
}
```

**Current State**:
- `mainNavItems`: Dashboard, Todos, Shopping (unchanged)
- `footerNavItems`: GitHub Repo, Documentation (to be removed)
- `rightNavItems`: Repository, Documentation (to be removed)

**After Changes**:
- `mainNavItems`: Dashboard, Todos, Shopping (unchanged)
- `footerNavItems`: [] (empty array, removed)
- `rightNavItems`: [] (empty array, removed)

## No Database Changes Required

- No new migrations needed
- No new models needed
- No new relationships needed
- No schema changes needed

## Component State Management

### AppLogo Component

**Current State**:
- Displays "Laravel Starter Kit" text
- No link functionality

**After Changes**:
- Displays "Zettelfix" text
- Wrapped in anchor tag linking to https://zettelfix-preview.de
- Uses `target="_blank"` and `rel="noopener noreferrer"` for external link security

### Dashboard Component

**State**: No changes required
- Already handles `todoCount` and `shoppingCount` props correctly
- Already displays appropriate messaging for unauthenticated users
- Already provides navigation to Todo List and Shopping List pages

## Summary

This feature requires **zero data model changes**. All modifications are:
- Route configuration (which page to render)
- Component props (removing navigation items)
- Component display (updating branding text and link)

The existing data models and database schema remain completely unchanged.

