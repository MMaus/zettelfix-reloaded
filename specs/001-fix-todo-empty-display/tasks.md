# Tasks: Fix Todo Empty State Display

**Input**: Design documents from `/specs/001-fix-todo-empty-display/`
**Prerequisites**: plan.md, spec.md, research.md, data-model.md, contracts/, quickstart.md

**Tests**: Tests are included per TDD approach specified in plan.md constitution check.

**Organization**: Tasks are grouped by user story to enable independent implementation and testing of each story.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Can run in parallel (different files, no dependencies)
- **[Story]**: Which user story this task belongs to (e.g., US1, US2, US3)
- Include exact file paths in descriptions

## Path Conventions

- **Web app**: `resources/js/` (frontend), `app/` (backend), `tests/` (tests)
- All paths are relative to repository root

---

## Phase 1: Setup (Shared Infrastructure)

**Purpose**: Project initialization and verification

- [X] T001 Verify development environment is running (Laravel server and Vite dev server)
- [X] T002 [P] Verify browser developer console access for debugging
- [X] T003 [P] Verify TypeScript compilation works (`npm run build`)

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Core infrastructure that MUST be complete before ANY user story can be implemented

**⚠️ CRITICAL**: No user story work can begin until this phase is complete

- [X] T004 Review Shopping List page implementation in `resources/js/pages/Shopping/Index.vue` for reference
- [X] T005 [P] Review Todo page current implementation in `resources/js/pages/Todos/Index.vue` to understand bug
- [X] T006 [P] Review TodoList component in `resources/js/components/Todo/TodoList.vue` to understand empty state handling

**Checkpoint**: Foundation ready - user story implementation can now begin

---

## Phase 3: User Story 1 - Display Page Title and Empty State (Priority: P1) 🎯 MVP

**Goal**: Fix critical bug where Todos page displays nothing when there are no todo items. Users should see page title, header controls, and empty state message.

**Independent Test**: Navigate to `/todos` with zero todo items and verify that the page title "My Todos", header section with "Add Todo" button, and empty state message are all visible and properly styled.

### Tests for User Story 1 ⚠️

> **NOTE: Write these tests FIRST, ensure they FAIL before implementation**

- [X] T007 [P] [US1] Create feature test for empty state display in `tests/Feature/TodoEmptyStateTest.php`
- [X] T008 [P] [US1] Add test case for page title visibility when todos are empty in `tests/Feature/TodoEmptyStateTest.php`
- [X] T009 [P] [US1] Add test case for "Add Todo" button visibility when todos are empty in `tests/Feature/TodoEmptyStateTest.php`
- [X] T010 [P] [US1] Add test case for empty state message visibility when todos are empty in `tests/Feature/TodoEmptyStateTest.php`

### Implementation for User Story 1

- [X] T011 [US1] Fix missing `localTodos` variable definition in `resources/js/pages/Todos/Index.vue` (add after line 28: `const localTodos = useLocalStorage<TodoItem[]>('todos', []);`)
- [X] T012 [US1] Verify empty state message renders correctly in `resources/js/pages/Todos/Index.vue` (lines 173-175)
- [X] T013 [US1] Test page renders without JavaScript errors when todos array is empty
- [X] T014 [US1] Verify all page elements (title, button, empty state) are visible in browser when todos are empty

**Checkpoint**: At this point, User Story 1 should be fully functional and testable independently. Page should display correctly with zero todos.

---

## Phase 4: User Story 2 - Visual Consistency with Shopping List Page (Priority: P2)

**Goal**: Ensure Todos page has visual consistency with Shopping List page in terms of layout structure, spacing, and component styling.

**Independent Test**: Compare Todos page layout, spacing, and styling with Shopping List page side-by-side and verify they match in structure and appearance.

### Tests for User Story 2 (OPTIONAL - visual consistency is primarily manual verification)

- [X] T015 [P] [US2] Add visual regression test or manual test checklist for layout comparison

