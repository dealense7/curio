# Database And Model Conventions

## Core Columns
- Primary key: `id` as `bigint` via `$table->id()`.
- Public identifier: `public_id` as ULID, unique, always exposed in APIs.
- Tenant key: `company_id` as `bigint` for company-owned records.
- Archive field: `archived_at` nullable timestamp for manual archival.
- Optimistic lock: `version` unsigned integer when concurrent edits matter.
- Timestamps: `created_at` and `updated_at` in UTC with `timestampsTz()`.
- Actor fields: `created_by` and `updated_by` when audit ownership matters.

## Migration Helpers
- These conventions are bootstrapped in `App\Providers\ModelConventionServiceProvider`.
- Use the provided schema macros in migrations instead of rewriting the same columns by hand.
- Import `App\Support\Database\BlueprintMacros` and annotate the migration `$table` variable as `Blueprint&BlueprintMacros` so IDEs and static analysis know the macro methods exist.
- Available helpers:
  - `$table->publicId()`
  - `$table->companyKey()`
  - `$table->companyKey(nullable: true)` is not supported; use `$table->companyKey(true)` for nullable ownership
  - `$table->archivable()`
  - `$table->optimisticLock()`
  - `$table->actorColumns()`

Example:

```php
use App\Support\Database\BlueprintMacros;

Schema::create('orders', function (Blueprint $table) {
    /** @var Blueprint&BlueprintMacros $table */
    $table->id();
    $table->publicId();
    $table->companyKey();
    $table->string('status');
    $table->archivable();
    $table->optimisticLock();
    $table->actorColumns();
    $table->timestampsTz();
});
```

## Value Types
- Enums: use PHP backed enums and persist them as strings.
- Money: store as `*_amount_minor bigint` plus `currency_code char(3)`.
- Coordinates: `decimal(10,7)`.
- Weight: `decimal(12,3)`.
- Dimensions: `decimal(10,2)`.

## Tenant Pattern
- Every company-owned query must declare company context explicitly.
- Standard query entry point: `Model::query()->forCompany($companyId)`.
- Controllers should not guess tenant scope. Services or repositories must pass the company context into every company-owned query.
- Never expose cross-company reads or writes without an explicit privileged path.
- Prefer repository filters for archived or active record selection instead of model scopes such as `scopeArchived()`.

## Foreign Keys And Indexes
- Foreign keys use `<relation>_id`.
- Required relations default to `restrictOnDelete()`.
- Nullable audit relations default to `nullOnDelete()`.
- Use `cascadeOnDelete()` only for true dependent rows such as pivot tables or children that have no meaning without the parent.
- Index names follow Laravel defaults unless a custom name clarifies a composite index.
- Company-owned tables should index `company_id` and common lookup pairs such as `company_id + public_id`.

## API Serialization
- Public APIs expose `public_id`, never sequential internal `id`.
- Dates serialize as UTC ISO-8601 strings.
- Money serializes as `{ amount_minor, currency_code }`.
- Enums serialize as backed string values.
- Resource classes should extend the repository base JSON resource and read data through model getters such as `getName()` or `getCreatedAt()`, not raw `->attribute` access.
- Internal audit and locking fields stay private unless a contract explicitly requires them.

## Validation And Persistence
- Validate enums in requests with `Rule::enum(...)` before persistence.
- Keep internal columns guarded by default: `id`, `public_id`, `company_id`, `version`, audit columns, and timestamps.
- `HasPublicId` generates ULIDs automatically on create.
- `Archivable` provides the manual archive pattern where needed.
