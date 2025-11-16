# Data Model: Fix Deployment Workflow Dependencies

**Date**: 2025-01-27  
**Feature**: Fix Deployment Workflow Dependencies  
**Branch**: `003-fix-deploy-dependencies`

## Overview

This feature modifies a GitHub Actions workflow configuration file. There is no traditional data model, but the workflow structure and execution flow can be modeled.

## Workflow Structure

### Workflow File Structure

```yaml
name: Deploy
on: [workflow triggers]
jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - [step definitions]
```

### Workflow Steps (Current → Target)

#### Current Workflow Steps
1. Checkout code
2. Setup Node.js
3. Install dependencies (npm ci)
4. Build assets (npm run build)
5. Install SSH client and rsync
6. Deploy to remote server (rsync only)

#### Target Workflow Steps
1. Checkout code
2. Setup PHP with Composer
3. Setup Node.js
4. Install PHP dependencies (composer install)
5. Install frontend dependencies (npm ci)
6. Build assets (npm run build)
7. Install SSH client and rsync
8. Deploy files to remote server (rsync)
9. Install PHP dependencies on target server (SSH command)

## Workflow State Transitions

### Deployment Workflow States

```
[Triggered] 
  → [Checkout] 
  → [Setup PHP] 
  → [Setup Node] 
  → [Install PHP Deps] 
  → [Install Frontend Deps] 
  → [Build Assets] 
  → [Prepare SSH] 
  → [Deploy Files] 
  → [Install Server Deps] 
  → [Complete]
```

### Failure Points

- **PHP Setup Failure**: Workflow fails, no deployment
- **Composer Install Failure**: Workflow fails, no deployment
- **npm ci Failure**: Workflow fails, no deployment
- **Build Failure**: Workflow fails, no deployment
- **SSH Connection Failure**: Workflow fails, partial deployment possible
- **Server Composer Install Failure**: Workflow fails, files deployed but app non-functional

## Workflow Inputs/Secrets

### Required Secrets (Existing)
- `SSH_HOST`: Target server hostname/IP
- `SSH_USERNAME`: SSH username for deployment
- `SSH_PASSWORD`: SSH password for authentication

### Environment Variables
- None required (all configuration via secrets)

## Workflow Outputs

### Artifacts Created
- `public/build/`: Compiled frontend assets (already created)
- `vendor/`: PHP dependencies (newly created in CI, then on server)

### Deployment Results
- Success: All files deployed, dependencies installed, application functional
- Failure: Workflow stops, error message displayed, partial deployment possible

## Dependencies Graph

```
composer.json
  └── composer install
      └── vendor/ (PHP dependencies)

package.json + package-lock.json
  └── npm ci
      └── node_modules/ (Frontend dependencies)
          └── npm run build
              └── public/build/ (Compiled assets)
```

## File Structure

### Repository Files (Input)
- `composer.json`: PHP dependency definitions
- `composer.lock`: PHP dependency lock file
- `package.json`: Frontend dependency definitions
- `package-lock.json`: Frontend dependency lock file
- `.github/workflows/deploy.yml`: Workflow configuration

### Generated Files (Output)
- `vendor/`: PHP dependencies directory (created in CI and on server)
- `node_modules/`: Frontend dependencies directory (created in CI only)
- `public/build/`: Compiled frontend assets (created in CI, deployed to server)

## Validation Rules

### Pre-Deployment Validation
- `composer.json` must exist
- `composer.lock` must exist (recommended)
- `package.json` must exist
- `package-lock.json` must exist (required for `npm ci`)

### Post-Deployment Validation
- `vendor/` directory must exist on target server
- `vendor/autoload.php` must be present (indicates successful Composer install)
- `public/build/` directory must exist on target server

## Notes

- The `node_modules/` directory is not deployed to the server (only built assets are)
- The `vendor/` directory is installed on both CI runner and target server
- Composer and npm commands provide their own validation and error reporting

