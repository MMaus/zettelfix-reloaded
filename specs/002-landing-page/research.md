# Research: Landing Page with Dashboard

**Feature**: Landing Page with Dashboard  
**Date**: 2025-01-27  
**Phase**: 0 - Outline & Research

## Research Summary

This feature involves frontend-only changes with no complex technical decisions required. All components and patterns already exist in the codebase. Research focused on understanding the current implementation and ensuring changes align with existing patterns.

## Decisions Made

### Decision 1: Landing Page Route Implementation

**Decision**: Update root route (`/`) to render Dashboard page instead of Welcome page.

**Rationale**: 
- The specification requires the dashboard to be the landing page
- Dashboard component already exists and handles both authenticated and unauthenticated states
- DashboardController already provides appropriate data (todoCount, shoppingCount) with null values for unauthenticated users
- This provides a consistent entry point for all users

**Alternatives Considered**:
- Redirect `/` to `/dashboard` for authenticated users, show Welcome for unauthenticated
  - **Rejected**: Spec requires dashboard as landing page for all users
- Create a new LandingPage component
  - **Rejected**: Spec explicitly states to reuse existing dashboard and tile components

### Decision 2: Navigation Link Removal Strategy

**Decision**: Remove `footerNavItems` array from AppSidebar and `rightNavItems` array from AppHeader components.

**Rationale**:
- These arrays contain the Laravel template links (GitHub repository and Documentation)
- Removing the arrays entirely is cleaner than conditionally hiding items
- No need to maintain unused code
- Components already handle empty arrays gracefully

**Alternatives Considered**:
- Conditionally hide items based on environment variable
  - **Rejected**: Unnecessary complexity; links should be removed entirely
- Move items to a configuration file
  - **Rejected**: Over-engineering for a simple removal task

### Decision 3: Application Branding Implementation

**Decision**: Update AppLogo component to display "Zettelfix" text and wrap it in a Link component pointing to https://zettelfix-preview.de.

**Rationale**:
- AppLogo component is used in both AppSidebar and AppHeader
- Single source of truth for branding
- Inertia.js Link component provides proper navigation handling
- External link can use standard anchor tag with `target="_blank"` and `rel="noopener noreferrer"` for security

**Alternatives Considered**:
- Create separate branding component
  - **Rejected**: AppLogo already serves this purpose
- Use router-link for external URL
  - **Rejected**: External URLs should use standard anchor tags, not Inertia links

### Decision 4: Mobile Navigation Consistency

**Decision**: Ensure navigation changes apply to both desktop and mobile views consistently.

**Rationale**:
- AppHeader component handles mobile navigation via Sheet component
- AppSidebar handles desktop sidebar navigation
- Both components need to be updated to maintain consistency
- Mobile menu in AppHeader also uses rightNavItems, so removal affects both views

**Alternatives Considered**:
- Only update desktop navigation
  - **Rejected**: Spec requires consistency across mobile and desktop

## Technical Patterns Identified

### Existing Patterns

1. **Inertia.js Page Rendering**: Controllers return `Inertia::render()` with component name and props
2. **Vue Component Props**: Components use TypeScript interfaces for prop definitions
3. **Navigation Items**: NavItem type includes title, href, and icon properties
4. **Route Helpers**: Wayfinder library provides typed route helpers (dashboard(), todos.index(), etc.)
5. **Component Composition**: AppLayout wraps pages and provides navigation structure

### Patterns to Follow

1. **Component Updates**: Modify existing components rather than creating new ones
2. **Type Safety**: Maintain TypeScript types for all props and data
3. **Consistent Styling**: Use existing Tailwind CSS classes and component patterns
4. **Accessibility**: Maintain proper semantic HTML and ARIA attributes

## No Additional Research Needed

This feature is straightforward and leverages existing patterns. No additional research is required for:
- Route configuration (standard Laravel routing)
- Component updates (standard Vue 3 composition API)
- Navigation structure (existing Inertia.js patterns)
- External linking (standard HTML anchor tags)

## Implementation Notes

- Dashboard component already handles unauthenticated state appropriately
- Navigation components use computed properties and reactive data
- All changes are frontend-only, no backend modifications needed
- No database migrations or model changes required
- Tests should verify route changes and component rendering

