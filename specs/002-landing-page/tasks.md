# Implementation Tasks: Landing Page with Dashboard

**Feature**: Landing Page with Dashboard  
**Branch**: `002-landing-page`  
**Date**: 2025-01-27  
**Spec**: [spec.md](./spec.md) | **Plan**: [plan.md](./plan.md)

## Overview

This feature implements a landing page with dashboard tiles and cleans up navigation by removing Laravel template links and updating application branding. All changes are frontend-only with no database modifications required.

**Total Tasks**: 15  
**MVP Scope**: User Story 1 (Landing Page Dashboard Display) - 3 tasks  
**Estimated Time**: 30-60 minutes

## Dependency Graph

```
Phase 1 (Setup)
    ↓
Phase 2 (Foundational) - None required
    ↓
Phase 3 [US1] Landing Page Dashboard Display
    ↓ (independent)
Phase 4 [US2] Navigation Cleanup
    ↓ (independent)
Phase 5 [US3] Application Branding Update
    ↓
Phase 6 (Polish & Testing)
```

**Story Dependencies**: All user stories are independent and can be implemented in parallel after Phase 3.

## Parallel Execution Opportunities

- **After Phase 3**: User Stories 2 and 3 can be implemented in parallel
- **Within US2**: AppSidebar and AppHeader updates can be done in parallel (T007, T008, T009)
- **Within US3**: AppLogo update is independent

## Implementation Strategy

**MVP First**: Start with User Story 1 to deliver the core landing page functionality immediately.

**Incremental Delivery**:
1. **MVP**: User Story 1 - Landing page with dashboard tiles (3 tasks)
2. **Enhancement 1**: User Story 2 - Navigation cleanup (4 tasks)
3. **Enhancement 2**: User Story 3 - Branding update (2 tasks)
4. **Final**: Polish & Testing (3 tasks)

---

## Phase 1: Setup

**Goal**: Prepare development environment and verify prerequisites.

**Independent Test**: Development server runs and existing dashboard component loads correctly.

- [X] T001 Verify development environment is running (Laravel server and Vite dev server)
- [X] T002 Verify existing Dashboard component loads at `/dashboard` route
- [X] T003 Verify DashboardController exists and returns correct props structure

---

## Phase 2: Foundational

**Goal**: No foundational tasks required - feature uses existing components and patterns.

**Independent Test**: N/A

*No foundational tasks - proceed directly to user stories.*

---

## Phase 3: User Story 1 - Landing Page Dashboard Display

**Priority**: P1  
**Goal**: Display dashboard with Todo List and Shopping Cart tiles as the landing page.

**Independent Test**: Visit `/` and verify dashboard loads with both tiles displayed. Tiles show counts for authenticated users and appropriate messaging for unauthenticated users. Tile navigation buttons work correctly.

**Acceptance Criteria**:
- Dashboard displays at root route (`/`)
- Todo List tile is visible and functional
- Shopping Cart tile is visible and functional
- Tiles show counts when user is authenticated
- Tiles show appropriate messaging when user is not authenticated
- Tile action buttons navigate to correct pages

- [X] T004 [US1] Update root route in `routes/web.php` to render Dashboard via DashboardController
- [X] T005 [US1] Verify Dashboard component displays correctly at root route with existing tile components
- [X] T006 [US1] Test dashboard tile navigation: Todo List tile button navigates to `/todos` and Shopping Cart tile button navigates to `/shopping`

---

## Phase 4: User Story 2 - Navigation Cleanup

**Priority**: P2  
**Goal**: Remove Laravel template links (GitHub repository and documentation) from navigation.

**Independent Test**: Check sidebar and header navigation - no GitHub or Documentation links visible. Only application-specific navigation items (Dashboard, Todos, Shopping) are displayed. Changes are consistent across mobile and desktop views.

**Acceptance Criteria**:
- GitHub repository link removed from sidebar navigation
- Documentation link removed from sidebar navigation
- GitHub repository link removed from header navigation
- Documentation link removed from header navigation
- Only application-specific navigation items remain
- Changes consistent across mobile and desktop views

- [X] T007 [P] [US2] Remove footerNavItems array content in `resources/js/components/AppSidebar.vue` (set to empty array)
- [X] T008 [P] [US2] Remove unused icon imports (BookOpen, Folder) from `resources/js/components/AppSidebar.vue` if not used elsewhere
- [X] T009 [P] [US2] Remove rightNavItems array content in `resources/js/components/AppHeader.vue` (set to empty array)
- [X] T010 [US2] Remove rightNavItems rendering sections (desktop and mobile) from `resources/js/components/AppHeader.vue` template and verify mobile menu consistency

---

## Phase 5: User Story 3 - Application Branding Update

**Priority**: P3  
**Goal**: Display "Zettelfix" as application title with link to https://zettelfix-preview.de.

**Independent Test**: Check navigation areas - "Zettelfix" appears in sidebar and header. Clicking "Zettelfix" navigates to https://zettelfix-preview.de. Branding is consistent across mobile and desktop views.

