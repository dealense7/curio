# System Design

## API Localization

Every human-readable string returned by an API must be defined in its own dedicated language file under lang/.

Do not hard-code API response messages in controllers, services, repositories, resources, or exceptions. Use translation keys and create a domain-specific language file when the domain does not already have one.
