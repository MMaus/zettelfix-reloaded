# Tasks: Fix Deployment Workflow Dependencies

**Input**: Design documents from `/specs/003-fix-deploy-dependencies/`
**Prerequisites**: plan.md (required), spec.md (required for user stories), research.md, data-model.md, contracts/, quickstart.md

**Tests**: Manual verification through GitHub Actions workflow execution. No automated test tasks required.

**Organization**: Tasks are grouped by user story to enable independent implementation and testing of each story.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Can run in parallel (different files, no dependencies)
- **[Story]**: Which user story this task belongs to (e.g., US1, US2, US3)
- Include exact file paths in descriptions

## Path Conventions

- **Workflow file**: `.github/workflows/deploy.yml` at repository root
- **Documentation**: `DEPLOYMENT.md` at repository root (may need updates)

---

## Phase 1: Setup (Workflow Preparation)

**Purpose**: Prepare for workflow modifications

- [x] T001 Review current workflow structure in `.github/workflows/deploy.yml`
- [x] T002 Verify required GitHub secrets are configured (SSH_HOST, SSH_USERNAME, SSH_PASSWORD)
- [x] T003 Verify composer.json and composer.lock exist in repository root
- [x] T004 Verify package.json and package-lock.json exist in repository root

**Checkpoint**: Prerequisites verified - ready to modify workflow

---

## Phase 2: User Story 1 - PHP Dependencies Installed in CI/CD Pipeline (Priority: P1) 🎯 MVP

**Goal**: Install all PHP dependencies during the CI/CD build process so that the application can be properly validated and prepared for deployment.

**Independent Test**: Run the deployment workflow and verify that PHP dependencies are installed in the vendor directory before deployment artifacts are created. Check workflow logs to confirm `composer install` step completes successfully.

### Implementation for User Story 1

- [x] T005 [US1] Add PHP setup step using shivammathur/setup-php@v2 action in `.github/workflows/deploy.yml` (after checkout, before Node.js setup)
- [x] T006 [US1] Add PHP dependency installation step with `composer install --no-dev --optimize-autoloader` in `.github/workflows/deploy.yml` (after Node.js setup, before npm install)
- [x] T007 [US1] Verify workflow YAML syntax is valid for `.github/workflows/deploy.yml`

**Checkpoint**: At this point, User Story 1 should be complete. PHP dependencies will be installed in CI/CD pipeline. Test by triggering workflow and verifying vendor directory is created.

---

## Phase 3: User Story 2 - PHP Dependencies Installed on Target Server (Priority: P2)

**Goal**: Install PHP dependencies on the target deployment server after files are copied so that the application has all required packages available at runtime.

**Independent Test**: Deploy to target environment and verify that PHP dependencies are installed after file transfer completes. SSH into server and check that vendor directory exists and contains required packages.

### Implementation for User Story 2

- [x] T008 [US2] Add SSH step to install PHP dependencies on target server in `.github/workflows/deploy.yml` (after rsync deployment step)
- [x] T009 [US2] Configure SSH command to run `composer install --no-dev --optimize-autoloader` in target directory `~/zettelfix.de/preview` in `.github/workflows/deploy.yml`
- [x] T010 [US2] Verify SSH command uses same production flags as CI installation in `.github/workflows/deploy.yml`

**Checkpoint**: At this point, User Stories 1 AND 2 should both work. PHP dependencies will be installed in both CI and on target server. Test by deploying and verifying vendor directory on server.

---

## Phase 4: User Story 3 - Frontend Dependencies Verified (Priority: P3)

**Goal**: Ensure that all frontend dependencies are properly downloaded and installed during the build process so that the frontend application has all required packages.

**Independent Test**: Run the deployment workflow and verify that frontend dependencies are installed before the build step executes. Check workflow logs to confirm `npm ci` step completes successfully before build.

### Implementation for User Story 3

- [x] T011 [US3] Verify npm ci step exists and runs before build step in `.github/workflows/deploy.yml`
- [x] T012 [US3] Ensure npm ci step is positioned correctly (after PHP dependency installation, before build) in `.github/workflows/deploy.yml`
- [x] T013 [US3] Verify package-lock.json is present and up-to-date in repository root

**Checkpoint**: At this point, all three user stories should be complete. Frontend dependencies are verified, PHP dependencies install in CI and on server. Test by running full deployment workflow.

---

## Phase 5: Polish & Cross-Cutting Concerns

**Purpose**: Final validation and documentation updates

