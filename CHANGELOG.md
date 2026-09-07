# Changelog

All notable changes to `array-item` will be documented in this file.

## v2.0.0

### Breaking

- Dropped Laravel 10 and PHP 8.2 support. PHP floor is now `^8.3`; Laravel support is `^11.0 || ^12.0 || ^13.0`. Laravel 10 has been EOL since February 2025 — if you still need it, stay on `^1.0`.
- `nesbot/carbon` is now an explicit dependency, pinned to `^3.8.4` (it was previously pulled in transitively and could resolve to Carbon 2).

### Fixed

- `ArrayItem::timestamp()` (and therefore `timestampFormat()`) now renders in the application's configured default timezone instead of silently switching to UTC. This was a behavioral divergence introduced by Carbon 3 (`Carbon::createFromTimestamp()` defaults to UTC there, but rendered in the default timezone on Carbon 2) — the fix pins the timezone explicitly so output is identical on both Carbon major versions.

### Internal

- `pestphp/pest-plugin-laravel` and `pestphp/pest-plugin-arch` are no longer explicit dev dependencies — the test suite uses no Laravel-HTTP-testing features, and `pest-plugin-arch` is a hard transitive dependency of Pest itself.
