# System Design Guidelines

## Application architecture

Use the project’s established layering for business operations:

```text
controller / middleware
        ↓
service contract
        ↓
service
        ↓
repository contract
        ↓
repository or cache repository
```

- Depend on service contracts at application boundaries.
- Depend on repository contracts inside services for reads and writes.
- Let the versioned repository binding resolve cacheable repository contracts to the cache repository.
- Let cache repositories delegate writes to the raw repository and invalidate their own cache.
- Keep transactions in services around business operations.
- Do not add service methods that only wrap a single permission check; keep simple permission checks direct and readable.

## Readability

- Prefer explicit branches, early returns, or named private methods for multi-path logic.
- Avoid nested or multi-line ternary expressions.
- Use backed enum cases whenever an enum exists instead of repeating raw strings; use `->value` for database queries.
- Keep authorization and tenant-context decisions easy to follow at the call site.
- Put model lifecycle behavior in observers when the project has an observer for that model; keep domain business logic out of `Model::booted()`.

## Tenant context

- Platform/admin routes use the permission system and do not require tenant middleware.
- Operational tenant routes resolve context through the relevant service contract.
- Never trust a tenant identifier from request input to select context.
- Never use `users.company_id` for tenant resolution.
- Treat a membership as operationally effective only when membership status is `active`, `archived_at` is null, and its membership contract is currently effective.
- A contract is effective when `start_at` has arrived, `end_at` is null or in the future, and `cancelled_at` is null. Date validity is evaluated at request time; no scheduled status mutation is required.
- `end_at = null` means an unlimited contract. Contract permissions are direct grants on the contract and never become role permissions.

## Authorization

- Use middleware only when the request needs an active company context or inactive-membership rejection.
- Use policies or services to authorize the operation-specific company roles.
- Do not put role checks in controllers.
- Do not use role middleware for operation-specific authorization when a policy can express the rule.
- For example, a delivery-route creation policy should check whether the resolved user has an allowed membership role such as `company_owner`, `company_admin`, or `dispatcher`.
- Platform users should be handled through the existing permission system.
- Company roles are company-scoped records. Website-admin Spatie roles must not be reused as company roles because one user can belong to multiple companies.
- A permission must be explicitly marked company-assignable before it can be attached to a company role or membership contract. Website-admin-only permissions remain unavailable to company ACLs.
- Company authorization is the union of the effective company role permissions and effective contract permissions, with platform permission checks handled separately.

## Public Identifier References

- API create and update requests must accept public ULIDs for related resources, even when the database column is named with an internal suffix such as country_id or currency_id.
- Resolve related public ULIDs through their service contracts and cached repository path before converting them to internal database IDs.
- If a related public ULID does not resolve, use the controller custom validation-error flow for the specific request field. Do not use direct database exists rules for these cached references.
- Request validation rules must not perform direct database lookups for business uniqueness checks. Resolve those checks through the controller's service contract and its repository/cache-repository path, then use the custom validation-error flow. Keep the database unique constraint as the final concurrency safeguard.

## Index Operations

- Index endpoints should accept a dedicated index request for filters, page, per-page, and sort input.
- Controllers should extract those values with the shared API controller helpers and pass them to the service.
- Services authorize the operation; repositories pass filters into the repository pipeline, apply safe sort columns, and paginate the result.
- Each repository filter should have one responsibility, receive the filter array through the pipeline payload, and be listed by the repository's `getFilters()` method. Do not repeat filter conditions inline in repository query methods.
- Sortable models use the shared `Sortable` trait. The model declares its additional sortable fields in `$sortFields`; `id` is always included by default and is the fallback sort (`id` descending).
- Repositories must use the model's `parseSort()` result for ordering instead of maintaining a separate hard-coded sortable-field list.
- Add ItemsTest coverage for unauthorized, forbidden, filtered/sorted results, and PaginateListTest coverage when pagination applies.

## Shared Test Structures

- Every item structure helper in ProvidesItemStructures must accept an optional relations array.
- The helper should copy its base structure, apply nested relations through includeNestedRelations, and return the resulting structure.
- This keeps future relation assertions additive without changing existing test method signatures.

## Test Data Providers

- Domain Integration tests must create records through ProvidesTestingData helpers rather than calling model factories directly.
- Prefer one helper per domain item with the signature createDomainRandomItem(array params = [], int count = 1).
- Pass scenario-specific values such as flags, names, and statuses through params instead of creating separate helper methods for each variation.
- Add factory state methods only when the state represents reusable domain behavior beyond a simple attribute override.
- Keep request data readable by assigning filters, sorting, pagination, or payload values to a named `$data` variable before calling the request helper.
- Keep response assertions as separate statements. Avoid long assertion chains so each expected response property is easy to scan and maintain.

## Domain-oriented organization

Organize application code by both layer and business domain. When a class belongs to a domain, place it under that domain rather than in a flat global namespace. This applies to services, service contracts, repository contracts, repositories, cache repositories, policies, events, middleware, jobs, listeners, requests, and resources.

New business models should include a domain seeder under `database/seeders/{Domain}` when development or test environments need representative records. Register the seeder in `DatabaseSeeder`, seed required foreign-key references from existing records, and use idempotent operations such as `updateOrCreate` so repeated seeding is safe.

For any domain, use corresponding namespaces such as:

- `App\Contracts\Services\{Domain}`
- `App\Contracts\Repositories\{Domain}`
- `App\Services\{Domain}`
- `App\Repositories\{Domain}`
- `App\CacheRepositories\{Domain}`
- `App\Events\{Domain}`

Use the existing domain folder structure as the guide and avoid placing domain-specific classes directly under generic namespaces such as `App\Events` or `App\Services`.

Membership lifecycle observers belong under `App\Observers`; membership and contract audit events belong under `App\Events\CompanyMembership`; company-role permission events belong under `App\Events\CompanyRole`.

## Integration test organization

Organize integration tests by layer and domain:

```text
tests/Integration/{Layer}/{Domain}/
├── ModelTestCase.php
├── ModelTest.php
├── CreateTest.php
├── UpdateTest.php
├── ShowTest.php
├── DeleteTest.php
├── ItemsTest.php
├── PaginateListTest.php
└── TreeTest.php
```

Create only the files that apply to the domain. Keep one operation per file. Within each operation file, use this test order where applicable: unauthorized, validation errors, forbidden permission, not found, then successful operation. Reuse the domain `ModelTestCase`, `IntegrationTestCase`, `DatabaseTransactions`, `ProvidesTestingData`, and shared request/response helpers.
