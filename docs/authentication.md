# Authentication

This project uses Laravel Passport with custom internal grants, aligned with the `new-api` structure, but without OTP.

## Setup

After installing Composer dependencies, run:

```bash
docker compose exec app php artisan migrate
docker compose exec app php artisan passport:keys --force
docker compose exec app php artisan auth:client --name="Default API Client"
```

Use the generated client ID for token and refresh requests.

The command restricts clients to the internal login and refresh flow. Public
clients are appropriate for browser or mobile applications that cannot
protect a secret. For a trusted server-side client, add `--confidential` and
store the displayed client secret securely. Confidential clients must send
`client_secret` with token and refresh requests.

## Token Request

Password login uses the custom `internal` grant:

```bash
curl --location 'http://localhost:8090/api/auth/token' \
  --header 'Accept: application/json' \
  --header 'Content-Type: application/json' \
  --data '{
    "grant_type": "internal",
    "client_id": "YOUR_CLIENT_ID",
    "login": "admin@example.com",
    "password": "secret"
  }'
```

Refresh uses the custom `internal_refresh_token` grant:

```bash
curl --location 'http://localhost:8090/api/auth/token' \
  --header 'Accept: application/json' \
  --header 'Content-Type: application/json' \
  --data '{
    "grant_type": "internal_refresh_token",
    "client_id": "YOUR_CLIENT_ID",
    "refresh_token": "YOUR_REFRESH_TOKEN"
  }'
```

## Authenticated Endpoints

- `GET /api/auth/me`
- `GET /api/auth/acl`
- `DELETE /api/auth/token`

Send:

```text
Authorization: Bearer <access_token>
Accept: application/json
```