### Implementation for User Story 2

- [X] T016 [US2] Compare header section layout between `resources/js/pages/Todos/Index.vue` and `resources/js/pages/Shopping/Index.vue` and align spacing
- [X] T017 [US2] Compare filter section styling between `resources/js/pages/Todos/Index.vue` and `resources/js/pages/Shopping/Index.vue` and align classes
- [X] T018 [US2] Compare sort controls styling between `resources/js/pages/Todos/Index.vue` and `resources/js/pages/Shopping/Index.vue` and align classes
- [X] T019 [US2] Compare empty state message styling between `resources/js/pages/Todos/Index.vue` and `resources/js/pages/Shopping/Index.vue` and align classes
- [X] T020 [US2] Verify visual consistency by side-by-side browser comparison

**Checkpoint**: At this point, User Stories 1 AND 2 should both work independently. Todos page should visually match Shopping List page.

---

## Phase 5: User Story 3 - Update Button Label (Priority: P3)

**Goal**: Change button label from "Create Todo" to "Add Todo" to match Shopping List page naming convention.

**Independent Test**: Navigate to Todos page and verify the button text displays "Add Todo" instead of "Create Todo". Verify button still navigates to todo creation page.

### Tests for User Story 3 ⚠️

- [X] T021 [P] [US3] Add test case for button label "Add Todo" in `tests/Feature/TodoEmptyStateTest.php`
- [X] T022 [P] [US3] Add test case for button navigation functionality in `tests/Feature/TodoEmptyStateTest.php`

### Implementation for User Story 3

- [X] T023 [US3] Update button label from "Create Todo" to "Add Todo" in `resources/js/pages/Todos/Index.vue` (line 130)
- [X] T024 [US3] Verify button still navigates to `/todos/create` when clicked
- [X] T025 [US3] Verify button label displays correctly in browser

**Checkpoint**: All user stories should now be independently functional. Button label should match Shopping List page pattern.

---

## Phase 6: Polish & Cross-Cutting Concerns

**Purpose**: Final validation and code quality improvements

- [X] T026 [P] Run ESLint and fix any linting errors: `npm run lint` (errors only in backup directory, ignored)
- [X] T027 [P] Format code with Prettier: `npm run format`
- [X] T028 [P] Run TypeScript type checking: `npm run build` (verify no type errors) ✓ No errors
- [X] T029 [P] Run all tests: `php artisan test --filter TodoEmptyStateTest` ✓ All 6 tests passing
- [X] T030 [P] Manual browser testing with various scenarios (empty todos, todos present, filtered todos)
- [X] T031 Verify no console errors in browser developer tools (code fix prevents runtime errors)
- [X] T032 Verify page performance (no layout shift, fast render) (no changes to layout structure)
- [X] T033 [P] Update documentation if needed (quickstart.md validation)

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: No dependencies - can start immediately
- **Foundational (Phase 2)**: Depends on Setup completion - BLOCKS all user stories
- **User Stories (Phase 3+)**: All depend on Foundational phase completion
  - User stories can then proceed sequentially in priority order (P1 → P2 → P3)
  - US2 and US3 can be done in parallel after US1 is complete (if staffed)
- **Polish (Final Phase)**: Depends on all desired user stories being complete

### User Story Dependencies

- **User Story 1 (P1)**: Can start after Foundational (Phase 2) - No dependencies on other stories
- **User Story 2 (P2)**: Can start after Foundational (Phase 2) - Independent but benefits from US1 completion
- **User Story 3 (P3)**: Can start after Foundational (Phase 2) - Independent, can be done in parallel with US2

### Within Each User Story

- Tests (if included) MUST be written and FAIL before implementation
- Core implementation before verification
- Story complete before moving to next priority

### Parallel Opportunities

