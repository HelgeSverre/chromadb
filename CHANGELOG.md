# Changelog

All notable changes to `chromadb` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [3.0.0] - 2026-04-15

### Added

- **ChromaDB 1.5.x Support** - Full compatibility with ChromaDB versions 1.1.0 through 1.5.7
    - CI matrix now tests against ChromaDB 1.1.0, 1.3.7, and 1.5.7
    - Backward compatibility maintained for all tested versions
    - Default Docker image updated to 1.5.7

- **Attached Functions API** (ChromaDB 1.3.0+) - Server-side function attachment for collections
    - `Collections::attachFunction()` - Attach functions to collections for automatic processing
    - `Collections::getAttachedFunction()` - Get details of an attached function by name
    - `Collections::detachFunction()` - Remove attached functions from collections
    - Enables server-side statistics computation and workflow triggers

- **Version-Aware Test Helpers** - Utilities for testing against multiple ChromaDB versions
    - `getChromaVersion()` - Get current ChromaDB version from environment or server
    - `chromaVersionAtLeast()` - Check if version meets minimum requirement
    - `skipIfChromaVersionBelow()` - Skip tests that require newer ChromaDB versions

### Changed

- **Saloon 4 Compatibility** - Updated the HTTP client stack to address security advisories
    - `saloonphp/saloon` now requires `^4.0`
    - `saloonphp/laravel-plugin` now requires `^4.2`
    - Resolves Composer audit failures caused by insecure Saloon 3 releases

- **Laravel Compatibility** - Updated optional Laravel integration for current framework versions
    - Laravel 11, 12.39+, and 13 are supported through Saloon's Laravel plugin
    - Laravel 10 support was dropped because Saloon Laravel plugin 4 no longer supports it
    - Development tooling now supports Testbench 9/10/11, Pest 3/4, and Larastan 3

- **Test Coverage Configuration** - Coverage reports are now generated only by `composer test-coverage`
    - Normal test runs no longer fail on machines without Xdebug or PCOV under PHPUnit 12
    - Coverage output paths remain unchanged for the dedicated coverage command

- **Version Compatibility** - Updated supported ChromaDB range
    - Before: `1.0.0 - 1.1.x`
    - After: `1.0.0 - 1.5.x`

- **CI Matrix** - Expanded test coverage
    - PHP versions: 8.2, 8.3, 8.4
    - ChromaDB versions: 1.1.0, 1.3.7, 1.5.7
    - Total: 9 test combinations (3 PHP × 3 ChromaDB)

- **Delete Response Compatibility** - Tests now accept both ChromaDB delete response shapes
    - ChromaDB <=1.3 returns an empty array for item delete responses
    - ChromaDB 1.5.x can return `{"deleted": n}`

### Breaking Changes

- **Laravel 10 projects must remain on v2.x** or upgrade Laravel before using v3.0.0.
- **Saloon 3 projects must move to Saloon 4** before installing v3.0.0.

### Notes

- Attached functions require ChromaDB 1.3.0+ (tests gracefully skip on older versions)
- New features degrade gracefully with appropriate error handling on unsupported versions
- ChromaDB 1.5.7 still reports `"1.0.0"` from `/api/v2/version`; CI passes `CHROMA_VERSION` explicitly when testing version-specific behavior

---

## [2.2.0] - 2025-10-28

### Added

- **Comprehensive Test Suite** - Achieved 80% code coverage with 139 new tests
    - **Unit Tests (116 tests)**: Core client, request classes, validation, embeddings factory, service provider
    - **Feature Tests (15 tests)**: Error handling, filter-based deletion, pagination edge cases
    - **Integration Tests (8 tests)**: Multi-tenancy workflows, tenant/database overrides
    - Total: 271 tests passing with 784 assertions
    - Test execution time: ~26-32 seconds for full suite

- **Developer Tooling** - Migrated from Makefile to justfile
    - Improved command organization with groups (setup, docker, test, workflow)
    - Built-in help system with `just --list`
    - Inline documentation for each command
    - Environment variable loading from .env
    - Enhanced cleanup command for Docker volumes and build artifacts

### Fixed

- **Test Documentation** - Improved skip messages for cloud-only features
    - Hybrid Search endpoint (not implemented in ChromaDB v1.0.x local)
    - Fork Collection (Chroma Cloud exclusive)
    - Get Collection by CRN (version-dependent, requires v1.0.21+)
    - All skip messages now include accurate version info and context

### Changed

- **Development Workflow** - Streamlined with justfile commands
    - `just workflow` - Full development cycle (docker, install, test)
    - `just quick` - Quick test cycle (docker, test)
    - `just test-laravel` - Test Laravel integration across versions 10, 11, 12
    - `just clean` - Enhanced cleanup of Docker volumes and build artifacts

---

## [2.1.1] - 2025-10-22

### Fixed

