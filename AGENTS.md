# Repository Guidelines

This is a Laravel application. Keep domain code organized by layer and business domain under app/, routes under routes/, database artifacts under database/, and tests under tests/.

Use the contract -> service -> repository/cache-repository pattern for business operations. Keep controllers thin, use policies or services for authorization, and resolve tenant context through application services rather than request body fields.

Use PHP backed enums whenever a supported enum exists. Store enum values as strings and use enum cases in assignments, validation, queries, factories, and authorization.

Use bigint primary keys, unique ULIDs for public identifiers, timezone-aware timestamps, nullable archive markers, and shared Blueprint macros. Keep internal IDs, audit columns, locking fields, and timestamps guarded.

Use the established Integration test structure and ordering. Run composer phpcs and relevant tests before completing changes.

API responses must use translation keys for human-readable strings. Create a dedicated language file under lang/ for each domain that returns localized API messages.
