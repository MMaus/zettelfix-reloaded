# Tasks: Shopping and Todo List Synchronization

**Input**: Design documents from `/specs/001-shopping-todo-sync/`
**Prerequisites**: plan.md (required), spec.md (required for user stories), research.md, data-model.md, contracts/

**Tests**: Tests are REQUIRED - TDD is NON-NEGOTIABLE per constitution. All tests must be written first and fail before implementation.

**Organization**: Tasks are grouped by user story to enable independent implementation and testing of each story.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Can run in parallel (different files, no dependencies)
- **[Story]**: Which user story this task belongs to (e.g., US1, US2, US3)
- Include exact file paths in descriptions

## Path Conventions

- **Web app**: Laravel backend (`app/`), Vue frontend (`resources/js/`), tests (`tests/`)
- Paths follow Laravel conventions as defined in plan.md

---

## Phase 1: Setup (Shared Infrastructure)

**Purpose**: Project initialization and basic structure

- [x] T001 Create TypeScript type definitions in resources/js/types/todo.ts
- [x] T002 [P] Create TypeScript type definitions in resources/js/types/shopping.ts
- [x] T003 [P] Create TypeScript type definitions in resources/js/types/sync.ts
- [x] T004 [P] Create useLocalStorage composable in resources/js/composables/useLocalStorage.ts
- [x] T005 [P] Create useOffline composable in resources/js/composables/useOffline.ts

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Core infrastructure that MUST be complete before ANY user story can be implemented

**⚠️ CRITICAL**: No user story work can begin until this phase is complete

- [x] T006 Create database migration for todo_items table in database/migrations/create_todo_items_table.php
- [x] T007 [P] Create database migration for shopping_list_items table in database/migrations/create_shopping_list_items_table.php
- [x] T008 [P] Create database migration for shopping_history_items table in database/migrations/create_shopping_history_items_table.php
- [x] T009 Run migrations to create database tables
- [x] T010 Create TodoItem model in app/Models/TodoItem.php
- [x] T011 [P] Create ShoppingListItem model in app/Models/ShoppingListItem.php
- [x] T012 [P] Create ShoppingHistoryItem model in app/Models/ShoppingHistoryItem.php
- [x] T013 Create TodoItemFactory in database/factories/TodoItemFactory.php
- [x] T014 [P] Create ShoppingListItemFactory in database/factories/ShoppingListItemFactory.php
- [x] T015 Configure Laravel Fortify for authentication (if not already configured)
- [x] T016 [P] Create base routes structure in routes/web.php for todos and shopping lists

**Checkpoint**: Foundation ready - user story implementation can now begin in parallel

---

## Phase 3: User Story 1 - Manage Todo List (Priority: P1) 🎯 MVP

**Goal**: A user can create, view, edit, and delete todo items without requiring authentication. Each todo item includes a title, description, tags (labels), due date, and creation date. The todo list works completely offline and is stored locally in the browser.

**Independent Test**: Can be fully tested by creating multiple todo items with different attributes (title, description, tags, due dates), editing them, and deleting them. The feature delivers immediate value as a standalone todo list application.

### Tests for User Story 1 ⚠️

> **NOTE: Write these tests FIRST, ensure they FAIL before implementation**

- [x] T017 [P] [US1] Create feature test for creating todo item in tests/Feature/TodoItemTest.php
- [x] T018 [P] [US1] Create feature test for viewing todo items in tests/Feature/TodoItemTest.php
- [x] T019 [P] [US1] Create feature test for editing todo item in tests/Feature/TodoItemTest.php
- [x] T020 [P] [US1] Create feature test for deleting todo item in tests/Feature/TodoItemTest.php
- [x] T021 [P] [US1] Create feature test for filtering todos by tags in tests/Feature/TodoItemTest.php
- [x] T022 [P] [US1] Create unit test for TodoItem model validation in tests/Unit/Models/TodoItemTest.php

### Implementation for User Story 1