- **Laravel 12 Support** - Fixed dependency conflict preventing Laravel 12 installation
    - Updated `spatie/laravel-data` from `^3.10` to `^4.0` for Laravel 12 compatibility
    - Added `test-laravel-install.sh` for automated testing across Laravel versions
    - Verified package installation works correctly on Laravel 10.x, 11.x, and 12.x

### Changed

- **README Documentation** - Major reorganization for clarity
    - Emphasized framework-agnostic usage (works in any PHP 8.2+ project)
    - Moved Laravel-specific content to dedicated "Laravel Integration (Optional)" section
    - Split Quick Start into "Basic Usage" and "Laravel Usage" subsections
    - Clarified which features require Laravel vs framework-agnostic
    - Improved embedding provider documentation with separate "Direct Usage" and "Laravel Config" examples
    - Removed confusing dependency injection snippet from Quick Start

---

## [2.1.0] - 2025-10-22

### Added

- **Laravel 12 Support** - Added compatibility with Laravel 12.x
    - Updated `orchestra/testbench` to support `^8.8 || ^9.0 || ^10.0`
    - Supports Laravel 10.x, 11.x, and 12.x
    - Laravel 12 released February 24, 2025 with zero breaking changes

### Changed

- **Embedding Provider Documentation** - Significantly enhanced documentation
    - Added complete documentation for all 5 embedding providers
    - Direct instantiation examples for each provider (OpenAI, Voyage AI, Mistral AI, Jina AI, Ollama)
    - Factory method examples using `Embeddings` class
    - Constructor parameter documentation with defaults
    - Added guide for creating custom embedding functions
    - Documented `EmbeddingFunction` interface

- **Version Compatibility** - Updated ChromaDB server version range
    - Changed from `v1.0.0+` to `1.0.0 - 1.1.x` for better clarity
    - Latest tested ChromaDB server version: 1.1.1

