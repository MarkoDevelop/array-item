<?php

namespace Overthink\ArrayItem;

use ArrayAccess;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use JsonSerializable;

class ArrayItem implements Arrayable, ArrayAccess, Jsonable, JsonSerializable
{
    use Conditionable;
    use Macroable;

    protected array $attributes;

    public static string $dateFormat = 'd.m.Y';

    public static int $decimals = 2;

    public static string $decimalSeparator = ',';

    public static string $thousandsSeparator = '';

    public function __construct(array|ArrayItem $attributes = [])
    {
        if ($attributes instanceof ArrayItem) {
            $attributes = $attributes->toArray();
        }

        $this->attributes = $this->default($attributes);
    }

    public static function make(array|ArrayItem $attributes = []): static
    {
        return new static($attributes);
    }

    public function default(array $attributes): array
    {
        return $attributes;
    }

    public function set(string|callable $key, mixed $value = null, bool $merge = false): static
    {
        if (is_callable($key) && ! is_string($key)) {

            if ($merge) {
                $this->attributes = array_merge($this->attributes, $key($this));
            } else {
                $this->attributes = $key($this);
            }

            return $this;
        }

        $setValue = value($value, $this);

        Arr::set($this->attributes, $key, $setValue);

        return $this;
    }

    public function merge(string|callable $key, mixed $value = null): static
    {
        return $this->set($key, $value, true);
    }

    public function get(string|callable $key, mixed $default = null): mixed
    {
        if (is_callable($key) && ! is_string($key)) {
            return $key($this);
        }

        return Arr::get($this->getAttributes(), $key, $default);
    }

    public function getOr(string|callable $key, mixed $default = null): mixed
    {
        $value = $this->get($key);

        if (empty($value)) {
            return value($default, $this, $key);
        }

        return $value;
    }

    public function has(string $key): bool
    {
        return Arr::has($this->getAttributes(), $key);
    }

    public function string(string|callable $key, string $default = ''): Stringable
    {
        return Str::of($this->get($key, $default));
    }

    public function timestamp(string|callable $key, ?string $default = null): ?Carbon
    {
        $value = $this->get($key, $default);

        if (is_null($value)) {
            return null;
        }

        return Carbon::createFromTimestamp($value);
    }

    public function date(string|callable $key, ?string $default = null): ?Carbon
    {
        $value = $this->get($key, $default);

        if (is_null($value)) {
            return null;
        }

        return $value instanceof Carbon ? $value : Carbon::parse($value);
    }

    public function dateFrom(string|callable $key, string $from, ?string $default = null): ?Carbon
    {
        $value = $this->get($key, $default);

        if (is_null($value)) {
            return null;
        }

        return Carbon::createFromFormat($from, $value);
    }

    public function timestampFormat(string|callable $key, ?string $format = null, ?string $default = null): ?string
    {
        $value = $this->timestamp($key, $default);

        if (is_null($value)) {
            return null;
        }

        return $value->format($format ?? static::$dateFormat);
    }

    public function json(string|callable $key, mixed $default = null): mixed
    {
        return json_decode($this->get($key, $default), true);
    }

    public function jsonItem(string|callable $key, mixed $default = null): ArrayItem
    {
        return ArrayItem::make($this->json($key, $default));
    }

    public function dateFormat(string|callable $key, ?string $format = null, ?string $default = null): ?string
    {
        $value = $this->date($key, $default);

        if (is_null($value)) {
            return null;
        }

        return $value->format($format ?? static::$dateFormat);
    }

    public function numberFormat(
        string|callable $key,
        ?int $decimals = null,
        ?string $decimalSeparator = null,
        ?string $thousandsSeparator = null,
        ?string $default = null
    ): string {
        return number_format(
            $this->float($key, $default),
            $decimals ?? static::$decimals,
            $decimalSeparator ?? static::$decimalSeparator,
            $thousandsSeparator ?? static::$thousandsSeparator
        );
    }

    public function number(string|callable $key, ?string $default = null): Number
    {
        return Number::make($this->float($key, $default));
    }

    public function float(string|callable $key, ?string $default = null): float
    {
        $value = $this->get($key, $default);

        return floatval($value) == $value
            ? floatval($value)
            : Str::of($value)->replace('.', '')->replace(',', '.')->toFloat();
    }

    public function collect(string|callable $key, mixed $default = []): Collection
    {
        return Collection::wrap($this->get($key, $default));
    }

    public function convert(string|callable $key, Convertable $converter, mixed $default = null): mixed
    {
        return $converter->convert($this->get($key, $default));
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function remove(string|array|Collection $item): static
    {
        if ($item instanceof Collection) {
            $item = $item->toArray();
        }

        $this->attributes = Arr::except($this->attributes, $item);

        return $this;
    }

    public function only(string|array|Collection $item): static
    {
        if ($item instanceof Collection) {
            $item = $item->toArray();
        } elseif (is_string($item)) {
            $item = Arr::wrap($item);
        }

        $attributes = [];
        foreach ($item as $index => $key) {
            $value = is_numeric($index) ? $key : $index;
            if (Arr::has($this->attributes, $value)) {
                $attributes[$key] = Arr::get($this->attributes, $value);
            }
        }

        $this->attributes = $attributes;

        return $this;
    }

    public function toArray(): array
    {
        return $this->getAttributes();
    }

    public function toCollection(): Collection
    {
        return collect($this->getAttributes());
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    public function __toString()
    {
        return $this->toJson();
    }

    // ArrayAccess
    public function offsetSet($offset, $value): void
    {
        $this->set($offset, $value);
    }

    public function offsetExists($offset): bool
    {
        return $this->has($offset);
    }

    public function offsetUnset($offset): void
    {
        $this->remove($offset);
    }

    public function offsetGet($offset): mixed
    {
        return $this->get($offset);
    }
}