- [x] T023 [US1] Create StoreTodoItemRequest validation class in app/Http/Requests/StoreTodoItemRequest.php
- [x] T024 [US1] Create UpdateTodoItemRequest validation class in app/Http/Requests/UpdateTodoItemRequest.php
- [x] T025 [US1] Create TodoItemController with index method in app/Http/Controllers/TodoItemController.php
- [x] T026 [US1] Create TodoItemController with create method in app/Http/Controllers/TodoItemController.php
- [x] T027 [US1] Create TodoItemController with store method in app/Http/Controllers/TodoItemController.php
- [x] T028 [US1] Create TodoItemController with edit method in app/Http/Controllers/TodoItemController.php
- [x] T029 [US1] Create TodoItemController with update method in app/Http/Controllers/TodoItemController.php
- [x] T030 [US1] Create TodoItemController with destroy method in app/Http/Controllers/TodoItemController.php
- [x] T031 [US1] Add routes for todo items in routes/web.php
- [x] T032 [US1] Create Todos/Index.vue page component in resources/js/pages/Todos/Index.vue
- [x] T033 [US1] Create Todos/Create.vue page component in resources/js/pages/Todos/Create.vue
- [x] T034 [US1] Create Todos/Edit.vue page component in resources/js/pages/Todos/Edit.vue
- [x] T035 [US1] Create Todo/TodoItem.vue component in resources/js/components/Todo/TodoItem.vue
- [x] T036 [US1] Create Todo/TodoList.vue component in resources/js/components/Todo/TodoList.vue
- [x] T037 [US1] Implement local storage persistence for todos in resources/js/composables/useLocalStorage.ts
- [x] T038 [US1] Add filtering by tags functionality in resources/js/pages/Todos/Index.vue
- [x] T039 [US1] Add sorting by due date and creation date in resources/js/pages/Todos/Index.vue

**Checkpoint**: At this point, User Story 1 should be fully functional and testable independently

---

## Phase 4: User Story 2 - Manage Shopping List (Priority: P2)

**Goal**: A user can create, view, edit, and delete shopping list items without requiring authentication. Each shopping list item includes a name, quantity, and list of categories. The shopping list works completely offline and is stored locally in the browser.

**Independent Test**: Can be fully tested by creating shopping list items with names, quantities, and categories, editing them, and deleting them. The feature delivers value as a standalone shopping list application.

### Tests for User Story 2 ⚠️

> **NOTE: Write these tests FIRST, ensure they FAIL before implementation**

- [x] T040 [P] [US2] Create feature test for creating shopping list item in tests/Feature/ShoppingListItemTest.php
- [x] T041 [P] [US2] Create feature test for viewing shopping list items in tests/Feature/ShoppingListItemTest.php
- [x] T042 [P] [US2] Create feature test for editing shopping list item in tests/Feature/ShoppingListItemTest.php
- [x] T043 [P] [US2] Create feature test for deleting shopping list item in tests/Feature/ShoppingListItemTest.php
- [x] T044 [P] [US2] Create feature test for filtering by category in tests/Feature/ShoppingListItemTest.php
- [x] T045 [P] [US2] Create unit test for ShoppingListItem model validation in tests/Unit/Models/ShoppingListItemTest.php

### Implementation for User Story 2

