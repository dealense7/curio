# Structure

## Purpose
This file defines the target repository structure and architectural rules for AI agents and contributors making code changes here. Follow these rules when adding new features so the project grows with a consistent pattern.

## Core Architecture
- Prefer a layered Laravel API structure: `request -> controller -> service -> repository -> model/resource`.
- Keep controllers thin. Controllers should validate input, call a service, and return a response or resource.
- Keep business logic in services, not in routes, controllers, or models.
- Keep query and persistence logic in repositories.
- When a service or repository is introduced, add a matching contract interface and bind it through a service provider.

## Application Structure
- Keep domain code in `app/`.
- Put controllers in `app/Http/Controllers`.
- Put request validation in `app/Http/Requests`.
- Put API or view resources in `app/Http/Resources` when response transformation becomes non-trivial.
- Put service classes in `app/Services`.
- Put repository classes in `app/Repositories`.
- Put cache-backed repository decorators in `app/CacheRepositories` if caching is added.
- Put interfaces in `app/Contracts/Services`, `app/Contracts/Repositories`, and `app/Contracts/Requests`.
- Put Eloquent models in `app/Models`.
- Put enums in `app/Enums` when domain constants start to multiply.
- Put reusable validation or domain rules in `app/Rules`.
- Put shared helpers, response wrappers, or utilities in `app/Support`.
- Put custom bindings in `app/Providers`, with interface-to-class registration handled by a dedicated binding provider such as `BindingServiceProvider`.

## Routing Rules
- Keep browser routes in `routes/web.php`.
- Keep console routes in `routes/console.php`.
- If the project adds an API, place routes in `routes/api.php` or split them by domain under `routes/api/*.php`.
- Group routes by domain, not by HTTP verb count.
- Keep route files declarative; do not place business logic in closures beyond trivial cases.

## Domain Organization
- Organize new code by domain first, then by layer when that keeps related code easier to find.
- Typical domains may include users, contacts, deliveries, orders, auth, and shared platform concerns.
- If a domain grows, mirror it across layers. Example:
  - `app/Contracts/Repositories/ContactRepositoryContract.php`
  - `app/Repositories/ContactRepository.php`
  - `app/Contracts/Services/ContactServiceContract.php`
  - `app/Services/ContactService.php`
  - `app/Http/Requests/Contact/StoreRequest.php`
  - `app/Http/Controllers/ContactController.php`

## Binding Rules
- Do not resolve concrete service or repository classes directly in higher layers when a contract exists.
- Bind interfaces to implementations in a provider under `app/Providers`.
- Keep binding names predictable so agents can discover the intended implementation quickly.
- When adding caching decorators, bind contracts to the cache repository wrapper, and let that wrapper delegate to the underlying repository.

## Repository Rules
- Repositories own query composition, filtering, pagination, eager loading, and persistence details.
- Services orchestrate repositories, authorization, validation flow, transactions, and domain behavior.
- Do not duplicate the same query logic across controllers and services.
- Prefer transactions in services when a use case touches multiple repositories or models.

## Cache Repository Rules
- Use `app/CacheRepositories` only for stable read-heavy queries.
- Keep cache repositories interface-compatible with the repository contracts they decorate.
- Do not bury core business logic in cache decorators; caching should stay a wrapper concern.
- Cache keys, TTLs, and invalidation strategy should be explicit and easy to trace.

## Change Rules
- Do not change application behavior, database structure, dependencies, environment settings, or public interfaces without explicit approval first.
- If the request is ambiguous or could affect behavior, ask before editing code.
- Safe without asking: documentation updates, formatting with existing tools, non-behavioral refactors, and tests that describe current behavior.

## Implementation Rules
- Follow Laravel conventions before introducing custom structure.
- Reuse existing config, routes, providers, and test structure instead of creating parallel patterns.
- If a feature is simple today but clearly domain-heavy, prefer starting with service and repository boundaries early.
- Avoid dumping logic into `helpers.php`-style globals; prefer typed classes in `app/Support`, services, or rules.
- Add new folders only when they support a repeated pattern, not a one-off preference.

## Style Rules
- Follow `.editorconfig`: UTF-8, LF, 4 spaces, and 2 spaces for YAML.
- Use PSR-12 style for PHP.
- Format PHP with `./vendor/bin/pint`.
- Use clear Laravel naming: `UserService`, `UserRepository`, `UserController`, `StoreUserRequest`, `UserResource`, `UserRepositoryContract`.

## Testing Rules
- Add feature tests for route and integration behavior.
- Add unit tests for isolated logic.
- Name test files with the `*Test.php` suffix.
- Run checks with `composer test`.
- When adding services or repositories with meaningful logic, add tests that cover success, failure, authorization, and persistence behavior.
- The current base test classes are `tests/TestCase.php` and Pest bootstrap in `tests/Pest.php`; extend those instead of inventing a parallel test entry point.
- Keep shared Pest expectations, helper functions, and suite-wide setup in `tests/Pest.php`.
- If repeated test data setup appears, create a dedicated helper or trait under `tests/Support` or `tests/Helpers` and reuse it across feature tests.
- If factory-heavy setup becomes common, prefer expressive helper methods that create complete domain scenarios instead of duplicating raw model creation in each test.
- When shared helpers are introduced, keep them focused on test data creation, auth setup, and common assertions, not production business logic.
