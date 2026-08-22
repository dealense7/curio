# Technical Design

This document describes the concrete implementation patterns used by this Laravel application. Read it together with [`system-design.md`](system-design.md); system design defines the rules, while this document defines their project-specific implementation.

## Application layers

Business operations follow this dependency direction:

```text
HTTP controller
    ↓
service contract → service
                      ↓
              repository contract → repository/cache repository
```

- Controllers receive service contracts through dependency injection.
- Services authorize operations, open business transactions, and depend on repository contracts.
- Repositories own query construction, filters, sorting, pagination, and persistence.
- Cache repositories decorate repository contracts and invalidate their own cache after writes.
- Controllers do not query Eloquent directly and do not depend on concrete repositories.

## Domain organization

Organize domain classes by layer and business domain:

```text
app/Models/{Domain}
app/Contracts/Services/{Domain}
app/Contracts/Repositories/{Domain}
app/Services/{Domain}
app/Repositories/{Domain}
app/CacheRepositories/{Domain}
app/Http/Controllers/{Layer}/{Domain}
app/Http/Requests/{Layer}/{Domain}
app/Http/Resources/{Domain}
app/Policies/{Domain}
app/Filters/{Layer}/{Domain}
database/factories/{Domain}
database/seeders/{Domain}
tests/Integration/{Layer}/{Domain}
```

General/reference models belong under `App\Models\General`. Shared traits belong under `App\Support\Traits`. Shared filters belong in the nearest common filter namespace, such as `App\Filters\Admin\FilterByIsActive`, and are reused by domain repositories.

## Models and persistence

- Extend `App\Models\Model`.
- Use `HasPublicId` for application models exposed through APIs.
- Add `SoftDeletes` for recoverable CRUD deletion where the domain does not use an explicit archive lifecycle.
- Define `$fillable`, casts, sortable fields, and default sorting explicitly.
- Add typed getters for all meaningful fields; nullable columns return nullable types.
- Use shared Blueprint macros where applicable.
- Accept public ULIDs in API requests and resolve them through service contracts before persistence.

## Requests, resources, and controllers

- Index endpoints use a dedicated index request for filters, sorting, page, and per-page values.
- Controllers extract request values with shared API controller helpers.
- Create and update requests validate shape only; cached business references and uniqueness checks are resolved through services in the controller flow.
- Resources expose model values through the model/resource transformation conventions and include loaded relations using the established `whenLoaded` pattern.
- Use explicit branches and named local variables for multi-step flows. Avoid compressed arrays, nested ternaries, and dense one-line methods.

## Filters and sorting

- Each filter has one responsibility and is registered through the repository's `getFilters()` method.
- Related-resource filters query relationships using public ULIDs; unknown index references return an empty successful collection.
- Models declare allowed sort fields and optional default sorting through `Sortable`.
- Repositories apply `$model->parseSort($sort)` for both item and paginated queries.

## File attachments

The shared file foundation is `App\Models\General\File`. Domain models that support attachments expose a typed relationship:

```php
public function files(): MorphMany
{
    return $this->morphMany(File::class, 'fileable');
}
```

The file foundation owns file metadata, file enums, soft deletion, and polymorphic columns. Upload/download workflows remain in the General File subsystem and are not reimplemented inside each domain.

## Integration and architecture tests

Integration tests live under `tests/Integration/{Layer}/{Domain}` with one operation per file. Use the order unauthorized → validation → forbidden → not found → success where applicable, and create records through `ProvidesTestingData` helpers.

Architecture tests live under `tests/Architecture`. They use Pest Arch expectations and do not connect to the database. They enforce namespace grouping, strict types, layer dependency direction, shared-trait placement, and other stable structural rules. Feature behavior remains covered by integration tests.