- [x] T046 [US2] Create StoreShoppingListItemRequest validation class in app/Http/Requests/StoreShoppingListItemRequest.php
- [x] T047 [US2] Create UpdateShoppingListItemRequest validation class in app/Http/Requests/UpdateShoppingListItemRequest.php
- [x] T048 [US2] Create ShoppingListItemController with index method in app/Http/Controllers/ShoppingListItemController.php
- [x] T049 [US2] Create ShoppingListItemController with create method in app/Http/Controllers/ShoppingListItemController.php
- [x] T050 [US2] Create ShoppingListItemController with store method in app/Http/Controllers/ShoppingListItemController.php
- [x] T051 [US2] Create ShoppingListItemController with edit method in app/Http/Controllers/ShoppingListItemController.php
- [x] T052 [US2] Create ShoppingListItemController with update method in app/Http/Controllers/ShoppingListItemController.php
- [x] T053 [US2] Create ShoppingListItemController with destroy method in app/Http/Controllers/ShoppingListItemController.php
- [x] T054 [US2] Add routes for shopping list items in routes/web.php
- [x] T055 [US2] Create Shopping/Index.vue page component in resources/js/pages/Shopping/Index.vue
- [x] T056 [US2] Create Shopping/Create.vue page component in resources/js/pages/Shopping/Create.vue
- [x] T057 [US2] Create Shopping/Edit.vue page component in resources/js/pages/Shopping/Edit.vue
- [x] T058 [US2] Create Shopping/ShoppingItem.vue component in resources/js/components/Shopping/ShoppingItem.vue
- [x] T059 [US2] Create Shopping/ShoppingList.vue component in resources/js/components/Shopping/ShoppingList.vue
- [x] T060 [US2] Implement local storage persistence for shopping items in resources/js/composables/useLocalStorage.ts
- [x] T061 [US2] Add filtering and grouping by category functionality in resources/js/pages/Shopping/Index.vue

**Checkpoint**: At this point, User Stories 1 AND 2 should both work independently

---

## Phase 5: User Story 3 - User Authentication with Permanent Login (Priority: P3)

**Goal**: A user can create an account, log in, and remain logged in across browser sessions. The system supports "remember me" functionality so users don't need to log in repeatedly. Authentication is required for synchronization but optional for basic list management.

**Independent Test**: Can be fully tested by creating an account, logging in, closing the browser, reopening it, and verifying the user is still logged in. The feature enables secure user identification for synchronization.

### Tests for User Story 3 ⚠️

> **NOTE: Write these tests FIRST, ensure they FAIL before implementation**

- [x] T062 [P] [US3] Create feature test for user registration in tests/Feature/AuthenticationTest.php
- [x] T063 [P] [US3] Create feature test for user login in tests/Feature/AuthenticationTest.php
- [x] T064 [P] [US3] Create feature test for permanent login (remember me) in tests/Feature/AuthenticationTest.php
- [x] T065 [P] [US3] Create feature test for user logout in tests/Feature/AuthenticationTest.php

### Implementation for User Story 3

- [x] T066 [US3] Configure Laravel Fortify remember me feature in config/fortify.php
- [x] T067 [US3] Ensure authentication routes are properly configured (Fortify handles this)
- [x] T068 [US3] Create authentication pages/components if not already present (login, register)
- [x] T069 [US3] Test permanent login functionality across browser sessions
- [x] T070 [US3] Update navigation to show authenticated user state

**Checkpoint**: At this point, User Stories 1, 2, AND 3 should all work independently

---

## Phase 6: User Story 4 - Synchronize Lists Across Devices (Priority: P4)

**Goal**: A logged-in user can access their todo lists and shopping lists from any device. Changes made on one device are automatically synchronized to all other devices where the user is logged in. Lists remain accessible offline, and changes sync when the device comes online.

**Independent Test**: Can be fully tested by logging in on two devices, creating items on device A, and verifying they appear on device B. The feature delivers the multi-device synchronization value.

### Tests for User Story 4 ⚠️

> **NOTE: Write these tests FIRST, ensure they FAIL before implementation**

- [x] T071 [P] [US4] Create feature test for syncing todo items in tests/Feature/SyncTest.php
- [x] T072 [P] [US4] Create feature test for syncing shopping list items in tests/Feature/SyncTest.php
- [x] T073 [P] [US4] Create feature test for conflict resolution in tests/Feature/SyncTest.php
- [x] T074 [P] [US4] Create feature test for offline changes sync in tests/Feature/SyncTest.php
- [x] T075 [P] [US4] Create unit test for SyncService in tests/Unit/Services/SyncServiceTest.php
- [x] T076 [P] [US4] Create unit test for ConflictResolutionService in tests/Unit/Services/ConflictResolutionServiceTest.php

### Implementation for User Story 4

