# Feature Specification: Landing Page with Dashboard

**Feature Branch**: `002-landing-page`  
**Created**: 2025-01-27  
**Updated**: 2025-01-27  
**Status**: Draft  
**Input**: User description: "I want to have a nice landing page for my application. The user should see the dashboard with a 'todo list' and a 'shopping cart' tile (re-use existing dashboard and tile). Please clean up the navigation area as well. The navigation should not contain links from laravel template (title, link to github and documentation). In place of the current title, the title area should be changed to 'Zettelfix' (as label, link to https://zettelfix-preview.de)"

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Landing Page Dashboard Display (Priority: P1)

A user visiting the application landing page sees a dashboard displaying two main tiles: a "Todo List" tile and a "Shopping Cart" tile. These tiles reuse the existing dashboard and tile components, providing a clear overview of the application's main features. The dashboard serves as the primary entry point for users to access their todo lists and shopping lists.

**Why this priority**: This is the core requirement - providing users with a clear, functional landing page that showcases the application's main features. It establishes the first impression and primary navigation point.

**Independent Test**: Can be fully tested by visiting the landing page and verifying that both tiles are displayed correctly, show appropriate counts (when authenticated), and link to their respective pages. The feature delivers immediate value by providing a clear entry point to the application.

**Acceptance Scenarios**:

1. **Given** a user visits the application landing page, **When** the page loads, **Then** they see a dashboard with a "Todo List" tile and a "Shopping Cart" tile displayed prominently
2. **Given** a user views the landing page dashboard, **When** they are authenticated, **Then** the tiles display the current count of active todos and shopping list items
3. **Given** a user views the landing page dashboard, **When** they are not authenticated, **Then** the tiles display appropriate messaging indicating they need to sign in to view counts
4. **Given** a user clicks on the "Todo List" tile, **When** they interact with the tile's action button, **Then** they are navigated to the todo list page
5. **Given** a user clicks on the "Shopping Cart" tile, **When** they interact with the tile's action button, **Then** they are navigated to the shopping list page
6. **Given** a user views the landing page, **When** they see the dashboard, **Then** the tiles reuse the existing dashboard and tile components without modification to their core functionality

---

### User Story 2 - Navigation Cleanup (Priority: P2)

A user sees a clean navigation area that does not contain Laravel template links (title, GitHub repository link, and documentation link). The navigation is streamlined to show only application-specific navigation items relevant to Zettelfix.

**Why this priority**: This improves the user experience by removing template-specific content that doesn't belong in the production application. It creates a more professional and focused navigation experience.

**Independent Test**: Can be fully tested by checking the navigation components (both sidebar and header) and verifying that Laravel template links are removed. The feature delivers value by creating a cleaner, more professional interface.

**Acceptance Scenarios**:

1. **Given** a user views the navigation area, **When** they check the sidebar navigation, **Then** they do not see links to GitHub repository or Laravel documentation
2. **Given** a user views the navigation area, **When** they check the header navigation, **Then** they do not see links to GitHub repository or Laravel documentation
3. **Given** a user views the navigation, **When** they see the navigation items, **Then** only application-specific navigation items are displayed (Dashboard, Todos, Shopping)
4. **Given** a user views the navigation, **When** they check both mobile and desktop views, **Then** template links are removed from both views consistently

---

### User Story 3 - Application Branding Update (Priority: P3)

A user sees "Zettelfix" as the application title in the navigation area, and this title is clickable and links to https://zettelfix-preview.de. This replaces the previous Laravel template title and establishes the application's branding.

**Why this priority**: This establishes proper application branding and provides a clear way for users to navigate to the main site. It replaces template branding with the actual application name.

**Independent Test**: Can be fully tested by checking that the title displays "Zettelfix", clicking it navigates to the specified URL, and it appears consistently across all navigation areas. The feature delivers value by establishing clear application identity.

**Acceptance Scenarios**:

1. **Given** a user views the navigation area, **When** they see the title/logo area, **Then** it displays "Zettelfix" as the label
2. **Given** a user views the "Zettelfix" title in navigation, **When** they click on it, **Then** they are navigated to https://zettelfix-preview.de
3. **Given** a user views the navigation, **When** they check both sidebar and header navigation, **Then** "Zettelfix" appears consistently in both locations
4. **Given** a user views the navigation on mobile, **When** they see the mobile menu, **Then** "Zettelfix" branding is displayed appropriately

---

### Edge Cases

- What happens when a user is not authenticated and views the dashboard tiles? (Tiles should display appropriate messaging)
- How does the navigation appear on mobile vs desktop? (Should be consistent across both)
- What happens if the external link (zettelfix-preview.de) is unavailable? (Link should still work, external site availability is not application's concern)
- How are existing dashboard tile counts calculated? (Should reuse existing logic)
- What happens if navigation components are used in other parts of the application? (Changes should be consistent across all uses)

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: System MUST display a landing page dashboard with "Todo List" and "Shopping Cart" tiles
- **FR-002**: System MUST reuse existing dashboard and tile components without modifying their core functionality
- **FR-003**: System MUST display current todo count and shopping list item count in tiles when user is authenticated
- **FR-004**: System MUST display appropriate messaging in tiles when user is not authenticated
- **FR-005**: System MUST provide navigation from dashboard tiles to their respective pages (Todo List and Shopping List)
- **FR-006**: System MUST remove GitHub repository link from navigation (both sidebar and header)
- **FR-007**: System MUST remove Laravel documentation link from navigation (both sidebar and header)
- **FR-008**: System MUST remove Laravel template title from navigation
- **FR-009**: System MUST display "Zettelfix" as the application title in navigation areas
- **FR-010**: System MUST make "Zettelfix" title clickable and link to https://zettelfix-preview.de
- **FR-011**: System MUST apply navigation changes consistently across sidebar and header navigation
- **FR-012**: System MUST apply navigation changes consistently across mobile and desktop views
- **FR-013**: System MUST maintain existing navigation functionality for application-specific items (Dashboard, Todos, Shopping)

### Key Entities *(include if feature involves data)*

- **Dashboard**: Represents the landing page view that displays overview tiles for Todo List and Shopping Cart features. Uses existing dashboard component and tile components.

- **Navigation Item**: Represents a navigation link in the sidebar or header. Includes title, href, and icon. Template-specific items (GitHub, Documentation) must be removed.

- **Application Branding**: Represents the application title "Zettelfix" displayed in navigation areas, linking to https://zettelfix-preview.de.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: Users can see both dashboard tiles (Todo List and Shopping Cart) within 2 seconds of landing page load
- **SC-002**: 100% of Laravel template links (GitHub, Documentation) are removed from navigation
- **SC-003**: "Zettelfix" title appears consistently in all navigation areas (sidebar and header)
- **SC-004**: Users can click "Zettelfix" title and navigate to https://zettelfix-preview.de successfully
- **SC-005**: Dashboard tiles display accurate counts for authenticated users
- **SC-006**: Dashboard tiles display appropriate messaging for unauthenticated users
- **SC-007**: Navigation changes are consistent across mobile and desktop views
- **SC-008**: Existing dashboard and tile functionality remains unchanged (reuse without modification)
- **SC-009**: Users can navigate from dashboard tiles to Todo List and Shopping List pages successfully

## Assumptions

- Existing dashboard component and tile components can be reused without modification
- Dashboard tile counts are calculated using existing logic from DashboardController
- Navigation components (AppSidebar, AppHeader) can be modified to remove template links
- "Zettelfix" branding should replace "Laravel Starter Kit" or similar template branding
- External link to https://zettelfix-preview.de should open in the same window/tab (standard link behavior)
- Navigation cleanup applies to both sidebar navigation and header navigation
- Mobile and desktop navigation should have consistent branding and link removal
- Existing application navigation items (Dashboard, Todos, Shopping) remain functional

