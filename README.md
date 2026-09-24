# Byte Bazaar

A PHP 8.2+ storefront with a responsive shopping experience, guest and account carts, cash-on-delivery checkout, and an administration area.

## Setup

Requirements: PHP 8.2+, Composer, MySQL 8.0+, and the PDO MySQL, mbstring, fileinfo, and standard PHP extensions. Tests also use PDO SQLite. The UI uses local CSS and vanilla JavaScript; no frontend build or external CDN is required.

```sh
composer install
cp config/config.example.php config/config.php
```

Configure `db.host`, `db.port`, `db.dbname`, `db.user`, and `db.pass` in your local configuration, or set `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, and `DB_PASSWORD`. Environment values override the local file. Keep credentials out of version control. The existing local config is preserved during upgrades; `.gitignore` does not untrack a file already committed in an older revision.

Create an empty MySQL database with UTF-8 support, then run:

```sh
composer migrate
composer serve
```

Open [the local store](http://localhost:8000). Set the production web root to `public/`.

`migrations/schema.sql` is the canonical clean-install schema. `composer migrate` also adds the required columns and indexes to the original application schema. It preserves existing products, accounts, carts, and orders, and changes the order/account foreign key to preserve order history when an account is deleted. Older orders retain their recorded total; the UI does not invent a historical tax breakdown. Back up a database before applying a schema change. MySQL DDL is not transactionally reversible.

The old, conflicting SQL dumps have been removed, including their embedded account and customer records. Do not use those files from an older revision to initialize a new environment.

### Optional demo data

```sh
composer seed
```

Seeding creates a small synthetic catalog and preserves existing records. It does not create an administrator with a default password. To create an initial administrator, supply `SEED_ADMIN_EMAIL` and `SEED_ADMIN_PASSWORD` through your environment and run the seed command. Passwords must contain 12–72 characters. Existing accounts are never promoted or have their passwords reset by seeding.

For an isolated SQLite preview, explicitly set a separate database file for both commands:

```sh
APP_DB_SQLITE=/tmp/byte-bazaar-preview.sqlite composer seed
APP_DB_SQLITE=/tmp/byte-bazaar-preview.sqlite php -S localhost:8088 -t public/
```

SQLite is useful for previews and tests. MySQL is the deployment target and is covered by the integration and concurrency checks.

## Sessions and dependency injection

`public/index.php` creates a new container on **every request**. Container singletons are shared only within that request. `SessionContext` holds a reference to that request's PHP session; there are no static identities, global container singletons, or cached PDO connections. Controllers are transient, and the view renderer receives its dependencies from the same request container.

- Guest carts use a cryptographically random server-side ownership key.
- Account carts use `user:<database user ID>` and persist across login sessions.
- Login merges the current guest cart into the account cart, caps quantities to available stock, rotates the PHP session ID and CSRF token, and clears transient session data.
- Logout clears authentication and starts a fresh guest session. It never turns the account cart into the next visitor's guest cart.
- Cookie names include the installation, environment, and database identity. Different localhost ports alone do **not** isolate browser cookies; the database-specific namespace prevents a preview login from being reused against the real local database.
- Cookies are HttpOnly and SameSite=Lax, with Secure enabled on HTTPS. PHP strict session mode rejects unknown session IDs. Responses containing session state use `private, no-store`.
- Roles are read from the database, not trusted from an `is_admin` session flag. Cart changes and order reads check the current owner.

Use the normal PHP request lifecycle (PHP-FPM, Apache PHP, or the built-in development server). A persistent application worker must build a fresh container and session context for every request. If multiple hosts serve the same installation, configure a shared PHP session store and a stable shared installation identifier before scaling out.

Changing to the new cookie namespace requires users of the old version to sign in again. The original application never reliably attached guest carts to accounts, so their old ownership cannot be inferred safely.

## Checkout and security

Checkout validates customer information and positive quantities on the server. It reads current prices and availability inside a transaction, conditionally decrements stock, saves the order and its items, and clears the cart before committing. Account-level locking coordinates carts across browser sessions. A unique checkout key makes retries return the existing order. Competing orders cannot consume the same final stock unit.

Prices are converted to integer cents before multiplication and tax rounding. Shipping is currently $5 per order; the configured business rule is a flat 8% tax. These are demo business rules, not a jurisdiction-aware tax engine. Only cash on delivery is supported. Cancellation, refunds, online payments, password recovery, and email delivery are not implemented or advertised as available.

POST routes require a session CSRF token. Product and customer fields are escaped in HTML, and the UI builds notifications with text nodes. Login attempts are limited using database counters across sessions. Uploaded images must pass file-content and dimension checks; stored extensions are derived from verified MIME types. Apache blocks executable extensions under uploads. Nginx must route PHP execution only to the front controller:

```nginx
root /path/to/byte-bazaar/public;
index index.php;
location / { try_files $uri $uri/ /index.php?$query_string; }
location = /index.php {
    include fastcgi_params;
    fastcgi_pass unix:/run/php/php8.2-fpm.sock;
    fastcgi_param SCRIPT_FILENAME $document_root/index.php;
}
location ~ \.php(?:/|$) { return 404; }
location ~ /\. { deny all; }
```

Use HTTPS and pass the real HTTPS state to PHP through your trusted proxy configuration. The app does not trust arbitrary forwarded-protocol headers. Keep the upload directory writable by PHP; a read-only upload directory does not prevent the rest of the store from starting. Back up uploads with the database. Run deployment installs with `composer install --no-dev --optimize-autoloader`.

## Verification

```sh
composer validate --strict --no-check-publish
composer test
```

The suite uses a freshly migrated in-memory SQLite database for each test. It covers independent sessions, DI scope, cart ownership, login merges and logout, roles, CSRF, order authorization, quantities, stock rollback, idempotency, pricing, uploads, migrations, and route targets.

For MySQL, create a **disposable** `byte_bazaar_test` database. The suite drops and recreates its application tables. Never point it at a database you want to keep:

```sh
TEST_MYSQL_DSN='mysql:unix_socket=/path/to/test.sock;dbname=byte_bazaar_test' vendor/bin/phpunit
```

For true concurrent requests, create a separate disposable `byte_bazaar_test_concurrency` database, then run the check below with PHP's pcntl extension. It races two buyers for the final unit, races duplicate checkout submissions, and checks the migration against a schema-only fixture of the original application.

```sh
TEST_MYSQL_DSN='mysql:unix_socket=/path/to/test.sock;dbname=byte_bazaar_test_concurrency' php tests/mysql_concurrency.php
```

`tests/http_smoke.py` exercises two independent cookie jars and a demo administrator against a disposable localhost preview. It creates synthetic accounts and orders and tests uploads. Seed that preview with the synthetic administrator `admin@example.test` / `local-demo-admin-only`, then run:

```sh
BYTE_BAZAAR_ALLOW_TEST_WRITES=1 python3 tests/http_smoke.py http://127.0.0.1:8088
```

These test-only credentials are never installed in the real local database. Browser verification should include the catalog filters, product detail, quantity changes, checkout, login/logout, administration, and narrow mobile layouts.