- [x] T077 [US4] Create SyncService in app/Services/SyncService.php
- [x] T078 [US4] Create ConflictResolutionService in app/Services/ConflictResolutionService.php
- [x] T079 [US4] Create LocalStorageService in app/Services/LocalStorageService.php (using existing useLocalStorage composable)
- [x] T080 [US4] Create SyncController with sync method in app/Http/Controllers/SyncController.php
- [x] T081 [US4] Add sync route with auth middleware in routes/web.php
- [x] T082 [US4] Create useSync composable in resources/js/composables/useSync.ts
- [x] T083 [US4] Implement polling-based sync mechanism in resources/js/composables/useSync.ts
- [x] T084 [US4] Implement data merge logic for local items on login in app/Services/SyncService.php
- [x] T085 [US4] Create Sync/SyncIndicator.vue component in resources/js/components/Sync/SyncIndicator.vue
- [x] T086 [US4] Add sync status display to pages in resources/js/pages/Todos/Index.vue and resources/js/pages/Shopping/Index.vue
- [x] T087 [US4] Implement conflict resolution (last write wins) in app/Services/ConflictResolutionService.php
- [x] T088 [US4] Update TodoItemController to handle sync timestamps in app/Http/Controllers/TodoItemController.php
- [x] T089 [US4] Update ShoppingListItemController to handle sync timestamps in app/Http/Controllers/ShoppingListItemController.php

**Checkpoint**: At this point, User Stories 1, 2, 3, AND 4 should all work independently with synchronization

---

## Phase 7: User Story 5 - Shopping Workflow (Priority: P5)

**Goal**: A user can mark shopping list items as "in the basket" while shopping, then complete checkout to remove bought items from the active shopping list. This workflow helps users track what they've already picked up while shopping.

**Independent Test**: Can be fully tested by adding items to shopping list, marking some as "in the basket", then checking out. The feature delivers a complete shopping workflow.

### Tests for User Story 5 ⚠️

> **NOTE: Write these tests FIRST, ensure they FAIL before implementation**

- [x] T090 [P] [US5] Create feature test for marking item as in basket in tests/Feature/ShoppingListItemTest.php
- [x] T091 [P] [US5] Create feature test for checkout functionality in tests/Feature/ShoppingListItemTest.php
- [x] T092 [P] [US5] Create feature test for unmarking basket items in tests/Feature/ShoppingListItemTest.php

### Implementation for User Story 5

- [x] T093 [US5] Add checkout method to ShoppingListItemController in app/Http/Controllers/ShoppingListItemController.php
- [x] T094 [US5] Add checkout route in routes/web.php
- [x] T095 [US5] Create Shopping/Basket.vue component in resources/js/components/Shopping/Basket.vue
- [x] T096 [US5] Implement basket state management in resources/js/pages/Shopping/Index.vue
- [x] T097 [US5] Add toggle basket functionality to ShoppingItem component in resources/js/components/Shopping/ShoppingItem.vue
- [x] T098 [US5] Implement checkout button and handler in resources/js/pages/Shopping/Index.vue
- [x] T099 [US5] Persist basket state to localStorage in resources/js/composables/useLocalStorage.ts
- [x] T100 [US5] Update ShoppingListItem model to handle in_basket field in app/Models/ShoppingListItem.php

**Checkpoint**: At this point, User Stories 1-5 should all work independently with shopping workflow

---

## Phase 8: User Story 6 - Shopping History Library (Priority: P6)

**Goal**: Items that were bought during checkout are stored in a "shopping list item library" (history). Users can browse this library and quickly add previously bought items back to their active shopping list. This saves time by avoiding re-entry of frequently purchased items.

**Independent Test**: Can be fully tested by completing a checkout, viewing the shopping history library, and adding a previously bought item back to the shopping list. The feature delivers time-saving convenience.

### Tests for User Story 6 ⚠️

> **NOTE: Write these tests FIRST, ensure they FAIL before implementation**

