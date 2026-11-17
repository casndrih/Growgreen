# Grogreen

A small multi-vendor grocery e-commerce platform built with **Laravel 12** and Tailwind CSS. It gives local Micro, Small, and Medium Enterprises (MSMEs) a place to showcase and sell their products online.

Originally developed for the 2023 APEC App Challenge. This repository has been professionally maintained and hardened with security and modernization fixes.

## Features

- **User accounts** — buyers register, log in, manage profile & address.
- **Vendor management** — MSMEs register as vendors, manage products and orders.
- **Product catalog** — create & manage listings (image, description, price).
- **Shopping cart** — add/remove items, then check out.
- **Order management** — buyers track orders; vendors manage fulfillment.
- **PayPal integration** — server-calculated checkout totals, persisted payment records, capture verification, and order settlement (requires PayPal sandbox/live credentials and end-to-end verification).
- **Search & filtering** — find products quickly.
- **Responsive UI** — Tailwind CSS across devices.

## Security hardening applied

| Area | Fix |
|------|-----|
| Auth middleware | `/user`, `/admin`, `/vendor` routes now require `auth` |
| Login throttling | `/login` limited to 5 attempts/minute |
| Logout | switched from `GET` to `POST` (CSRF-safe) |
| Mass assignment | removed `$guarded = []` from `User`/`Vendor`/`Admin`; explicit `$fillable` including `role` |
| Leaked secret | hardcoded Unsplash API key removed; now read from `UNSPLASH_ACCESS_KEY` |
| Dependency audit | `composer audit` is enforced by CI |

## Requirements

- PHP `^8.4.1`
- Composer 2
- MySQL 8 / MariaDB
- Node.js 18+ (for asset build)

## Installation

```bash
git clone https://github.com/casndrih/grogreen.git
cd grogreen
composer install
npm ci && npm run production

cp .env.example .env
# Configure DB, APP_URL, PAYPAL_MODE, PAYPAL_SANDBOX_CLIENT_ID, PAYPAL_SANDBOX_CLIENT_SECRET, and optional UNSPLASH_ACCESS_KEY in .env
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan serve
```

Visit `http://localhost:8000`. For PayPal sandbox checkout, create sandbox REST app credentials in the PayPal Developer Dashboard and set `PAYPAL_MODE=sandbox`. Use HTTPS and live credentials only after sandbox verification.

## CI & Maintenance

- GitHub Actions runs `composer audit`, Laravel Pint style check, and the test suite on every push/PR.
- Dependabot keeps Composer, npm, and GitHub Actions dependencies up to date weekly.

## Contributing

Pull requests and issues are welcome on the [GitHub repository](https://github.com/casndrih/grogreen).


### Frontend dependency security

The production dependency audit is enforced in CI and currently reports no production vulnerabilities. The legacy Laravel Mix development toolchain still has two high-severity advisories in transitive development-only packages for which npm reports no compatible fix. The build is pinned to webpack 5.105.0 for Laravel Mix 6 compatibility; migrate the build toolchain separately before exposing a webpack dev server to untrusted networks.


## Demo data (optional)

Demo records are opt-in and are not created in production by default. To populate a local or staging database with a sample administrator, customer, vendor and ten grocery products, set:

```env
GROWGREEN_DEMO_SEED=true
GROWGREEN_DEMO_PASSWORD=use-a-unique-password-at-least-12-characters
```

Then run `php artisan db:seed --force`. The accounts are created only if their reserved `@growgreen.test` email addresses do not already exist:

- Admin: `admin.demo@growgreen.test`
- Customer: `customer.demo@growgreen.test`
- Vendor: `vendor.demo@growgreen.test`

All three use the password supplied in `GROWGREEN_DEMO_PASSWORD`. Use a unique staging-only password, never a production administrator password. Disable `GROWGREEN_DEMO_SEED` and remove the demo password after testing. The seeder is idempotent and does not delete or overwrite existing records.
