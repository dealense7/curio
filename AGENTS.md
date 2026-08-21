# Repository Guidelines

## Project Structure & Module Organization
This repository is a Laravel 13 application. Core PHP code lives in `app/`, HTTP routes in `routes/`, configuration in `config/`, and database migrations, factories, and seeders in `database/`. Frontend assets are in `resources/css` and `resources/js`, with compiled output handled by Vite. Public entry points and static files live in `public/`. Tests are organized under `tests/Feature` for HTTP or integration coverage and `tests/Unit` for isolated logic.

## Build, Test, and Development Commands
Use `composer setup` for first-time project setup: it installs PHP and Node dependencies, creates `.env` if needed, generates the app key, runs migrations, and builds frontend assets. Use `composer dev` for local development; it starts the Laravel server, queue listener, log tailing, and Vite in parallel. Use `composer test` to clear config cache and run the full test suite. Frontend-only work can use `npm run dev` for Vite hot reload and `npm run build` for a production asset build.

## Coding Style & Naming Conventions
Follow PSR-12 style and Laravel conventions. `.editorconfig` sets UTF-8, LF line endings, and 4-space indentation for most files; YAML uses 2 spaces. Format PHP with `./vendor/bin/pint`. Use `StudlyCase` for classes, `camelCase` for methods and variables, and Laravel’s default naming for migrations such as `create_orders_table`. Keep controllers in `app/Http/Controllers` and Eloquent models in `app/Models`.

## Testing Guidelines
This project uses Pest on top of PHPUnit. Add feature tests in `tests/Feature` and unit tests in `tests/Unit`; name files with the `*Test.php` suffix. Prefer covering new routes, validation rules, model behavior, and database side effects with focused tests. Run tests with `composer test` or `php artisan test`.

## Commit & Pull Request Guidelines
Current history uses short, imperative commit messages such as `Install Pest` and `Set up a fresh Laravel app`. Keep that pattern: start with a verb, describe one logical change, and avoid noisy WIP commits. Pull requests should include a short summary, testing notes, linked issues when relevant, and screenshots only for UI changes.

## Security & Configuration Tips
Do not commit secrets from `.env`. Use `.env.example` as the template for new variables. Review `config/`, queue settings, and database migrations before deploying changes that affect runtime behavior or stored data.

## Data Modeling Conventions
Apply these conventions to every new business table and model:

- Use `id` as an auto-incrementing `bigint` primary key and `public_id` as a unique ULID for external identifiers.
- Tenant-scoped tables use a `company_id` `bigint` foreign key. Global/reference tables do not add a tenant key.
- Use nullable `archived_at` for archival; do not use `deleted_at` or `softDeletes()` for business records.
- Add an unsigned integer `version` optimistic-lock column when concurrent editing is possible.
- Store `created_at` and `updated_at` as timezone-aware timestamps; the application timezone is UTC.
- Add nullable `created_by` and `updated_by` user foreign keys where actor history is useful.
- Use PHP backed enums with varchar columns, never database `enum` columns.
- Store money as `*_amount_minor` (`unsignedBigInteger`) with a paired `*_currency_code` `char(3)`.
- Use decimal `(10,7)` for coordinates, `(12,3)` for weight, and `(10,2)` for dimensions.

Use the shared `Blueprint` macros (`publicId`, `companyKey`, `archivable`, `optimisticLock`, `actorColumns`, `enumString`, `money`, `coordinates`, `weight`, and `dimensions`) in migrations so these rules remain consistent. Before committing, audit new migrations and casts against this section and run the test suite.