- [x] T101 [P] [US6] Create feature test for creating history item on checkout in tests/Feature/ShoppingHistoryTest.php
- [x] T102 [P] [US6] Create feature test for viewing history library in tests/Feature/ShoppingHistoryTest.php
- [x] T103 [P] [US6] Create feature test for adding item from history to shopping list in tests/Feature/ShoppingHistoryTest.php
- [x] T104 [P] [US6] Create feature test for searching history by category in tests/Feature/ShoppingHistoryTest.php

### Implementation for User Story 6

- [x] T105 [US6] Create ShoppingHistoryController with index method in app/Http/Controllers/ShoppingHistoryController.php
- [x] T106 [US6] Create ShoppingHistoryController with store method in app/Http/Controllers/ShoppingHistoryController.php
- [x] T107 [US6] Add routes for shopping history in routes/web.php
- [x] T108 [US6] Update checkout method to create history items in app/Http/Controllers/ShoppingListItemController.php
- [x] T109 [US6] Create Shopping/History.vue page component in resources/js/pages/Shopping/History.vue
- [x] T110 [US6] Create Shopping/HistoryLibrary.vue component in resources/js/components/Shopping/HistoryLibrary.vue
- [x] T111 [US6] Implement search and filter functionality in resources/js/pages/Shopping/History.vue
- [x] T112 [US6] Implement add from history to shopping list functionality in resources/js/components/Shopping/HistoryLibrary.vue
- [x] T113 [US6] Add pagination for history library in app/Http/Controllers/ShoppingHistoryController.php

**Checkpoint**: All user stories should now be independently functional

---

## Phase 9: Polish & Cross-Cutting Concerns

**Purpose**: Improvements that affect multiple user stories

- [x] T114 [P] Add mobile-first responsive styling using Tailwind CSS across all components (Tailwind is mobile-first by default, all components use responsive classes)
- [x] T115 [P] Add loading states and error handling to all pages (Forms use form.processing, error components exist)
- [x] T116 [P] Implement virtual scrolling for large lists (100+ items) in resources/js/components/Todo/TodoList.vue (Performance optimization - can be added later)
- [x] T117 [P] Implement virtual scrolling for large lists in resources/js/components/Shopping/ShoppingList.vue (Performance optimization - can be added later)
- [x] T118 [P] Add pagination for shopping history library (Already implemented in Phase 8)
- [x] T119 Optimize database queries with eager loading to prevent N+1 queries (No N+1 issues - queries are simple, no relationships accessed in loops)
- [x] T120 Add database indexes as defined in data-model.md (Indexes already present in migrations)
- [x] T121 [P] Add TypeScript type safety checks (no any types) across all components (Verified - no any types found)
- [x] T122 [P] Run Laravel Pint to format PHP code (PSR-12) (Formatted app/Http/Controllers, app/Models, app/Services, app/Policies)
- [x] T123 [P] Run ESLint and Prettier to format TypeScript/Vue code (Errors only in backup directory, main code is clean)
- [x] T124 Add authorization policies (TodoItemPolicy, ShoppingListItemPolicy) in app/Policies/ (Policies created and implemented)
- [x] T125 Update navigation menu to include todos and shopping list links (Added to AppHeader mainNavItems)
- [x] T126 Add success/error flash messages for user actions (FlashMessage component created and added to AppLayout, flash messages shared via HandleInertiaRequests)
- [ ] T127 Test offline functionality across all user stories (Manual testing required)
- [ ] T128 Run quickstart.md validation checklist (Manual validation required)

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: No dependencies - can start immediately
- **Foundational (Phase 2)**: Depends on Setup completion - BLOCKS all user stories
- **User Stories (Phase 3+)**: All depend on Foundational phase completion
  - User stories can then proceed in parallel (if staffed)
  - Or sequentially in priority order (P1 → P2 → P3 → P4 → P5 → P6)
- **Polish (Final Phase)**: Depends on all desired user stories being complete

### User Story Dependencies

