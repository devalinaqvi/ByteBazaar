# Upgrade verification

Verified locally on PHP 8.3 and MySQL 8.0.46.

- PHPUnit on SQLite: **24 tests, 102 assertions passed**.
- The same PHPUnit suite on isolated MySQL: **24 tests, 102 assertions passed**.
- Independent HTTP cookie jars: **68 checks passed**, including customer isolation, CSRF, session-ID rotation, guest/account cart merging, logout, persisted carts, order authorization, retries, escaped customer data, admin routes, status transitions, and disguised uploads.
- MySQL parallel-process checks: exactly one buyer obtains the last stock unit; concurrent submissions of the same checkout key produce one order and one stock decrement.
- The original schema-only fixture upgrades successfully; running the migration again succeeds.
- PHP syntax, JavaScript syntax, Composer validation, and whitespace checks pass.
- Browser checks: desktop homepage, mobile catalog, category filtering, sorting, cart, guest checkout and receipt, login, administrator navigation, and mobile overflow. No broken catalog images or browser console errors were observed in the checked pages.
- Two sites using different databases on localhost were open in the same browser. Logging into the demo administrator account did not log into the real local store.

The configured local MySQL database was backed up before applying the migration. All original table row counts were unchanged afterward. The backup is private and outside the repository at `/tmp/byte-bazaar-before-upgrade-20260924-214230.sql`; move it to durable private storage if long-term retention is needed. Existing local configuration edits were preserved.

Synthetic HTTP accounts, orders, and upload tests used a separate SQLite preview database. No synthetic accounts or orders were inserted into the configured local MySQL database.

CI is configured for PHP 8.2 and 8.3; the hosted workflow has not been run from this workspace. The manual MySQL and HTTP commands are documented in the README.
