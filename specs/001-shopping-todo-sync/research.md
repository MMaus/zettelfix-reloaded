# Research: Shopping and Todo List Synchronization

**Date**: 2025-01-27  
**Feature**: Shopping and Todo List Synchronization

## Research Areas

### 1. Offline-First Architecture with Local Storage

**Decision**: Use Browser LocalStorage for offline data persistence, with IndexedDB as fallback for larger datasets.

**Rationale**: 
- LocalStorage provides simple key-value storage suitable for small to medium datasets (5-10MB limit)
- IndexedDB supports larger datasets and structured queries if needed
- Both are supported in all modern browsers
- No additional dependencies required
- Works completely offline without service workers initially

**Alternatives Considered**:
- Service Workers with Cache API: More complex, requires HTTPS, better for caching but overkill for simple local storage
- WebSQL: Deprecated, not recommended
- External libraries (localForage): Adds dependency, but provides unified API - may consider if IndexedDB needed

**Implementation Notes**:
- Use Vue composable `useLocalStorage` for consistent access
- Implement sync queue for offline changes
- Merge local data with server data on login

### 2. Multi-Device Synchronization Strategy

**Decision**: Implement polling-based synchronization with conflict resolution using "last write wins" strategy initially, with timestamps for ordering.

**Rationale**:
- Simpler than real-time WebSocket connections for MVP
- Works with Inertia.js page-based architecture
- Can be upgraded to WebSockets/Laravel Echo later if needed
- Conflict resolution can be enhanced with operational transforms if conflicts become frequent

**Alternatives Considered**:
- WebSockets (Laravel Echo + Pusher): Real-time but adds complexity and cost
- Server-Sent Events (SSE): One-way only, not suitable for bidirectional sync
- Long polling: More server resources, similar complexity to polling

**Implementation Notes**:
- Sync endpoint checks for changes since last sync timestamp
- Client sends local changes with timestamps
- Server merges changes, resolves conflicts (last write wins)
- Return updated data to client
- Sync every 10-30 seconds when online, or on user action

### 3. Conflict Resolution Strategy

**Decision**: Last write wins (LWW) with timestamp comparison. If same timestamp, server timestamp takes precedence.

**Rationale**:
- Simple to implement and understand
- Suitable for personal lists (low conflict probability)
- Can be enhanced later with more sophisticated strategies if needed
- Timestamps provide clear ordering

**Alternatives Considered**:
- Operational Transformation (OT): Complex, overkill for this use case
- CRDTs (Conflict-free Replicated Data Types): Complex, better for collaborative editing
- Manual conflict resolution: Better UX but requires more implementation

**Implementation Notes**:
- Use `updated_at` timestamps for comparison
- Store `synced_at` timestamp to track last successful sync
- If conflict detected, show user notification (future enhancement)
- For MVP, silently resolve using LWW

### 4. Data Merge Strategy (Local → Server on Login)

**Decision**: Merge local items with server items, avoiding duplicates by matching on content (title/name) and creation date within 1-hour window.

**Rationale**:
- Prevents data loss when user creates items offline then logs in
- Simple matching logic prevents duplicates
- Time window accounts for clock skew

**Alternatives Considered**:
- Replace local with server: Loses user's offline work
- Replace server with local: Loses data from other devices
- User chooses: Better UX but requires UI complexity

**Implementation Notes**:
- On login, send local items to server
- Server checks for duplicates (same content + creation date within 1 hour)
- Non-duplicates are created on server
- Client receives merged list

### 5. Permanent Login Implementation

**Decision**: Use Laravel Fortify's "remember me" functionality with long-lived refresh tokens stored in secure HTTP-only cookies.

**Rationale**:
- Laravel Fortify provides built-in "remember me" support
- Secure token storage in HTTP-only cookies prevents XSS attacks
- Tokens can be rotated for security
- Follows Laravel best practices

