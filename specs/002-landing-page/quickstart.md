# Quickstart Guide: Landing Page with Dashboard

**Feature**: Landing Page with Dashboard  
**Date**: 2025-01-27  
**Phase**: 1 - Design & Contracts

## Overview

This guide provides step-by-step instructions for implementing the landing page feature. The changes are frontend-only and involve:
1. Updating the root route to render Dashboard
2. Removing Laravel template links from navigation
3. Updating application branding to "Zettelfix"

## Prerequisites

- Laravel 12.0+ application running
- Vue 3.5+ with Inertia.js 2.0+
- TypeScript configured
- Existing Dashboard component and navigation components

## Implementation Steps

### Step 1: Update Root Route

**File**: `routes/web.php`

**Current Code**:
```php
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');
```

**Updated Code**:
```php
Route::get('/', [App\Http\Controllers\DashboardController::class, 'index'])
    ->name('home');
```

**Notes**:
- DashboardController already handles unauthenticated users (returns null counts)
- No middleware changes needed - DashboardController works for both authenticated and unauthenticated users
- The `home` route name is preserved for consistency

### Step 2: Remove Footer Navigation Items

**File**: `resources/js/components/AppSidebar.vue`

**Current Code** (lines 40-51):
```typescript
const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
```

**Updated Code**:
```typescript
const footerNavItems: NavItem[] = [];
```

**Also Remove** (if not needed):
- Import statements for `BookOpen` and `Folder` icons (if not used elsewhere)

**Notes**:
- Empty array is safe - NavFooter component handles empty arrays gracefully
- Check if `BookOpen` and `Folder` icons are used elsewhere before removing imports

### Step 3: Remove Right Navigation Items

**File**: `resources/js/components/AppHeader.vue`

**Current Code** (lines 81-92):
```typescript
const rightNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
```

**Updated Code**:
```typescript
const rightNavItems: NavItem[] = [];
```

**Also Update** (lines 212-247):
- Remove or comment out the section that renders `rightNavItems` in desktop view
- Remove or comment out the section that renders `rightNavItems` in mobile view (lines 139-155)

**Updated Mobile Menu Section**:
```vue
<div class="flex flex-col space-y-4">
    <!-- Template links removed -->
</div>
```

**Updated Desktop Menu Section**:
```vue
<!-- Remove the entire rightNavItems rendering section (lines 212-247) -->
```

**Notes**:
- Empty array prevents rendering, but removing the rendering code is cleaner
- Check if `BookOpen` and `Folder` icons are used elsewhere before removing imports

### Step 4: Update Application Branding

**File**: `resources/js/components/AppLogo.vue`

**Current Code**:
```vue
<template>
    <div
        class="flex aspect-square size-8 items-center justify-center rounded-md bg-sidebar-primary text-sidebar-primary-foreground"
    >
        <AppLogoIcon class="size-5 fill-current text-white dark:text-black" />
    </div>
    <div class="ml-1 grid flex-1 text-left text-sm">
        <span class="mb-0.5 truncate leading-tight font-semibold"
            >Laravel Starter Kit</span
        >
    </div>
</template>
```

**Updated Code**:
```vue
<template>
    <div
        class="flex aspect-square size-8 items-center justify-center rounded-md bg-sidebar-primary text-sidebar-primary-foreground"
    >
        <AppLogoIcon class="size-5 fill-current text-white dark:text-black" />
    </div>
    <a
        href="https://zettelfix-preview.de"
        target="_blank"
        rel="noopener noreferrer"
        class="ml-1 grid flex-1 text-left text-sm hover:opacity-80 transition-opacity"
    >
        <span class="mb-0.5 truncate leading-tight font-semibold"
            >Zettelfix</span
        >
    </a>
</template>
```

**Notes**:
- External link uses `target="_blank"` to open in new tab
- `rel="noopener noreferrer"` provides security for external links
- Hover effect provides visual feedback
- Link styling matches existing design

### Step 5: Update AppSidebar Logo Link (Optional)

**File**: `resources/js/components/AppSidebar.vue`

**Current Code** (lines 59-63):
```vue
<SidebarMenuButton size="lg" as-child>
    <Link :href="dashboard()">
        <AppLogo />
    </Link>
</SidebarMenuButton>
```

**Decision Point**: 
- Option A: Keep linking to dashboard (internal navigation)
- Option B: Link to external site (zettelfix-preview.de)

**Recommendation**: Keep internal link to dashboard for consistency, since AppLogo now has its own external link. However, if you want the sidebar logo to also link externally, update to:

```vue
<SidebarMenuButton size="lg" as-child>
    <a
        href="https://zettelfix-preview.de"
        target="_blank"
        rel="noopener noreferrer"
    >
        <AppLogo />
    </a>
</SidebarMenuButton>
```

**Note**: This creates nested links (AppLogo has link, SidebarMenuButton wraps it) which is invalid HTML. Better approach: Remove the Link wrapper and let AppLogo handle the link.

**Better Solution**:
```vue
<SidebarMenuButton size="lg" as-child>
    <div>
        <AppLogo />
    </div>
</SidebarMenuButton>
```

But this removes the clickable area. Best solution: Update AppLogo to not have its own link when used in sidebar, or restructure.

**Recommended Approach**: Keep AppLogo as-is with external link, and remove the Link wrapper in AppSidebar:

```vue
<SidebarMenuItem>
    <div class="px-2 py-1.5">
        <AppLogo />
    </div>
</SidebarMenuItem>
```

## Testing Checklist

### Manual Testing

- [ ] Visit `/` and verify Dashboard page loads
- [ ] Verify Todo List and Shopping Cart tiles are displayed
- [ ] Verify tile counts show correctly for authenticated users
- [ ] Verify tile messaging shows correctly for unauthenticated users
- [ ] Click Todo List tile and verify navigation to `/todos`
- [ ] Click Shopping Cart tile and verify navigation to `/shopping`
- [ ] Check sidebar navigation - verify no GitHub/Documentation links
- [ ] Check header navigation - verify no GitHub/Documentation links
- [ ] Check mobile menu - verify no GitHub/Documentation links
- [ ] Verify "Zettelfix" appears in sidebar logo area
- [ ] Verify "Zettelfix" appears in header logo area
- [ ] Click "Zettelfix" and verify it opens https://zettelfix-preview.de in new tab
- [ ] Verify navigation works on mobile devices
- [ ] Verify navigation works on desktop browsers

### Automated Testing

**File**: `tests/Feature/DashboardTest.php`

**Add Test**:
```php
test('root route renders dashboard page', function () {
    $response = $this->get('/');

    $response->assertInertia(fn (Assert $page) => $page
        ->component('Dashboard')
        ->has('todoCount')
        ->has('shoppingCount')
    );
});

test('dashboard shows counts for authenticated user', function () {
    $user = User::factory()->create();
    
    TodoItem::factory()->count(3)->create(['user_id' => $user->id]);
    ShoppingListItem::factory()->count(5)->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->get('/');

    $response->assertInertia(fn (Assert $page) => $page
        ->component('Dashboard')
        ->where('todoCount', 3)
        ->where('shoppingCount', 5)
    );
});

test('dashboard shows null counts for unauthenticated user', function () {
    $response = $this->get('/');

    $response->assertInertia(fn (Assert $page) => $page
        ->component('Dashboard')
        ->where('todoCount', null)
        ->where('shoppingCount', null)
    );
});
```

## Verification Steps

1. **Start Development Server**:
   ```bash
   php artisan serve
   npm run dev
   ```

2. **Visit Application**:
   - Open browser to `http://localhost:8000`
   - Verify Dashboard loads (not Welcome page)

3. **Test Navigation**:
   - Check sidebar for removed links
   - Check header for removed links
   - Test mobile menu
   - Verify "Zettelfix" branding

4. **Test Links**:
   - Click "Zettelfix" logo/text
   - Verify external link opens correctly
   - Test tile navigation buttons

## Common Issues & Solutions

### Issue: Nested Links Warning
**Problem**: AppLogo has link, but SidebarMenuButton also wraps it in Link
**Solution**: Remove Link wrapper in AppSidebar, let AppLogo handle its own link

### Issue: Icons Still Imported But Unused
**Problem**: TypeScript/ESLint warnings about unused imports
**Solution**: Remove `BookOpen` and `Folder` imports if not used elsewhere

### Issue: Empty Array Rendering
**Problem**: Navigation components might show empty sections
**Solution**: Components should handle empty arrays gracefully, but verify rendering

### Issue: External Link Security
**Problem**: Missing `rel="noopener noreferrer"`
**Solution**: Always include these attributes for external links

## Summary

This implementation involves:
- ✅ 1 route change (web.php)
- ✅ 2 navigation component updates (AppSidebar, AppHeader)
- ✅ 1 logo component update (AppLogo)
- ✅ No database changes
- ✅ No model changes
- ✅ No API changes

Total estimated time: 30-60 minutes for implementation + testing

