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

    public function __construct(array $attributes = [])
    {
        $this->attributes = $this->default($attributes);
    }

    public static function make(array $attributes = []): static
    {
        return new static($attributes);
    }

    public function default(array $attributes): array
    {
        return $attributes;
    }

    public function set(string $key, mixed $value = null): static
    {
        $setValue = value($value, $this);

        Arr::set($this->attributes, $key, $setValue);

        return $this;
    }

    public function get(string|callable $key, mixed $default = null)
    {
        if (is_callable($key) && ! is_string($key)) {
            return $key($this);
        }

        return Arr::get($this->getAttributes(), $key, $default);
    }

    public function has(string $key): bool
    {
        return Arr::has($this->getAttributes(), $key);
    }

    public function string(string|callable $key, string $default = ''): Stringable
    {
        return Str::of($this->get($key, $default));
    }

    public function date(string|callable $key, string $default = null): Carbon
    {
        return Carbon::parse($this->get($key, $default));
    }

    public function dateFormat(string|callable $key, string $format = null, string $default = null): string
    {
        return $this->date($key, $default)->format($format ?? static::$dateFormat);
    }

    public function number(
        string|callable $key,
        int $decimals = null,
        string $decimalSeparator = null,
        string $default = null
    ): string {
        return number_format(
            $this->float($key, $default),
            $decimals ?? static::$decimals,
            $decimalSeparator ?? static::$decimalSeparator,
            static::$thousandsSeparator
        );
    }

    public function float(string|callable $key, string $default = null): float
    {
        $value = $this->get($key, $default);

        return is_string($value)
            ? Str::of($value)->replace('.', '')->replace(',', '.')->toFloat()
            : floatval($value);
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

    public function toArray(): array
    {
        return $this->getAttributes();
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
