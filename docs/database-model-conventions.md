# Database And Model Conventions

Use bigint primary keys, unique ULIDs for public identifiers, timezone-aware timestamps, nullable archive markers where needed, and PHP backed enums stored as strings.

Use the shared Blueprint macros for public IDs, archival, optimistic locking, actor columns, enums, money, coordinates, weight, and dimensions.

Keep internal identifiers, audit fields, locking fields, and timestamps guarded. Public APIs expose ULIDs and enum values, not internal IDs or enum objects. Existing shared foundations such as General File may use their established UUID and integer-enum storage format; new business models should use the standard application macros and string-backed enums unless the domain explicitly integrates with that foundation.

Use explicit tenant keys only for models that are genuinely tenant-scoped. Tenant context must be resolved by the application and never trusted from request body fields.