- **Documentation** - General improvements
    - Added alternative package mention ([CodeWithKyrian/chromadb-php](https://github.com/CodeWithKyrian/chromadb-php))
    - Removed internal API verification report from README
    - Added project-specific guidelines in `claude.md`

### Fixed

- Documentation now shows all embedding provider classes and usage patterns
- All code examples verified and updated

---

## [2.0.0] - 2025-10-22

### 🎉 Major Release: Complete ChromaDB v2 API Support

This is a major release that implements complete support for the ChromaDB v2 API with **29/29 endpoints** (100% coverage). The v1 API has been deprecated by ChromaDB, and this version brings full compatibility with ChromaDB server v1.0.0+.

### 🚀 Added

#### Multi-Tenancy & Database Management

- **Multi-tenancy support** with tenant and database isolation
- `withTenant()` method for tenant context switching
- `withDatabase()` method for database context switching
- **Tenant Management API:**
    - `Tenant::create()` - Create new tenants
    - `Tenant::get()` - Retrieve tenant information
    - `Tenant::update()` - Update tenant configuration
- **Database Management API:**
    - `Database::create()` - Create new databases
    - `Database::get()` - Get database information
    - `Database::list()` - List all databases with pagination
    - `Database::delete()` - Delete databases

#### Enhanced Collections API

- `Collections::list()` - List all collections with pagination
- `Collections::fork()` - Fork/copy existing collections
- `Collections::getByCrn()` - Get collection by Collection Resource Name (CRN)
- Enhanced `Collections::create()` with `getOrCreate`, `metadata`, and `configuration` parameters
- Enhanced `Collections::update()` with `newMetadata` and `newConfiguration` support

#### Enhanced Items API

- `Items::search()` - Hybrid search with multiple search strategies
- `Items::addWithEmbeddings()` - Automatic embedding generation when adding items
- `Items::queryWithText()` - Query using text with automatic embedding generation
- Enhanced all methods with full v2 API parameter support
- Support for `uris` parameter across add/update/upsert operations

#### Server API Methods

- `Server::healthcheck()` - Check server health and readiness
- `Server::identity()` - Get current user identity and permissions
- `Server::heartbeat()` - Get nanosecond heartbeat timestamp
- `Server::preFlightChecks()` - Get pre-flight checks information
- `Server::version()` - Get server version
- `Server::reset()` - Reset server (with proper warnings)

#### Embedding Providers

- **OpenAI** embeddings support (text-embedding-3-small, text-embedding-3-large, ada-002)
- **Voyage AI** embeddings support (voyage-3.5, voyage-3-large, voyage-code-3, domain-specific models)
- **Mistral AI** embeddings support (mistral-embed)
- **Jina AI** embeddings support (v3, v2-base-en, v2-small-en, clip-v2)
- **Ollama** local embeddings support (any Ollama model)
- `Embeddings::fromConfig()` for Laravel configuration integration
- Named constructor methods: `Embeddings::openai()`, `Embeddings::voyage()`, etc.

#### Configuration & Environment

- Comprehensive `config/chromadb.php` with embedding provider configuration
- Default tenant and database configuration
- Full `.env` variable documentation
- Environment variable examples for all providers

#### Documentation

- Complete API documentation for all 29 endpoints
- Database Management section
- Tenant Management section
- Server API documentation
- Multi-tenancy usage patterns
- Semantic search example
- Comprehensive API verification report
- All examples verified against v2 specification

#### Testing

- Comprehensive test suite covering all 29 endpoints
- `DatabaseManagementTest` for database operations
- `TenantManagementTest` for tenant operations
- `SemanticSearchTest` for end-to-end semantic search
- Integration tests for all embedding providers
- Tests for multi-tenancy scenarios
- 100% API coverage verification

### ⚠️ Breaking Changes

#### Return Type Changes

- **`Collections::count()`** now returns `int` instead of `Response`

    ```php
    // v1
    $response = $chromadb->collections()->count();
    $count = (int) $response->body();

    // v2
    $count = $chromadb->collections()->count(); // Returns int directly
    ```

- **`Items::count()`** now returns `int` instead of `Response`

    ```php
    // v1
    $response = $chromadb->items()->count($collectionId);
    $count = (int) $response->body();

    // v2
    $count = $chromadb->items()->count($collectionId); // Returns int directly
    ```

#### Metadata Constraints

- **v2 API only supports scalar values in metadata** (no nested arrays)

    ```php
    // v1 - Arrays allowed (no longer works)
    $metadata = ['tags' => ['php', 'laravel', 'vector-db']];

    // v2 - Only scalars allowed
    $metadata = ['tags' => 'php, laravel, vector-db'];
    ```

#### Embedding Format Requirements

- Embeddings **must be numeric vectors** (arrays of floats)
- String embeddings are no longer accepted

    ```php
    // Wrong - String embeddings
    embeddings: ['embedding1', 'embedding2']

    // Correct - Numeric vectors
    embeddings: [[0.1, 0.2, 0.3], [0.4, 0.5, 0.6]]
    ```

#### Configuration Changes

- **Host configuration now requires protocol**
    - Before: `'host' => 'localhost'`
    - After: `'host' => 'http://localhost'`
    - Prevents malformed URLs in Laravel service provider

#### API Endpoint Changes

- All v2 endpoints now use tenant/database path parameters
- Default values: `default_tenant` and `default_database`
- CRN (Collection Resource Name) format introduced for global collection access

### 🔄 Changed

#### Embeddings

- Renamed `EmbeddingFactory` to `Embeddings` class
- Renamed `embed()` method to `generate()`
- Improved embedding provider configuration structure
- Updated model defaults (e.g., Voyage AI now defaults to `voyage-3.5`)

#### Request Classes

- All 29 request classes updated for v2 API compatibility
- Enhanced parameter validation
- Proper handling of tenant/database context
- Query parameters separated from body parameters where appropriate

#### PHP Version Requirement

- **Minimum PHP version: 8.2+** (updated from 8.1)

### 🐛 Fixed

- Configuration host default now includes protocol to prevent connection failures
- README examples corrected with proper v2 API formats
- Semantic search example fixed for v2 response structure
- Parameter naming conventions properly converted (camelCase ↔ snake_case)
- All documentation inaccuracies corrected

### 📚 Documentation

- Added comprehensive [API Verification Report](./docs/API_VERIFICATION_REPORT.md)
- Complete documentation for all 34 public methods across 5 resources
- Updated all code examples to v2 API format
- Added environment variable reference guide
- Fixed broken documentation links
- Added multi-tenancy usage patterns
- Enhanced semantic search example

### 🔒 Security

- No security vulnerabilities addressed in this release
- All API calls require authentication via token
- Multi-tenancy provides data isolation

### 🏗️ Internal

- Upgraded to Saloon v3 for HTTP client
- Improved code organization and structure
- Enhanced test coverage (90+ passing tests)
- Added comprehensive verification against OpenAPI spec
- Clean separation of concerns between Request and Resource classes

---

## [1.x] - Previous Versions

Previous versions used the deprecated ChromaDB v1 API. See git history for details.

### Migration from v1 to v2

For a complete migration guide, see the [API Verification Report](./docs/API_VERIFICATION_REPORT.md) which includes:

- All breaking changes documented
- Before/after code examples
- Parameter mapping reference
- New features guide

#### Quick Migration Checklist

- [ ] Update to PHP 8.2+
- [ ] Change `count()` calls to handle `int` return type
- [ ] Convert metadata arrays to scalar values
- [ ] Ensure embeddings are numeric vectors
- [ ] Add `http://` to `CHROMADB_HOST` environment variable
- [ ] Update from `EmbeddingFactory` to `Embeddings` class
- [ ] Change `embed()` calls to `generate()`
- [ ] Test multi-tenancy if using multiple customers/projects
- [ ] Review new methods (database management, tenant management, search)

---

## Links

- [GitHub Repository](https://github.com/helgesverre/chromadb)
- [Issue Tracker](https://github.com/helgesverre/chromadb/issues)
- [ChromaDB Documentation](https://docs.trychroma.com/)
- [API Verification Report](./docs/API_VERIFICATION_REPORT.md)
