# Research: Fix Deployment Workflow Dependencies

**Date**: 2025-01-27  
**Feature**: Fix Deployment Workflow Dependencies  
**Branch**: `003-fix-deploy-dependencies`

## Research Questions

1. How to install PHP dependencies in GitHub Actions workflows?
2. How to install PHP dependencies on target server after file deployment?
3. Best practices for error handling in GitHub Actions workflows?
4. How to verify frontend dependencies are properly installed?
5. Production vs development dependency installation for PHP?

## Findings

### 1. Installing PHP Dependencies in GitHub Actions

**Decision**: Use `shivammathur/setup-php@v2` action with Composer tool, then run `composer install --no-dev --optimize-autoloader`

**Rationale**: 
- The existing `tests.yml` workflow already uses `shivammathur/setup-php@v2` which provides Composer
- `--no-dev` flag excludes development dependencies (appropriate for production deployment)
- `--optimize-autoloader` improves performance by generating optimized class maps
- This matches the pattern used in the existing test workflow

**Alternatives considered**:
- Installing Composer manually: More complex, unnecessary when action provides it
- Using `composer install` without flags: Would include dev dependencies unnecessarily

**Source**: Existing `.github/workflows/tests.yml` workflow, Laravel deployment best practices

### 2. Installing PHP Dependencies on Target Server

**Decision**: Execute `composer install --no-dev --optimize-autoloader` via SSH after file transfer completes

**Rationale**:
- Dependencies should be installed on target server to ensure compatibility with server's PHP version
- Installing after file transfer ensures `composer.json` and `composer.lock` are present
- `--no-dev` ensures production-only dependencies are installed
- `--optimize-autoloader` improves runtime performance

**Alternatives considered**:
- Copying vendor folder from CI: Would require copying large directory, potential version mismatches
- Installing before file transfer: Files not yet present on server

**Source**: DEPLOYMENT.md Step 4, Laravel deployment documentation

### 3. Error Handling in GitHub Actions

**Decision**: Use `set -e` in bash scripts and let GitHub Actions fail on non-zero exit codes

**Rationale**:
- GitHub Actions automatically fails the workflow if any step returns non-zero exit code
- Composer and npm commands return appropriate exit codes on failure
- Clear error messages from package managers will be displayed in workflow logs
- No need for complex error handling - let the tools handle it

**Alternatives considered**:
- Custom error handling scripts: Unnecessary complexity, package managers already provide good error messages
- Continuing on error: Would mask deployment failures

**Source**: GitHub Actions documentation, best practices

### 4. Verifying Frontend Dependencies

**Decision**: Use `npm ci` instead of `npm install` to ensure exact dependency versions

**Rationale**:
- `npm ci` installs dependencies directly from `package-lock.json` ensuring exact versions
- `npm ci` is faster and more reliable for CI/CD environments
- Already used in the workflow, just need to verify it runs before build step
- Fails if `package-lock.json` is out of sync with `package.json`

**Alternatives considered**:
- `npm install`: Less strict, may install different versions than lock file
- Verifying node_modules exists: Doesn't ensure correct versions

**Source**: npm documentation, existing workflow already uses `npm ci`

### 5. Production Dependency Installation

**Decision**: Use `--no-dev` flag for Composer to exclude development dependencies

**Rationale**:
- Development dependencies (like Pest, PHPUnit) are not needed in production
- Reduces vendor folder size and improves security surface
- Standard Laravel production deployment practice
- Matches DEPLOYMENT.md recommendations

**Alternatives considered**:
- Including dev dependencies: Unnecessary bloat, security risk
- Custom dependency filtering: Overcomplicated, Composer handles this well

**Source**: DEPLOYMENT.md, Laravel deployment best practices, Composer documentation

## Implementation Approach

1. **Add PHP setup step** in GitHub Actions workflow before dependency installation
2. **Add Composer install step** after Node.js setup but before asset build
3. **Add SSH command** to install PHP dependencies on target server after rsync completes
4. **Verify npm ci** runs before build (already correct, just document)
5. **Add error handling** by ensuring commands fail appropriately (default behavior)

## Dependencies and Prerequisites

- GitHub Actions runner must support PHP 8.2+ (Ubuntu latest provides this)
- Target server must have Composer installed and accessible in PATH
- Target server must have PHP 8.2+ installed
- SSH access to target server must be configured (already in place)
- `composer.json` and `composer.lock` must be present in repository

## Risks and Mitigations

| Risk | Mitigation |
|------|------------|
| Composer not installed on target server | Document requirement, add verification step |
| PHP version mismatch | Use same PHP version in CI and production, document requirement |
| Network failures during installation | Let commands fail naturally, GitHub Actions will show error |
| Disk space issues | Document requirement, let installation fail with clear error |
| Missing composer.json/lock | Repository already contains these files, no action needed |

## References

- Existing `.github/workflows/tests.yml` - Shows PHP/Composer setup pattern
- Existing `.github/workflows/deploy.yml` - Current deployment workflow
- `DEPLOYMENT.md` - Manual deployment guide showing server-side steps
- `composer.json` - PHP dependency configuration
- `package.json` - Frontend dependency configuration

