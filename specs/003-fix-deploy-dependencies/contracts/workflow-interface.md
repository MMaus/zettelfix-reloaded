# Workflow Interface Contract: Deployment Workflow

**Date**: 2025-01-27  
**Feature**: Fix Deployment Workflow Dependencies  
**Branch**: `003-fix-deploy-dependencies`

## Overview

This document defines the interface contract for the GitHub Actions deployment workflow. Unlike API contracts, this defines the workflow's inputs, outputs, and execution contract.

## Workflow Trigger Contract

### Input Triggers
- **Manual Dispatch**: `workflow_dispatch` event
- **Push Events**: Pushes to `main` or `master` branches

### Trigger Conditions
- Workflow runs on every push to `main` or `master`
- Workflow can be manually triggered via GitHub Actions UI
- No input parameters required for manual dispatch

## Workflow Inputs Contract

### Required Secrets
| Secret Name | Type | Description | Example |
|-------------|------|-------------|---------|
| `SSH_HOST` | string | Target server hostname or IP address | `example.com` or `192.168.1.100` |
| `SSH_USERNAME` | string | SSH username for server access | `deploy` |
| `SSH_PASSWORD` | string | SSH password for authentication | `secure_password` |

### Required Files (Repository)
| File | Purpose | Required |
|------|---------|----------|
| `composer.json` | PHP dependency definitions | Yes |
| `composer.lock` | PHP dependency lock file | Recommended |
| `package.json` | Frontend dependency definitions | Yes |
| `package-lock.json` | Frontend dependency lock file | Yes (for `npm ci`) |

## Workflow Execution Contract

### Step Execution Order
1. **Checkout**: Must succeed before any other steps
2. **Setup PHP**: Must succeed before PHP dependency installation
3. **Setup Node.js**: Must succeed before frontend dependency installation
4. **Install PHP Dependencies**: Must succeed before deployment
5. **Install Frontend Dependencies**: Must succeed before asset build
6. **Build Assets**: Must succeed before deployment
7. **Prepare SSH Tools**: Must succeed before remote deployment
8. **Deploy Files**: Must succeed before server-side dependency installation
9. **Install Server Dependencies**: Must succeed for application to function

### Failure Behavior
- **Early Failure**: Workflow stops immediately, no deployment occurs
- **Late Failure**: Partial deployment possible, workflow fails with error
- **Error Reporting**: All failures reported in GitHub Actions logs with command output

## Workflow Outputs Contract

### Success Criteria
- All workflow steps complete with exit code 0
- Files successfully transferred to target server
- PHP dependencies installed on target server
- Application can run on target server

### Failure Criteria
- Any step returns non-zero exit code
- SSH connection fails
- Dependency installation fails
- File transfer fails

### Output Artifacts
| Artifact | Location | Purpose |
|----------|----------|---------|
| Compiled Assets | `public/build/` | Frontend application assets |
| PHP Dependencies (CI) | `vendor/` | PHP packages (CI validation only) |
| PHP Dependencies (Server) | `vendor/` on server | PHP packages (runtime) |

## Environment Contract

### CI Environment Requirements
- **OS**: Ubuntu Latest (GitHub Actions default)
- **PHP**: 8.2+ (via setup-php action)
- **Composer**: Latest (via setup-php action)
- **Node.js**: 20 (via setup-node action)
- **npm**: Included with Node.js

### Target Server Requirements
- **OS**: Linux (any distribution)
- **PHP**: 8.2+ (must match CI version)
- **Composer**: Installed and in PATH
- **SSH**: Accessible from GitHub Actions runners
- **Disk Space**: Sufficient for application + dependencies

## Error Contract

### Error Types
1. **Configuration Errors**: Missing secrets, invalid configuration
2. **Dependency Errors**: Package installation failures, version conflicts
3. **Build Errors**: Asset compilation failures
4. **Deployment Errors**: SSH failures, file transfer failures
5. **Server Errors**: Server-side dependency installation failures

### Error Reporting
- All errors reported in GitHub Actions workflow logs
- Error messages include:
  - Failed command
  - Exit code
  - Command output (stdout/stderr)
  - Step name and workflow run URL

## Validation Contract

### Pre-Execution Validation
- Repository contains required dependency files
- GitHub secrets are configured
- Workflow file syntax is valid YAML

### Post-Execution Validation
- Workflow completes successfully (all steps pass)
- Files exist on target server
- Dependencies installed on target server
- Application can start without missing dependency errors

## Compatibility Contract

### Backward Compatibility
- Existing deployment process continues to work
- No breaking changes to workflow triggers
- No changes to required secrets
- No changes to target server requirements (adds Composer requirement)

### Forward Compatibility
- Workflow structure allows future enhancements
- Can add additional deployment steps without breaking existing flow
- Can add additional validation steps

## Notes

- This workflow modifies infrastructure/deployment configuration, not application code
- Changes are backward compatible (adds steps, doesn't remove)
- Target server must have Composer installed (new requirement)

