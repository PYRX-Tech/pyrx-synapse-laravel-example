# Synapse Laravel Example

All 14 SDK endpoints with [pyrx/synapse](https://synapse.pyrx.tech/developers/sdks/php) + Laravel 11 API-only.

## Setup

1. `composer install`
2. Copy `.env.example` to `.env` and fill in your keys
3. `php artisan serve --port=4011`

## Endpoints

**Core:** POST /api/track, /api/track/batch, /api/identify, /api/identify/batch, /api/send
**Contacts:** GET /api/contacts, PUT/DELETE /api/contacts/:id
**Templates:** GET/POST /api/templates, GET/PUT/DELETE /api/templates/:slug, POST /api/templates/:slug/preview

- [Synapse Docs](https://synapse.pyrx.tech/developers)
