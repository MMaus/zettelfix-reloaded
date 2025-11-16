# Implementation Plan: Fix Deployment Workflow Dependencies

**Branch**: `003-fix-deploy-dependencies` | **Date**: 2025-01-27 | **Spec**: [spec.md](./spec.md)
**Input**: Feature specification from `/specs/003-fix-deploy-dependencies/spec.md`

**Note**: This template is filled in by the `/speckit.plan` command. See `.specify/templates/commands/plan.md` for the execution workflow.

## Summary

Fix the GitHub Actions deployment workflow to ensure all PHP dependencies are installed during the CI/CD build process and on the target server after deployment. The workflow currently only installs frontend dependencies and builds assets, but does not install PHP dependencies (vendor folder) either in the CI/CD pipeline or on the target server. Additionally, verify that frontend dependencies are properly installed before building.

## Technical Context

**Language/Version**: PHP 8.2+, YAML (GitHub Actions workflow)  
**Primary Dependencies**: Composer (PHP package manager), npm (Node.js package manager), Laravel 12.0  
**Storage**: N/A (workflow configuration only)  
**Testing**: GitHub Actions workflow execution, manual deployment verification  
**Target Platform**: GitHub Actions runners (Ubuntu), Linux deployment server  
**Project Type**: CI/CD workflow configuration  
**Performance Goals**: Deployment workflow completes dependency installation within reasonable time (< 5 minutes for PHP dependencies, < 2 minutes for frontend dependencies)  
**Constraints**: Must work with existing deployment infrastructure (SSH/rsync), must not break existing deployment process, must handle errors gracefully  
**Scale/Scope**: Single deployment workflow file, affects all deployments triggered from main/master branches

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

### Principle I: Laravel-First Architecture
✅ **PASS** - No violation. This is a deployment workflow configuration, not application code. The workflow ensures Laravel dependencies are properly installed.

### Principle II: Inertia.js Integration Pattern
✅ **PASS** - No violation. Not applicable to deployment workflow.

### Principle III: Type Safety
✅ **PASS** - No violation. GitHub Actions workflows use YAML, which is inherently type-safe for configuration.

### Principle IV: Test-Driven Development
⚠️ **PARTIAL** - Workflow changes should be tested through actual deployment runs. Manual verification required for deployment workflows.

### Principle V: Security-First Authentication
✅ **PASS** - No violation. Workflow uses existing SSH secrets for deployment. No authentication changes required.

### Principle VI: Asset Compilation & Performance
✅ **PASS** - No violation. Workflow already handles Vite asset compilation. This change ensures dependencies are available for proper compilation.

### Principle VII: Code Quality & Standards
✅ **PASS** - No violation. YAML workflow files follow GitHub Actions best practices.

### Principle VIII: Data Migrations
✅ **PASS** - No violation. Not applicable to deployment workflow.

**Overall Status**: ✅ **PASS** - No constitution violations. Workflow improvements align with existing principles.

## Project Structure

### Documentation (this feature)

```text
specs/003-fix-deploy-dependencies/
├── plan.md              # This file (/speckit.plan command output)
├── research.md          # Phase 0 output (/speckit.plan command)
├── data-model.md        # Phase 1 output (/speckit.plan command)
├── quickstart.md        # Phase 1 output (/speckit.plan command)
├── contracts/           # Phase 1 output (/speckit.plan command)
└── tasks.md             # Phase 2 output (/speckit.tasks command - NOT created by /speckit.plan)
```

### Source Code (repository root)

```text
.github/
└── workflows/
    └── deploy.yml       # Deployment workflow to be updated
```

**Structure Decision**: This is a single-file change to the existing GitHub Actions workflow. No new directory structure needed. The workflow file is located at `.github/workflows/deploy.yml` in the repository root.

## Complexity Tracking

> **Fill ONLY if Constitution Check has violations that must be justified**

No violations requiring justification.

## Phase Completion Status

### Phase 0: Outline & Research ✅ COMPLETE
- **research.md**: Created with findings on PHP dependency installation, server-side installation, error handling, and best practices
- **Status**: All research questions resolved, no NEEDS CLARIFICATION markers remain

### Phase 1: Design & Contracts ✅ COMPLETE
- **data-model.md**: Created workflow structure model and execution flow
- **contracts/workflow-interface.md**: Created workflow interface contract defining inputs, outputs, and execution behavior
- **quickstart.md**: Created step-by-step implementation guide
- **Agent Context**: Updated `.cursor/rules/specify-rules.mdc` with new technology information
- **Status**: All design artifacts created, ready for task breakdown

### Phase 2: Task Breakdown
- **Status**: Pending - Use `/speckit.tasks` command to generate task breakdown

## Implementation Notes

- Workflow changes are additive (adding steps, not modifying existing ones)
- Backward compatible - existing deployment process continues to work
- Requires Composer to be installed on target server (new requirement, documented)
- All dependency installation uses production flags (`--no-dev`, `--optimize-autoloader`)