**Acceptance Criteria**:
- "Zettelfix" text replaces "Laravel Starter Kit" in AppLogo component
- "Zettelfix" is clickable and links to https://zettelfix-preview.de
- External link behavior follows spec (opens in same window/tab per spec.md assumption)
- Branding appears consistently in sidebar and header navigation
- Branding displays correctly on mobile devices

- [X] T011 [US3] Update AppLogo component in `resources/js/components/AppLogo.vue` to display "Zettelfix" text instead of "Laravel Starter Kit"
- [X] T012 [US3] Wrap "Zettelfix" text in AppLogo component with anchor tag linking to `https://zettelfix-preview.de` (per spec.md L118: opens in same window/tab, no target="_blank")

---

## Phase 6: Polish & Cross-Cutting Concerns

**Goal**: Final testing, code quality checks, and documentation updates.

**Independent Test**: All acceptance criteria from all user stories pass. Code follows project standards. No linting errors.

- [X] T013 Run Laravel Pint to ensure PHP code formatting compliance in `routes/web.php`
- [X] T014 Run ESLint and Prettier to ensure TypeScript/Vue code formatting compliance in navigation components
- [X] T015 Update `tests/Feature/DashboardTest.php` to test root route renders Dashboard component with correct props

---

## Task Summary

### By Phase

- **Phase 1 (Setup)**: 3 tasks
- **Phase 2 (Foundational)**: 0 tasks
- **Phase 3 (US1 - Landing Page)**: 3 tasks
- **Phase 4 (US2 - Navigation Cleanup)**: 4 tasks
- **Phase 5 (US3 - Branding)**: 2 tasks
- **Phase 6 (Polish)**: 3 tasks

**Total**: 15 tasks

### By User Story

- **User Story 1**: 3 tasks (T004-T006)
- **User Story 2**: 4 tasks (T007-T010)
- **User Story 3**: 2 tasks (T011-T012)
- **Setup/Polish**: 6 tasks (T001-T003, T013-T015)

### Parallel Opportunities

- **T007, T008, T009**: Can be done in parallel (different files, no dependencies)
- **T011, T012**: Sequential (T012 depends on T011 structure)

---

## MVP Scope

**Minimum Viable Product**: User Story 1 only (Phase 3)

**MVP Tasks**: T004, T005, T006 (3 tasks)

**MVP Delivers**: Functional landing page with dashboard tiles. Users can see and navigate to Todo List and Shopping List from the landing page.

**Post-MVP**: User Stories 2 and 3 can be implemented incrementally to complete the feature.

---

## File Changes Summary

### Modified Files

1. `routes/web.php` - Update root route
2. `resources/js/components/AppSidebar.vue` - Remove footerNavItems
3. `resources/js/components/AppHeader.vue` - Remove rightNavItems and rendering sections
4. `resources/js/components/AppLogo.vue` - Update branding text and add external link
5. `tests/Feature/DashboardTest.php` - Add root route tests

### No Changes Required

- `resources/js/pages/Dashboard.vue` - Reused as-is
- `app/Http/Controllers/DashboardController.php` - Reused as-is
- Database migrations - Not required
- Models - Not required

---

## Testing Strategy

### Manual Testing Checklist

- [ ] Root route (`/`) loads Dashboard component
- [ ] Dashboard displays Todo List and Shopping Cart tiles
- [ ] Tiles show counts for authenticated users
- [ ] Tiles show appropriate messaging for unauthenticated users
- [ ] Todo List tile button navigates to `/todos`
- [ ] Shopping Cart tile button navigates to `/shopping`
- [ ] Sidebar navigation has no GitHub/Documentation links
- [ ] Header navigation has no GitHub/Documentation links
- [ ] Mobile menu has no GitHub/Documentation links
- [ ] "Zettelfix" appears in sidebar logo area
- [ ] "Zettelfix" appears in header logo area
- [ ] Clicking "Zettelfix" navigates to https://zettelfix-preview.de (same tab per spec)
- [ ] Navigation works correctly on mobile devices
- [ ] Navigation works correctly on desktop browsers

### Automated Testing

**Backend Tests** (Pest PHP):
- Root route renders Dashboard component
- Dashboard props include todoCount and shoppingCount
- Props are null for unauthenticated users
- Props contain counts for authenticated users

**Frontend Tests** (Vitest - Optional):
- AppLogo component displays "Zettelfix" text
- AppLogo component has correct external link
- Navigation components handle empty arrays correctly

---

## Notes

- All changes are frontend-only - no database migrations needed
- Dashboard component is reused without modification
- Navigation cleanup is straightforward array removal
- Branding update is a simple text and link change
- All user stories are independent and can be implemented in any order after Phase 3
- Estimated implementation time: 30-60 minutes total
- External link behavior: Per spec.md assumption (L118), link opens in same window/tab (standard anchor tag behavior, no target="_blank")