**Alternatives Considered**:
- JWT tokens in localStorage: Vulnerable to XSS attacks
- Session-based only: Requires frequent re-login, poor UX
- Custom token system: Reinvents wheel, less secure than Fortify

**Implementation Notes**:
- Configure Fortify to use "remember me" feature
- Set appropriate token expiration (30-90 days)
- Implement token refresh mechanism
- Handle token invalidation on logout

### 6. Shopping Basket State Management

**Decision**: Store basket state (items marked "in basket") in component state and sync to server only on checkout.

**Rationale**:
- Basket state is temporary and device-specific
- No need to sync basket state across devices
- Reduces sync complexity
- Better performance (no unnecessary syncs)

**Alternatives Considered**:
- Sync basket state: Adds complexity, not needed across devices
- Store in localStorage only: Lost on page refresh, poor UX

**Implementation Notes**:
- Basket state managed in Vue component
- Persist to localStorage for page refresh resilience
- On checkout, move items to history and remove from list
- Sync checkout action to server

### 7. Mobile-First Responsive Design

**Decision**: Use Tailwind CSS with mobile-first breakpoints, touch-friendly components, and responsive layouts.

**Rationale**:
- Tailwind CSS already in project
- Mobile-first approach ensures good mobile experience
- Responsive utilities handle desktop scaling
- Touch targets minimum 44x44px for accessibility

**Alternatives Considered**:
- Separate mobile app: Much more complex, not needed for MVP
- Desktop-first: Poor mobile experience
- Framework-specific UI library: Adds dependencies, Tailwind sufficient

**Implementation Notes**:
- Use Tailwind's responsive prefixes (sm:, md:, lg:)
- Ensure touch targets are large enough
- Test on actual mobile devices
- Use Vue components that adapt to screen size

### 8. Performance Optimization for Large Lists

**Decision**: Implement virtual scrolling for lists with 100+ items, pagination for server data, and lazy loading.

**Rationale**:
- Virtual scrolling renders only visible items, improving performance
- Pagination reduces initial load time
- Lazy loading improves perceived performance

**Alternatives Considered**:
- Render all items: Performance issues with large lists
- Server-side pagination only: Requires more server requests
- Infinite scroll: Can be combined with virtual scrolling

**Implementation Notes**:
- Use Vue virtual scrolling library (vue-virtual-scroller or similar)
- Implement pagination for shopping history library
- Lazy load images/icons if needed
- Optimize database queries with indexes

## Technical Decisions Summary

| Area | Decision | Rationale |
|------|----------|------------|
| Offline Storage | LocalStorage + IndexedDB fallback | Simple, native, no dependencies |
| Sync Strategy | Polling-based with timestamps | Simple, works with Inertia.js |
| Conflict Resolution | Last write wins | Simple, suitable for personal lists |
| Data Merge | Content + time window matching | Prevents duplicates, preserves data |
| Authentication | Laravel Fortify remember me | Secure, built-in, follows best practices |
| Basket State | Component state + localStorage | Temporary, device-specific |
| Responsive Design | Tailwind mobile-first | Already in project, proven approach |
| Performance | Virtual scrolling + pagination | Handles large lists efficiently |

## Open Questions Resolved

1. **Q**: How to handle offline data when user logs in?  
   **A**: Merge local items with server items, avoiding duplicates by matching content and creation time.

2. **Q**: How often should synchronization occur?  
   **A**: Every 10-30 seconds when online, or immediately on user actions (create, update, delete).

3. **Q**: Should basket state sync across devices?  
   **A**: No, basket state is temporary and device-specific. Only checkout action syncs.

4. **Q**: How to handle very long lists?  
   **A**: Virtual scrolling for rendering, pagination for server data, lazy loading.

5. **Q**: What happens if user edits same item on two devices?  
   **A**: Last write wins based on timestamp. Future enhancement: show conflict notification.

## Next Steps

- Implement data models based on entities defined in spec
- Design API contracts (Inertia page props, not REST endpoints)
- Create quickstart guide for developers
- Set up test structure following TDD principles

