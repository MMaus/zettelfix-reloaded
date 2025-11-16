# API Contracts: Disable CSRF Token Check

**Feature**: 001-disable-csrf  
**Date**: 2025-01-27  
**Status**: N/A

## Overview

This feature does not introduce new API endpoints or modify existing API contracts. All existing endpoints continue to function identically, except that CSRF token validation is no longer required.

## Existing Endpoints

All existing web routes continue to work without modification:

- `POST /todos` - Create todo item (no CSRF token required)
- `PUT /todos/{id}` - Update todo item (no CSRF token required)
- `DELETE /todos/{id}` - Delete todo item (no CSRF token required)
- `POST /shopping` - Create shopping list item (no CSRF token required)
- `PUT /shopping/{id}` - Update shopping list item (no CSRF token required)
- `DELETE /shopping/{id}` - Delete shopping list item (no CSRF token required)
- All other POST/PUT/PATCH/DELETE routes (no CSRF token required)

## Contract Changes

**Before**: All POST/PUT/PATCH/DELETE requests required a valid CSRF token in the request.

**After**: All POST/PUT/PATCH/DELETE requests are accepted without CSRF token validation.

## Request Format

No changes to request format. Requests that previously included CSRF tokens will continue to work (tokens are simply ignored). Requests without CSRF tokens will now succeed instead of being rejected.

## Response Format

No changes to response format. Successful requests return the same responses as before.

## Error Handling

**Before**: Requests without valid CSRF tokens returned `419 CSRF Token Mismatch` errors.

**After**: Requests without CSRF tokens are processed normally. No CSRF-related errors occur.

## Notes

- This is a middleware configuration change, not an API contract change
- No OpenAPI/Swagger documentation updates required
- No GraphQL schema changes required
- Frontend code does not need to be modified (though CSRF token generation can be removed if desired)