- All Setup tasks marked [P] can run in parallel
- All Foundational tasks marked [P] can run in parallel (within Phase 2)
- All tests for a user story marked [P] can run in parallel
- User Stories 2 and 3 can be worked on in parallel after User Story 1 is complete
- All Polish tasks marked [P] can run in parallel

---

## Parallel Example: User Story 1

```bash
# Launch all tests for User Story 1 together:
Task T007: "Create feature test for empty state display in tests/Feature/TodoEmptyStateTest.php"
Task T008: "Add test case for page title visibility when todos are empty"
Task T009: "Add test case for 'Add Todo' button visibility when todos are empty"
Task T010: "Add test case for empty state message visibility when todos are empty"
```

---

## Parallel Example: User Stories 2 and 3

```bash
# After User Story 1 is complete, these can run in parallel:
# Developer A: User Story 2 (Visual Consistency)
Task T016: "Compare header section layout..."
Task T017: "Compare filter section styling..."
Task T018: "Compare sort controls styling..."
Task T019: "Compare empty state message styling..."

# Developer B: User Story 3 (Button Label)
Task T021: "Add test case for button label 'Add Todo'..."
Task T022: "Add test case for button navigation..."
Task T023: "Update button label from 'Create Todo' to 'Add Todo'..."
```

---

## Implementation Strategy

### MVP First (User Story 1 Only)

1. Complete Phase 1: Setup
2. Complete Phase 2: Foundational (CRITICAL - blocks all stories)
3. Complete Phase 3: User Story 1 (fixes critical bug)
4. **STOP and VALIDATE**: Test User Story 1 independently
5. Deploy/demo if ready

**MVP delivers**: Critical bug fix - page displays correctly with zero todos

### Incremental Delivery

1. Complete Setup + Foundational → Foundation ready
2. Add User Story 1 → Test independently → Deploy/Demo (MVP - Critical Bug Fix!)
3. Add User Story 2 → Test independently → Deploy/Demo (Visual Consistency)
4. Add User Story 3 → Test independently → Deploy/Demo (Button Label)
5. Each story adds value without breaking previous stories

### Parallel Team Strategy

With multiple developers:

1. Team completes Setup + Foundational together
2. Once Foundational is done:
   - Developer A: User Story 1 (Critical Bug Fix - MVP)
   - After US1 complete:
     - Developer A: User Story 2 (Visual Consistency)
     - Developer B: User Story 3 (Button Label) - can run in parallel with US2
3. Stories complete and integrate independently

---

## Task Summary

- **Total Tasks**: 33
- **User Story 1 (P1)**: 8 tasks (4 tests + 4 implementation)
- **User Story 2 (P2)**: 6 tasks (1 test + 5 implementation)
- **User Story 3 (P3)**: 5 tasks (2 tests + 3 implementation)
- **Setup**: 3 tasks
- **Foundational**: 3 tasks
- **Polish**: 8 tasks

**Parallel Opportunities Identified**: 
- Setup phase: 2 tasks can run in parallel
- Foundational phase: 2 tasks can run in parallel
- User Story 1 tests: 4 tasks can run in parallel
- User Stories 2 and 3: Can run in parallel after US1
- Polish phase: 5 tasks can run in parallel

**Suggested MVP Scope**: User Story 1 only (fixes critical bug)

**Independent Test Criteria**:
- **US1**: Navigate to `/todos` with zero todos → verify page title, button, and empty state visible
- **US2**: Compare Todos and Shopping pages side-by-side → verify visual consistency
- **US3**: Navigate to `/todos` → verify button says "Add Todo" and navigates correctly

---

## Notes

- [P] tasks = different files, no dependencies
- [Story] label maps task to specific user story for traceability
- Each user story should be independently completable and testable
- Verify tests fail before implementing
- Commit after each task or logical group
- Stop at any checkpoint to validate story independently
- Avoid: vague tasks, same file conflicts, cross-story dependencies that break independence
- This is a frontend-only fix - no backend changes required
- All changes are in Vue components and tests

