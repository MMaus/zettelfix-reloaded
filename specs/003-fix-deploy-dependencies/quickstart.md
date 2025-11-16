# Quickstart: Fix Deployment Workflow Dependencies

**Date**: 2025-01-27  
**Feature**: Fix Deployment Workflow Dependencies  
**Branch**: `003-fix-deploy-dependencies`

## Overview

This guide provides step-by-step instructions to implement the fix for the GitHub Actions deployment workflow to ensure PHP dependencies are installed both in CI/CD and on the target server.

## Prerequisites

- Access to GitHub repository
- Understanding of GitHub Actions workflows
- Access to target deployment server (for verification)
- Composer installed on target server (verify with `composer --version`)

## Implementation Steps

### Step 1: Add PHP Setup Step

Add PHP setup step after checkout and before Node.js setup:

```yaml
- name: Setup PHP
  uses: shivammathur/setup-php@v2
  with:
    php-version: 8.2
    tools: composer:v2
```

**Location**: After `Checkout code` step, before `Setup Node.js` step

**Rationale**: Provides PHP and Composer tools needed for dependency installation

### Step 2: Add PHP Dependency Installation Step

Add Composer install step after Node.js setup:

```yaml
- name: Install PHP dependencies
  run: composer install --no-dev --optimize-autoloader
```

**Location**: After `Setup Node.js` step, before `Install dependencies` (npm) step

**Rationale**: 
- Installs PHP dependencies in CI for validation
- `--no-dev` excludes development dependencies (production-ready)
- `--optimize-autoloader` improves performance

### Step 3: Verify Frontend Dependency Installation

Ensure `npm ci` step exists before build (already present, verify):

```yaml
- name: Install dependencies
  run: npm ci
```

**Location**: After PHP dependency installation, before build step

**Rationale**: `npm ci` ensures exact dependency versions from lock file

### Step 4: Add Server-Side PHP Dependency Installation

Add SSH command to install PHP dependencies on target server after file deployment:

```yaml
- name: Install PHP dependencies on server
  env:
    SSH_HOST: ${{ secrets.SSH_HOST }}
    SSH_USERNAME: ${{ secrets.SSH_USERNAME }}
    SSH_PASSWORD: ${{ secrets.SSH_PASSWORD }}
  run: |
    sshpass -p "$SSH_PASSWORD" ssh -o StrictHostKeyChecking=no "$SSH_USERNAME@$SSH_HOST" \
      "cd ~/zettelfix.de/preview && composer install --no-dev --optimize-autoloader"
```

**Location**: After `Deploy to remote server` step (rsync), as final step

**Rationale**: 
- Installs PHP dependencies on target server after files are copied
- Ensures compatibility with server's PHP version
- Uses same flags as CI installation for consistency

### Step 5: Complete Workflow Structure

Final workflow structure should be:

```yaml
steps:
  - name: Checkout code
  - name: Setup PHP
  - name: Setup Node.js
  - name: Install PHP dependencies
  - name: Install dependencies (npm)
  - name: Build assets
  - name: Install SSH client and rsync
  - name: Deploy to remote server
  - name: Install PHP dependencies on server
```

## Verification Steps

### 1. Verify Workflow Syntax

```bash
# Check YAML syntax (if you have yamllint)
yamllint .github/workflows/deploy.yml

# Or use online YAML validator
```

### 2. Test Workflow Execution

1. Push changes to a test branch
2. Manually trigger workflow via GitHub Actions UI
3. Monitor workflow execution logs
4. Verify each step completes successfully

### 3. Verify Server Dependencies

After successful deployment, SSH into target server and verify:

```bash
ssh user@server
cd ~/zettelfix.de/preview
ls -la vendor/          # Should exist
php vendor/bin/phpunit --version  # Should work (if dev deps were included)
```

### 4. Verify Application Functionality

1. Access deployed application URL
2. Verify application loads without errors
3. Check application logs for missing dependency errors
4. Test key application functionality

## Troubleshooting

### Issue: Composer not found on target server

**Solution**: Install Composer on target server:
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### Issue: PHP version mismatch

**Solution**: Ensure target server has PHP 8.2+:
```bash
php -v  # Check version
# Update if needed based on server OS
```

### Issue: Workflow fails at Composer install

**Check**:
- `composer.json` exists in repository
- `composer.lock` exists (recommended)
- GitHub Actions runner has internet access
- No syntax errors in workflow file

### Issue: Server-side installation fails

**Check**:
- SSH connection works: `ssh user@host`
- Composer is in PATH on server: `which composer`
- Sufficient disk space: `df -h`
- Correct directory path in SSH command

## Testing Checklist

- [ ] Workflow file syntax is valid
- [ ] PHP setup step completes successfully
- [ ] PHP dependencies install in CI
- [ ] Frontend dependencies install in CI
- [ ] Assets build successfully
- [ ] Files deploy to server
- [ ] PHP dependencies install on server
- [ ] Application runs without dependency errors
- [ ] No regressions in existing functionality

## Rollback Plan

If issues occur, revert the workflow file:

```bash
git checkout HEAD~1 -- .github/workflows/deploy.yml
git commit -m "Revert deployment workflow changes"
git push
```

## Next Steps

After successful implementation:

1. Monitor deployment logs for any issues
2. Document any server-specific requirements
3. Consider adding deployment notifications
4. Update DEPLOYMENT.md if needed

## References

- [GitHub Actions Documentation](https://docs.github.com/en/actions)
- [setup-php Action](https://github.com/shivammathur/setup-php)
- [Composer Documentation](https://getcomposer.org/doc/)
- [Laravel Deployment Guide](https://laravel.com/docs/deployment)

