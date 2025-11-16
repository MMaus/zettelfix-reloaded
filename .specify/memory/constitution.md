<!--
Sync Impact Report:
Version change: N/A → 1.0.0 (initial constitution)
Modified principles: N/A (new constitution)
Added sections: Core Principles, Technology Stack, Development Workflow, Governance
Removed sections: N/A
Templates requiring updates:
  ✅ plan-template.md - Constitution Check section references constitution
  ✅ spec-template.md - No direct constitution references, but aligns with principles
  ✅ tasks-template.md - No direct constitution references, but aligns with principles
  ✅ checklist-template.md - No direct constitution references
  ✅ agent-file-template.md - No direct constitution references
Follow-up TODOs: None
-->

# Zettelfix-reloaded Constitution

## Core Principles

### I. Laravel-First Architecture

All backend functionality MUST be implemented using Laravel conventions and patterns. Controllers handle HTTP requests, Models represent data entities, Services encapsulate business logic, and Middleware handles cross-cutting concerns. Follow Laravel's directory structure and naming conventions. Use Eloquent ORM for database interactions. Leverage Laravel's built-in features (validation, authorization, queues, events) before introducing external dependencies.

**Rationale**: Laravel provides a mature, well-documented framework with established patterns. Adhering to Laravel conventions ensures maintainability, team familiarity, and compatibility with Laravel ecosystem packages.

### II. Inertia.js Integration Pattern

Frontend-backend communication MUST use Inertia.js for seamless SPA-like experience without API overhead. Server-side controllers return Inertia responses with data. Client-side Vue components consume Inertia page props. Avoid creating separate REST API endpoints for frontend data unless required for external consumption or mobile apps. Use Inertia's form helpers and page props for state management.

**Rationale**: Inertia.js eliminates the need for separate API development while maintaining modern SPA UX. It reduces code duplication, simplifies authentication, and provides better performance than traditional API-driven SPAs.

### III. Type Safety (NON-NEGOTIABLE)

All TypeScript code MUST have proper type definitions. No `any` types except where absolutely necessary (with justification). Vue components MUST use TypeScript with proper prop types and component typing. PHP code SHOULD use type hints for parameters and return types. Use Laravel's type-safe features (typed properties, return types) consistently.

**Rationale**: Type safety catches errors at development time, improves IDE support, makes refactoring safer, and serves as inline documentation. It's especially critical in a full-stack application where type mismatches can cause runtime errors.

### IV. Test-Driven Development

Feature development MUST follow TDD: Write tests first (or in parallel), ensure tests fail, implement functionality, verify tests pass, then refactor. Use Pest PHP for Laravel backend tests. Use Vitest or similar for Vue/TypeScript frontend tests. Integration tests MUST cover critical user journeys. Unit tests SHOULD cover complex business logic. Contract tests verify API boundaries if external APIs are consumed.

**Rationale**: TDD ensures code correctness, prevents regressions, and provides living documentation. Tests serve as a safety net for refactoring and help clarify requirements before implementation.

### V. Security-First Authentication

Authentication and authorization MUST use Laravel Fortify. All routes requiring authentication MUST use `auth` middleware. Authorization policies MUST be defined for resource access. Never expose sensitive data in Inertia responses. Validate and sanitize all user inputs. CSRF token checking is DISABLED for this application. Use secure session handling.

**Rationale**: Security vulnerabilities can compromise user data and system integrity. Laravel Fortify provides battle-tested authentication features. CSRF protection has been disabled per application requirements. Following security best practices from the start prevents costly security incidents.

### VI. Asset Compilation & Performance

Frontend assets MUST be compiled using Vite in production. Development uses Vite's HMR (Hot Module Replacement). Production builds MUST be optimized (minified, tree-shaken, code-split). Use Laravel's asset helpers (`@vite`, `vite_asset`) for proper asset loading. Implement caching strategies (config cache, route cache, view cache) in production. Database queries MUST be optimized (avoid N+1, use eager loading, add indexes).