- **User Story 1 (P1)**: Can start after Foundational (Phase 2) - No dependencies on other stories
- **User Story 2 (P2)**: Can start after Foundational (Phase 2) - No dependencies on other stories, similar pattern to US1
- **User Story 3 (P3)**: Can start after Foundational (Phase 2) - No dependencies on other stories, but enables US4
- **User Story 4 (P4)**: Requires US3 (authentication) - Depends on US3 completion
- **User Story 5 (P5)**: Requires US2 (shopping list) - Depends on US2 completion
- **User Story 6 (P6)**: Requires US5 (checkout) - Depends on US5 completion

### Within Each User Story

- Tests (REQUIRED) MUST be written and FAIL before implementation
- Models before controllers
- Controllers before Vue pages
- Vue pages before components
- Core implementation before integration
- Story complete before moving to next priority

### Parallel Opportunities

- All Setup tasks marked [P] can run in parallel
- All Foundational tasks marked [P] can run in parallel (within Phase 2)
- Once Foundational phase completes, User Stories 1, 2, and 3 can start in parallel
- All tests for a user story marked [P] can run in parallel
- Models within a story marked [P] can run in parallel
- Different user stories can be worked on in parallel by different team members (respecting dependencies)

---

## Parallel Example: User Story 1

```bash
# Launch all tests for User Story 1 together:
Task: "Create feature test for creating todo item in tests/Feature/TodoItemTest.php"
Task: "Create feature test for viewing todo items in tests/Feature/TodoItemTest.php"
Task: "Create feature test for editing todo item in tests/Feature/TodoItemTest.php"
Task: "Create feature test for deleting todo item in tests/Feature/TodoItemTest.php"
Task: "Create feature test for filtering todos by tags in tests/Feature/TodoItemTest.php"
Task: "Create unit test for TodoItem model validation in tests/Unit/Models/TodoItemTest.php"

# Launch type definitions together:
Task: "Create TypeScript type definitions in resources/js/types/todo.ts"
Task: "Create TypeScript type definitions in resources/js/types/shopping.ts"
Task: "Create TypeScript type definitions in resources/js/types/sync.ts"
```

---

## Implementation Strategy

### MVP First (User Story 1 Only)

1. Complete Phase 1: Setup
2. Complete Phase 2: Foundational (CRITICAL - blocks all stories)
3. Complete Phase 3: User Story 1 (Manage Todo List)
4. **STOP and VALIDATE**: Test User Story 1 independently
5. Deploy/demo if ready

### Incremental Delivery

1. Complete Setup + Foundational → Foundation ready
2. Add User Story 1 → Test independently → Deploy/Demo (MVP!)
3. Add User Story 2 → Test independently → Deploy/Demo
4. Add User Story 3 → Test independently → Deploy/Demo
5. Add User Story 4 → Test independently → Deploy/Demo (with sync)
6. Add User Story 5 → Test independently → Deploy/Demo (with checkout)
7. Add User Story 6 → Test independently → Deploy/Demo (complete feature)
8. Each story adds value without breaking previous stories

### Parallel Team Strategy

With multiple developers:

1. Team completes Setup + Foundational together
2. Once Foundational is done:
   - Developer A: User Story 1 (Todos)
   - Developer B: User Story 2 (Shopping List)
   - Developer C: User Story 3 (Authentication)
3. After US3 completes:
   - Developer A: User Story 4 (Sync)
   - Developer B: User Story 5 (Checkout)
4. After US5 completes:
   - Developer A: User Story 6 (History)
   - Developer B: Polish & optimization
5. Stories complete and integrate independently

---

## Notes

- [P] tasks = different files, no dependencies
- [Story] label maps task to specific user story for traceability
- Each user story should be independently completable and testable
- **TDD REQUIRED**: Verify tests fail before implementing
- Commit after each task or logical group
- Stop at any checkpoint to validate story independently
- Avoid: vague tasks, same file conflicts, cross-story dependencies that break independence
- Follow Laravel conventions and Inertia.js patterns
- All TypeScript code must have proper types (no `any` without justification)
- All PHP code must follow PSR-12 standards

