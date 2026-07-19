# Model Development Checklist

- Add a migration with `$table->id()` and `$table->publicId()`.
- Use `timestampsTz()` and add `archived_at`, `company_id`, `version`, or actor fields only when the model needs them.
- Extend `App\Models\Model` for normal Eloquent models.
- Add `BelongsToCompany` when the record is company-owned.
- Add `Archivable` when records are manually archived instead of deleted.
- Keep internal fields guarded and define explicit fillable attributes only for true write inputs.
- Use PHP backed enums and validate them with `Rule::enum(...)`.
- Expose the model through a JSON resource that returns the public identifier and reads values via model getters.
- Add a factory that creates valid defaults without manually setting internal keys.
- Add tests for public ID generation, enum validation, tenant scoping, and resource serialization where relevant.