**Rationale**: Performance directly impacts user experience and server costs. Vite provides fast development and optimized production builds. Laravel's caching mechanisms significantly improve response times.

### VII. Code Quality & Standards

PHP code MUST follow PSR-12 coding standards (enforced via Laravel Pint). TypeScript/Vue code MUST follow ESLint rules (configured in eslint.config.js). Prettier SHOULD format code consistently. All code MUST be reviewed before merging. Complex logic MUST include comments explaining the "why" not just the "what". Use meaningful variable and function names.

**Rationale**: Consistent code style improves readability and maintainability. Automated formatting prevents style debates. Code reviews catch bugs and share knowledge across the team.

### VIII: Data migrations

All changes to database tables MUST be implemented as proper database migration script.

**Rationale**: The system can be deployed at various environments. Database schema changes must be reproducible on every stage.


## Technology Stack

### Backend
- **Framework**: Laravel 12.0
- **PHP Version**: 8.2 or higher
- **Database**: MySQL/MariaDB (production), SQLite (development/testing)
- **Authentication**: Laravel Fortify
- **Testing**: Pest PHP
- **Code Style**: Laravel Pint (PSR-12)

### Frontend
- **Framework**: Vue 3 with Composition API
- **Language**: TypeScript
- **Integration**: Inertia.js
- **Build Tool**: Vite
- **Styling**: Tailwind CSS
- **Testing**: Vitest (recommended)

### Development Tools
- **Package Manager**: Composer (PHP), npm (JavaScript)
- **Version Control**: Git
- **Code Quality**: ESLint, Prettier, Laravel Pint

## Development Workflow

### Feature Development Process

1. **Planning**: Create feature specification (`spec.md`) with user stories, requirements, and acceptance criteria
2. **Design**: Create implementation plan (`plan.md`) with technical approach, data models, and project structure
3. **Task Breakdown**: Generate task list (`tasks.md`) organized by user story priority
4. **Implementation**: Follow TDD - write tests, implement feature, verify tests pass
5. **Review**: Code review must verify constitution compliance, test coverage, and code quality
6. **Testing**: Run full test suite, verify feature works independently
7. **Deployment**: Follow deployment guide for production releases

### Code Review Requirements

All pull requests MUST:
- Pass all automated tests
- Comply with constitution principles
- Include tests for new functionality
- Have no linter errors
- Be reviewed by at least one other developer
- Update documentation if needed

### Testing Requirements

- **Unit Tests**: Required for complex business logic, services, and utilities
- **Feature Tests**: Required for all user-facing functionality
- **Integration Tests**: Required for critical user journeys and external integrations
- **Test Coverage**: Aim for 80%+ coverage on critical paths

### Deployment Process

1. Build production assets: `npm run build`
2. Run tests: `php artisan test`
3. Optimize Laravel: `php artisan config:cache`, `route:cache`, `view:cache`
4. Run migrations: `php artisan migrate --force`
5. Verify environment configuration
6. Deploy to production server following DEPLOYMENT.md guide

## Governance

This constitution supersedes all other development practices and guidelines. All code, architecture decisions, and development processes MUST comply with these principles.

### Amendment Process

1. Proposed amendments MUST be documented with rationale
2. Amendments require team discussion and consensus
3. Version MUST be incremented according to semantic versioning:
   - **MAJOR**: Backward-incompatible principle changes or removals
   - **MINOR**: New principles or significant expansions
   - **PATCH**: Clarifications, wording improvements, non-semantic changes
4. Constitution changes MUST be reflected in dependent templates and documentation
5. All team members MUST be notified of constitution amendments

### Compliance

- All pull requests MUST verify constitution compliance
- Code reviews MUST check adherence to principles
- Violations MUST be justified or corrected
- Complexity additions MUST be documented in plan.md's Complexity Tracking section
- Use `.specify/templates/agent-file-template.md` for runtime development guidance

### Version History

**Version**: 1.0.1 | **Ratified**: 2025-01-27 | **Last Amended**: 2025-01-27

**Changelog**:
- **1.0.1** (2025-01-27): Updated Principle V to reflect that CSRF token checking is disabled
