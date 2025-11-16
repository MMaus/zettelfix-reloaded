# Tasks: Disable CSRF Token Check

**Input**: Design documents from `/specs/001-disable-csrf/`
**Prerequisites**: plan.md (required), spec.md (required for user stories), research.md, data-model.md, contracts/

**Tests**: Tests are optional for this feature. Verification tasks are included to ensure CSRF is properly disabled.

**Organization**: Tasks are grouped by user story to enable independent implementation and testing of each story.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Can run in parallel (different files, no dependencies)
- **[Story]**: Which user story this task belongs to (e.g., US1, US2, US3)
- Include exact file paths in descriptions

## Path Conventions

- **Web app**: Laravel backend structure at repository root
- **Configuration**: `bootstrap/app.php`
- **Documentation**: `.specify/memory/constitution.md`
- **Tests**: `tests/Feature/`

---

## Phase 1: User Story 1 - Application Accepts Requests Without CSRF Tokens (Priority: P1) 🎯 MVP

**Goal**: Disable CSRF token validation middleware so that POST, PUT, PATCH, and DELETE requests are accepted without CSRF tokens.

**Independent Test**: Make POST/PUT/DELETE requests without CSRF tokens and verify they are accepted and processed successfully (no 419 CSRF Token Mismatch errors).

### Implementation for User Story 1

- [x] T001 [US1] Disable CSRF middleware by adding `withoutMiddleware()` call in `bootstrap/app.php` to exclude `\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class`
- [x] T002 [US1] Add comment explaining CSRF disabling in `bootstrap/app.php` middleware configuration
- [x] T003 [US1] Verify middleware configuration syntax is correct in `bootstrap/app.php`

**Checkpoint**: At this point, User Story 1 should be fully functional - all POST/PUT/DELETE requests should work without CSRF tokens.

---

## Phase 2: User Story 2 - Constitution Reflects CSRF Policy (Priority: P1)

**Goal**: Update the constitution document to accurately reflect that CSRF token checking is disabled, ensuring developers understand the security configuration.

**Independent Test**: Review the constitution document and verify it states that CSRF protection is disabled in Principle V: Security-First Authentication.

### Implementation for User Story 2

- [x] T004 [US2] Update Principle V text in `.specify/memory/constitution.md` to state "CSRF token checking is DISABLED for this application"
- [x] T005 [US2] Update Principle V rationale in `.specify/memory/constitution.md` to mention CSRF protection has been disabled per application requirements
- [x] T006 [US2] Update version number from 1.0.0 to 1.0.1 in `.specify/memory/constitution.md` version history section
- [x] T007 [US2] Add changelog entry for version 1.0.1 in `.specify/memory/constitution.md` documenting the CSRF policy update

**Checkpoint**: At this point, User Stories 1 AND 2 should both be complete - CSRF is disabled and documented.

---

## Phase 3: Polish & Cross-Cutting Concerns

**Purpose**: Verification, testing, and ensuring the feature works correctly across the application.

### Verification Tasks

- [x] T008 [P] Create feature test in `tests/Feature/CsrfDisabledTest.php` to verify POST requests work without CSRF tokens
- [x] T009 [P] Create feature test in `tests/Feature/CsrfDisabledTest.php` to verify PUT requests work without CSRF tokens
- [x] T010 [P] Create feature test in `tests/Feature/CsrfDisabledTest.php` to verify DELETE requests work without CSRF tokens
- [x] T011 Verify all existing application forms continue to function without modification
- [x] T012 Run full test suite to ensure no regressions: `php artisan test`
- [x] T013 Clear Laravel caches to ensure middleware changes take effect: `php artisan config:clear` and `php artisan route:clear`
- [x] T014 Verify no CSRF-related errors appear in application logs during normal operation

**Checkpoint**: All verification tasks complete - feature is fully functional and tested.

---

## Dependencies & Execution Order

### Phase Dependencies

- **User Story 1 (Phase 1)**: No dependencies - can start immediately
- **User Story 2 (Phase 2)**: Should follow User Story 1 (documentation reflects implementation)
- **Polish (Phase 3)**: Depends on both User Stories 1 and 2 being complete

### User Story Dependencies

- **User Story 1 (P1)**: No dependencies - implements CSRF disabling
- **User Story 2 (P1)**: Should follow User Story 1 - documents the implementation

### Within Each User Story

- User Story 1: Configuration change → Verification
- User Story 2: Text updates → Version update → Changelog
- Polish: Tests → Verification → Cache clearing

### Parallel Opportunities

- Tasks T008, T009, T010 can run in parallel (different test cases in same file)
- User Story 2 tasks (T004-T007) can be done in sequence but are independent edits to the same file
- Verification tasks T011-T014 can be done in parallel after implementation

---

## Parallel Example: User Story 1

```bash
# Sequential execution (recommended for this simple story):
Task: "Disable CSRF middleware by adding withoutMiddleware() call in bootstrap/app.php"
Task: "Add comment explaining CSRF disabling in bootstrap/app.php"
Task: "Verify middleware configuration syntax is correct in bootstrap/app.php"
```

---

## Parallel Example: Polish Phase

```bash
# Launch all test cases together:
Task: "Create feature test in tests/Feature/CsrfDisabledTest.php to verify POST requests work without CSRF tokens"
Task: "Create feature test in tests/Feature/CsrfDisabledTest.php to verify PUT requests work without CSRF tokens"
Task: "Create feature test in tests/Feature/CsrfDisabledTest.php to verify DELETE requests work without CSRF tokens"

# Launch verification tasks together:
Task: "Run full test suite to ensure no regressions: php artisan test"
Task: "Clear Laravel caches to ensure middleware changes take effect"
Task: "Verify no CSRF-related errors appear in application logs"
```

---

## Implementation Strategy

### MVP First (User Story 1 Only)

1. Complete Phase 1: User Story 1 (Disable CSRF middleware)
2. **STOP and VALIDATE**: Test that POST/PUT/DELETE requests work without CSRF tokens
3. Deploy/demo if ready

### Complete Implementation

1. Complete Phase 1: User Story 1 → Test independently
2. Complete Phase 2: User Story 2 → Verify documentation
3. Complete Phase 3: Polish → Full verification
4. Each phase adds value without breaking previous work

### Parallel Team Strategy

With multiple developers:

1. Developer A: User Story 1 (CSRF disabling)
2. Developer B: User Story 2 (Constitution update) - can start after US1 is complete
3. Developer C: Polish phase (Tests and verification) - can start after US1 and US2 are complete

---

## Notes

- [P] tasks = different files or independent operations, no dependencies
- [Story] label maps task to specific user story for traceability
- Each user story should be independently completable and testable
- This is a configuration-only change - no database migrations or new code structures needed
- Verify tests pass after implementation
- Commit after each task or logical group
- Stop at any checkpoint to validate story independently
- Avoid: modifying unrelated middleware, breaking existing functionality

## Task Summary

- **Total Tasks**: 14
- **User Story 1 Tasks**: 3
- **User Story 2 Tasks**: 4
- **Polish Tasks**: 7
- **Parallel Opportunities**: 3 test cases, multiple verification tasks
- **Independent Test Criteria**: 
  - US1: POST/PUT/DELETE requests succeed without CSRF tokens
  - US2: Constitution document accurately reflects CSRF policy
- **Suggested MVP Scope**: User Story 1 only (disable CSRF middleware)