- [x] T014 Verify complete workflow step order in `.github/workflows/deploy.yml` matches quickstart.md specification
- [x] T015 [P] Update DEPLOYMENT.md with Composer requirement on target server if needed
- [x] T016 [P] Add troubleshooting section for common deployment issues in DEPLOYMENT.md if needed
- [x] T017 Test complete deployment workflow end-to-end
- [x] T018 Verify error handling: Test workflow failure scenarios (missing composer.json, network failure, etc.)
- [x] T019 Verify application runs correctly on target server after deployment

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: No dependencies - can start immediately
- **User Story 1 (Phase 2)**: Depends on Setup completion - MVP scope
- **User Story 2 (Phase 3)**: Depends on User Story 1 completion (workflow must have PHP setup first)
- **User Story 3 (Phase 4)**: Depends on User Story 1 completion (workflow structure must be established)
- **Polish (Phase 5)**: Depends on all user stories being complete

### User Story Dependencies

- **User Story 1 (P1)**: Can start after Setup (Phase 1) - No dependencies on other stories
- **User Story 2 (P2)**: Depends on User Story 1 (needs PHP setup step from US1)
- **User Story 3 (P3)**: Depends on User Story 1 (needs workflow structure from US1) - Can be verified independently

### Within Each User Story

- Workflow steps must be added in correct order
- Each step must be tested before moving to next
- Story complete before moving to next priority

### Parallel Opportunities

- Setup tasks T001-T004 can be verified in parallel
- Polish tasks T015-T016 marked [P] can run in parallel
- User Story 3 verification tasks T011-T013 can be done in parallel (all are verification/positioning tasks)

---

## Parallel Example: Setup Phase

```bash
# Verify prerequisites in parallel:
Task: "Review current workflow structure in .github/workflows/deploy.yml"
Task: "Verify required GitHub secrets are configured"
Task: "Verify composer.json and composer.lock exist"
Task: "Verify package.json and package-lock.json exist"
```

---

## Implementation Strategy

### MVP First (User Story 1 Only)

1. Complete Phase 1: Setup (verify prerequisites)
2. Complete Phase 2: User Story 1 (add PHP setup and installation in CI)
3. **STOP and VALIDATE**: Test workflow execution, verify PHP dependencies install in CI
4. Deploy/demo if ready

### Incremental Delivery

1. Complete Setup → Prerequisites verified
2. Add User Story 1 → PHP dependencies install in CI → Test independently → Deploy/Demo (MVP!)
3. Add User Story 2 → PHP dependencies install on server → Test independently → Deploy/Demo
4. Add User Story 3 → Frontend dependencies verified → Test independently → Deploy/Demo
5. Each story adds value without breaking previous stories

### Sequential Implementation (Recommended)

Since this is a single workflow file with dependent steps:

1. Complete Setup phase (parallel verification)
2. Implement User Story 1 (adds PHP setup and CI installation)
3. Test User Story 1 independently
4. Implement User Story 2 (adds server-side installation)
5. Test User Story 2 independently
6. Verify User Story 3 (frontend dependencies already handled)
7. Complete Polish phase

---

## Task Summary

**Total Tasks**: 19 tasks

**Tasks per User Story**:
- **User Story 1 (P1)**: 3 tasks (T005-T007)
- **User Story 2 (P2)**: 3 tasks (T008-T010)
- **User Story 3 (P3)**: 3 tasks (T011-T013)
- **Setup**: 4 tasks (T001-T004)
- **Polish**: 6 tasks (T014-T019)

**Parallel Opportunities**: 
- Setup phase: 4 tasks can be verified in parallel
- Polish phase: 2 tasks marked [P] can run in parallel
- User Story 3: 3 verification tasks can be done in parallel

**Independent Test Criteria**:
- **User Story 1**: Run workflow, verify vendor directory created in CI, check logs for successful composer install
- **User Story 2**: Deploy to server, SSH and verify vendor directory exists on target server
- **User Story 3**: Run workflow, verify npm ci completes before build, check logs

**Suggested MVP Scope**: User Story 1 only (PHP dependencies in CI/CD pipeline)

---

## Notes

- All tasks modify single file: `.github/workflows/deploy.yml`
- Tasks must be completed sequentially within each user story (workflow steps are ordered)
- Manual verification required: Test workflow execution after each user story
- No automated tests: Workflow execution is the test
- Commit after each user story completion for easy rollback if needed
- Verify workflow syntax after each modification
- Test on a branch before merging to main/master

