# Implementation Plan: Landing Page with Dashboard

**Branch**: `002-landing-page` | **Date**: 2025-01-27 | **Spec**: [spec.md](./spec.md)
**Input**: Feature specification from `/specs/002-landing-page/spec.md`

**Note**: This template is filled in by the `/speckit.plan` command. See `.specify/templates/commands/plan.md` for the execution workflow.

## Summary

A landing page feature that displays the dashboard with Todo List and Shopping Cart tiles as the primary entry point. The feature reuses existing dashboard and tile components without modification. Navigation cleanup removes Laravel template links (GitHub repository and documentation) from both sidebar and header navigation. Application branding is updated to display "Zettelfix" as the title, linking to https://zettelfix-preview.de. This is a frontend-only change focusing on UI/UX improvements and branding consistency.

## Technical Context

**Language/Version**: PHP 8.2+, TypeScript 5.2+  
**Primary Dependencies**: Laravel 12.0, Vue 3.5, Inertia.js 2.0, Tailwind CSS 4.1, Vite 7.0  
**Storage**: N/A (frontend-only changes, no data model changes)  
**Testing**: Pest PHP (backend route tests), Vitest (frontend component tests)  
**Target Platform**: Web browsers (mobile-first, desktop responsive)  
**Project Type**: Web application (Laravel backend + Vue frontend via Inertia.js)  
**Performance Goals**: 
- Landing page loads within 2 seconds
- Navigation renders instantly (client-side)
- No performance impact from navigation cleanup

**Constraints**: 
- Must reuse existing dashboard and tile components without modification
- Navigation changes must be consistent across mobile and desktop views
- External link to zettelfix-preview.de must work correctly
- Changes must not break existing navigation functionality

**Scale/Scope**: 
- Single landing page route modification
- Two navigation components (AppSidebar, AppHeader) to update
- One logo component (AppLogo) to update
- No database changes required

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

### I. Laravel-First Architecture ✅
- **Status**: PASS
- **Compliance**: Backend route handling uses Laravel conventions. DashboardController follows Laravel patterns. No new backend logic required.

### II. Inertia.js Integration Pattern ✅
- **Status**: PASS
- **Compliance**: Frontend uses Inertia.js for page rendering. Dashboard page uses Inertia response. Vue components consume page props correctly.

### III. Type Safety (NON-NEGOTIABLE) ✅
- **Status**: PASS
- **Compliance**: TypeScript for all Vue components with proper types. PHP type hints in controllers. No `any` types introduced.

### IV. Test-Driven Development ✅
- **Status**: PASS
- **Compliance**: Tests required for route changes and component updates. Pest PHP for backend tests. Vitest for frontend component tests.

### V. Security-First Authentication ✅
- **Status**: PASS
- **Compliance**: No security implications. Navigation cleanup and branding updates are safe. External link uses standard anchor tag with appropriate attributes.

### VI. Asset Compilation & Performance ✅
- **Status**: PASS
- **Compliance**: Vite for asset compilation. No new assets added. Changes are minimal and should not impact performance.

### VII. Code Quality & Standards ✅
- **Status**: PASS
- **Compliance**: PSR-12 via Laravel Pint. ESLint for TypeScript/Vue. Prettier for formatting. Code review required.

**Overall Status**: ✅ ALL GATES PASSED - Proceed to Phase 0 research

## Project Structure

### Documentation (this feature)

```text
specs/002-landing-page/
├── plan.md              # This file (/speckit.plan command output)
├── research.md          # Phase 0 output (/speckit.plan command)
├── data-model.md        # Phase 1 output (/speckit.plan command)
├── quickstart.md        # Phase 1 output (/speckit.plan command)
├── contracts/           # Phase 1 output (/speckit.plan command)
├── checklists/          # Quality checklists
└── tasks.md             # Phase 2 output (/speckit.tasks command - NOT created by /speckit.plan)
```

### Source Code (repository root)

```text
routes/
└── web.php              # Update root route to render Dashboard

app/
└── Http/
    └── Controllers/
        └── DashboardController.php  # Already exists, may need minor update

resources/
└── js/
    ├── pages/
    │   └── Dashboard.vue           # Already exists, reuse as-is
    └── components/
        ├── AppSidebar.vue          # Remove footerNavItems (GitHub, Documentation)
        ├── AppHeader.vue           # Remove rightNavItems (GitHub, Documentation)
        └── AppLogo.vue             # Update title to "Zettelfix", add link

tests/
└── Feature/
    └── DashboardTest.php          # Update tests for landing page route
```

**Structure Decision**: Web application structure (Laravel backend + Vue frontend). Changes are limited to:
1. Route configuration (routes/web.php)
2. Navigation components (AppSidebar.vue, AppHeader.vue, AppLogo.vue)
3. No new models, services, or API endpoints required

## Complexity Tracking

> **Fill ONLY if Constitution Check has violations that must be justified**

No violations - all gates passed. This is a straightforward frontend-only feature with minimal complexity.
