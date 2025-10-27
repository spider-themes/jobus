<!--
Sync Impact Report:
Version change: 1.0.0 → 1.1.0 (MINOR: Added new principle)
Added sections:
- Principle VI: Code Deduplication & Reusability (new)
Modified principles:
- None renamed
Removed sections:
- None
Templates requiring updates:
✅ .specify/templates/plan-template.md (added DRY to Constitution Check)
✅ .specify/templates/spec-template.md (added reusability to requirements)
✅ .specify/templates/tasks-template.md (added refactoring task category)
Follow-up TODOs:
- None
-->

# Jobus WordPress Plugin Constitution

## Core Principles

### I. WordPress Integration Standards
WordPress plugins MUST follow official WordPress coding standards and integration patterns. All code MUST:
- Follow WordPress PHP coding standards and naming conventions
- Use WordPress hooks, filters, and actions for extensibility
- Integrate with WordPress user roles and capabilities system
- Support WordPress multisite installations
- Use WordPress database schema conventions

Rationale: Ensures compatibility, maintainability, and adherence to WordPress ecosystem best practices.

### II. Test-Driven Development
All features MUST follow TDD principles with comprehensive test coverage:
- Unit tests for business logic and data models
- Integration tests for WordPress hooks and filters
- End-to-end tests for user journeys
- Performance benchmarks for database queries
- Accessibility testing for frontend components

Rationale: Ensures reliability, maintainability, and prevents regressions during updates.

### III. User Experience Consistency
All user interfaces MUST maintain consistent design and behavior:
- Follow WordPress admin UI patterns for backend interfaces
- Use consistent styling for frontend components
- Implement responsive design for all screen sizes
- Support RTL languages
- Meet WCAG 2.1 Level AA accessibility standards

Rationale: Delivers professional, intuitive user experience across all plugin interfaces.

### IV. Performance First
All code MUST meet strict performance requirements:
- Database queries MUST be optimized with proper indexing
- Assets MUST be minified and loaded conditionally
- AJAX calls MUST be minimized and batched where possible
- Caching MUST be implemented for expensive operations
- Memory usage MUST be monitored and optimized

Rationale: Ensures plugin scalability and optimal WordPress site performance.

### V. Security Best Practices
All code MUST follow WordPress security best practices:
- Properly escape and validate all data input/output
- Use WordPress nonces for form submissions
- Follow WordPress permissions and capabilities model
- Implement rate limiting for API endpoints
- Regular security audits and updates

Rationale: Protects user data and maintains site security.

### VI. Code Deduplication & Reusability
All code MUST follow DRY (Don't Repeat Yourself) principles:
- Duplicate code patterns MUST be extracted into reusable components
- Template fragments MUST be consolidated into template parts
- Repeated logic MUST be abstracted into utility functions or classes
- Code duplication MUST be monitored and addressed within sprint cycles
- Reusable components MUST be documented and discoverable

Rationale: Reduces maintenance burden, prevents inconsistencies, improves code quality and performance.

## WordPress Development Standards

### Technical Requirements
- PHP Version: 7.4 or higher
- WordPress Version: 6.0 or higher
- MySQL/MariaDB: 8.0/10.5 or higher
- HTTPS Support Required

### Code Organization
- Follow WordPress plugin directory structure
- Implement PSR-4 autoloading for classes
- Separate business logic from presentation
- Use template files for frontend views
- Maintain clean separation of concerns

### Development Tools
- Composer for PHP dependencies
- npm for asset compilation
- PHPCS for code style enforcement
- PHPUnit for testing
- wp-cli for WordPress CLI integration

## Quality Assurance Process

### Code Review Standards
1. All code changes MUST be reviewed
2. Testing requirements MUST be met
3. Performance impact MUST be assessed
4. Security implications MUST be evaluated
5. Documentation MUST be updated

### Release Process
1. Version numbers follow semantic versioning
2. Changelog MUST be maintained
3. Release notes MUST be comprehensive
4. Migration scripts MUST be tested
5. Backward compatibility MUST be maintained

## Governance

1. Constitution Amendment Process:
   - Proposals MUST be documented in issues
   - Impact analysis MUST be provided
   - Core team approval required
   - Migration plan MUST be included

2. Version Control:
   - Feature branches MUST follow naming convention
   - Commits MUST reference issues
   - Pull requests MUST include tests
   - CI/CD pipelines MUST pass

3. Quality Gates:
   - All PRs MUST pass automated tests
   - Code coverage MUST not decrease
   - Performance benchmarks MUST pass
   - Security scans MUST pass

4. Documentation Requirements:
   - Code MUST be documented inline
   - API documentation MUST be maintained
   - User documentation MUST be updated
   - Breaking changes MUST be documented

5. Review Process:
   - Code reviews within 48 hours
   - Two approvals required for merge
   - Technical leads have final authority
   - Security reviews for sensitive changes

This constitution governs all development work on the Jobus WordPress plugin. Exceptions require explicit approval and documentation.

**Version**: 1.1.0 | **Ratified**: 2025-10-01 | **Last Amended**: 2025-10-27