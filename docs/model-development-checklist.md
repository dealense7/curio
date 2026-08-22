# Model Development Checklist

- Add a migration with an ID, public ULID, timestamps, and only the fields the model needs.
- Use the shared migration macros and PHP backed enums.
- Extend the application model base and add archive behavior only when required.
- Keep internal fields guarded and define explicit write inputs.
- Expose records through a JSON resource using model getters.
- Add typed getters for every model field that is consumed by services, resources, or tests, including nullable return types where the database field is nullable.
- Add a factory with valid defaults.
- Add tests for identifiers, validation, authorization, persistence, and serialization where relevant.
