# Array Item

[![Latest Version on Packagist](https://img.shields.io/packagist/v/overthink/array-item.svg?style=flat-square)](https://packagist.org/packages/overthink/array-item)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/MarkoDevelop/array-item/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/MarkoDevelop/array-item/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/MarkoDevelop/array-item/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/MarkoDevelop/array-item/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/overthink/array-item.svg?style=flat-square)](https://packagist.org/packages/overthink/array-item)

A fluent, typed accessor for reading and manipulating array data in Laravel apps. Wrap any array (API response, JSON column, config, ...) in an `ArrayItem` to get dot-notation access, typed getters (string, float, date, number, collection, JSON), and a small set of array manipulation helpers, without giving up `ArrayAccess`.

## Installation

You can install the package via composer:

```bash
composer require overthink/array-item
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="array-item-config"
```

## Usage

```php
use Overthink\ArrayItem\ArrayItem;

$item = ArrayItem::make([
    'name' => 'Widget',
    'price' => '1.234,56',
    'created_at' => '10.10.2020',
    'meta' => ['color' => 'red'],
]);

$item->get('name');                 // 'Widget'
$item->get('missing', 'default');   // 'default'
$item->getOr('missing', fn () => 'computed'); // 'computed'
$item->has('meta.color');           // true

$item->string('name');              // Illuminate\Support\Stringable
$item->float('price');              // 1234.56
$item->number('price')->currency(); // '€1,234.56'
$item->numberFormat('price');       // '1234,56'
$item->collect('meta');             // Illuminate\Support\Collection

$item->date('created_at');                  // Carbon\Carbon
$item->dateFormat('created_at', 'Y-m-d');    // '2020-10-10'
$item->timestamp('created_at');              // Carbon\Carbon (unix timestamp input)

$item->set('meta.size', 'M');
$item->merge(fn ($item) => ['extra' => true]);
$item->only(['name', 'price']);
$item->remove('meta');

$item['name'];                       // ArrayAccess is supported too
$item->toArray();
$item->toCollection();
$item->toJson();
```

### Available methods

| Method | Description |
| --- | --- |
| `make(array\|ArrayItem $attributes = [])` | Create a new instance (static). |
| `default(array $attributes): array` | Overridable hook to seed default attributes on construction. |
| `get(string\|callable $key, mixed $default = null)` | Dot-notation get, or resolve a callable against the item. |
| `getOr(string\|callable $key, mixed $default = null)` | Like `get()`, but falls back to `$default` when the value is `empty()`. |
| `has(string $key): bool` | Dot-notation existence check. |
| `set(string\|callable $key, mixed $value = null, bool $merge = false)` | Dot-notation set; a callable replaces (or merges into) all attributes. |
| `merge(string\|callable $key, mixed $value = null)` | Shorthand for `set(..., merge: true)`. |
| `only(string\|array\|Collection $keys)` | Keep only the given keys (supports `['from' => 'to']` remapping). |
| `remove(string\|array\|Collection $keys)` | Remove the given keys. |
| `string(string\|callable $key, string $default = '')` | Get the value as an `Illuminate\Support\Stringable`. |
| `float(string\|callable $key, ?string $default = null): float` | Parse the value as a float, handling `"1.234,56"`-style European numbers. |
| `number(string\|callable $key, ?string $default = null): Number` | Get the value wrapped in a `Number` helper (see below). |
| `numberFormat(string\|callable $key, ?int $decimals, ?string $decimalSeparator, ?string $thousandsSeparator, ?string $default): string` | `number_format()` over `float()`, using the static defaults below when omitted. |
| `collect(string\|callable $key, mixed $default = []): Collection` | Get the value as a `Collection`. |
| `json(string\|callable $key, mixed $default = null): mixed` | `json_decode()` the value. |
| `jsonItem(string\|callable $key, mixed $default = null): ArrayItem` | Decode the value and wrap it in a new `ArrayItem`. |
| `date(string\|callable $key, ?string $default = null): ?Carbon` | Parse the value as a `Carbon` date. |
| `dateFrom(string\|callable $key, string $from, ?string $default = null): ?Carbon` | Parse the value with an explicit input format. |
| `dateFormat(string\|callable $key, ?string $format = null, ?string $default = null): ?string` | Format a date value, defaulting to `static::$dateFormat`. |
| `timestamp(string\|callable $key, ?string $default = null): ?Carbon` | Parse the value as a unix timestamp. |
| `timestampFormat(string\|callable $key, ?string $format = null, ?string $default = null): ?string` | Format a timestamp value, defaulting to `static::$dateFormat`. |
| `convert(string\|callable $key, Convertable $converter, mixed $default = null)` | Pass the value through a custom `Convertable` implementation. |
| `getAttributes(): array` / `toArray(): array` | Get the raw underlying array. |
| `toCollection(): Collection` | Get the underlying array as a `Collection`. |
| `toJson($options = 0): string` / `__toString()` | JSON-encode the item. |

`ArrayItem` also implements `ArrayAccess` (`$item['key']`), `Illuminate\Contracts\Support\Arrayable`, `Jsonable`, `JsonSerializable`, and uses Laravel's `Conditionable` (`when()`/`unless()`) and `Macroable` traits.

Static configuration, shared across all instances:

```php
ArrayItem::$dateFormat = 'd.m.Y';
ArrayItem::$decimals = 2;
ArrayItem::$decimalSeparator = ',';
ArrayItem::$thousandsSeparator = '';
```

### `Number`

`ArrayItem::number()` returns an `Overthink\ArrayItem\Number` instance, a thin wrapper around `Illuminate\Support\Number`:

```php
$item->number('price')->format();       // '1,234.56'
$item->number('price')->currency();     // '€1,234.56' (defaults to EUR)
$item->number('price')->percentage();
$item->number('price')->abbreviate();
$item->number('price')->spell();        // requires ext-intl
$item->number('price')->ordinal();      // requires ext-intl
```

### `convert()` and `Convertable`

Custom conversion logic can be plugged in via the `Convertable` interface:

```php
use Overthink\ArrayItem\Convertable;

class UppercaseConverter implements Convertable
{
    public function convert(mixed $value): mixed
    {
        return mb_strtoupper($value);
    }
}

$item->convert('name', new UppercaseConverter()); // 'WIDGET'
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Marko Zagar](https://github.com/MarkoDevelop)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
