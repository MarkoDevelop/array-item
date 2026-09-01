<?php

namespace Overthink\ArrayItem;

use Illuminate\Support\Number as BaseNumber;
use Stringable;

class Number implements Stringable
{
    public function __construct(
        public int|float $number
    ) {}

    public static function make(int|float $number): static
    {
        return new static($number);
    }

    public function format(?int $precision = null, ?int $maxPrecision = null, ?string $locale = null): string|false
    {
        return BaseNumber::format($this->number, $precision, $maxPrecision, $locale);
    }

    public function spell(?string $locale = null, ?int $after = null, ?int $until = null): string
    {
        return BaseNumber::spell($this->number, $locale, $after, $until);
    }

    public function ordinal(?string $locale = null): string
    {
        return BaseNumber::ordinal($this->number, $locale);
    }

    public function percentage(int $precision = 0, ?int $maxPrecision = null, ?string $locale = null): string
    {
        return BaseNumber::percentage($this->number, $precision, $maxPrecision, $locale);
    }

    public function currency(string $in = 'EUR', ?string $locale = null): string
    {
        return BaseNumber::currency($this->number, $in, $locale);
    }

    public function fileSize(int $precision = 0, ?int $maxPrecision = null): string
    {
        return BaseNumber::fileSize($this->number, $precision, $maxPrecision);
    }

    public function abbreviate(int $precision = 0, ?int $maxPrecision = null): string
    {
        return BaseNumber::abbreviate($this->number, $precision, $maxPrecision);
    }

    public function forHumans(int $precision = 0, ?int $maxPrecision = null, bool $abbreviate = false): string
    {
        return BaseNumber::forHumans($this->number, $precision, $maxPrecision, $abbreviate);
    }

    public function clamp(int|float $min, int|float $max): float|int
    {
        return BaseNumber::clamp($this->number, $min, $max);
    }

    public static function withLocale(string $locale, callable $callback): string
    {
        return BaseNumber::withLocale($locale, $callback);
    }

    public function useLocale(string $locale): static
    {
        BaseNumber::useLocale($locale);

        return $this;
    }

    public function __toString(): string
    {
        return (string) $this->number;
    }
}
