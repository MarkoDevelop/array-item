<?php

use Illuminate\Support\Collection;
use Illuminate\Support\Stringable;
use Overthink\ArrayItem\ArrayItem;
use Overthink\ArrayItem\Number;
use Overthink\ArrayItem\Tests\Fixtures\UppercaseConverter;

it('can return string class', function () {
    $item = ArrayItem::make(['foo' => 'bar']);

    expect($item->string('foo'))->toBeInstanceOf(Stringable::class)
        ->and((string) $item->string('foo'))->toBe('bar');
});

it('can return float from a european-formatted string number', function () {
    $item = ArrayItem::make(['foo' => '1.000,23']);

    expect($item->float('foo'))->toBe(1000.23);
});

it('can return float from a plain decimal string', function () {
    $item = ArrayItem::make(['foo' => '1.5']);

    expect($item->float('foo'))->toBe(1.5);
});

it('can return float from a numeric value', function () {
    $item = ArrayItem::make(['foo' => 1.5, 'bar' => '5']);

    expect($item->float('foo'))->toBe(1.5)
        ->and($item->float('bar'))->toBe(5.0);
});

it('returns 0.0 for a missing float value', function () {
    $item = ArrayItem::make([]);

    expect($item->float('missing'))->toBe(0.0);
});

it('treats a dotted-thousands-only string as a plain decimal (documented tradeoff)', function () {
    $item = ArrayItem::make(['foo' => '1.000']);

    expect($item->float('foo'))->toBe(1.0);
});

it('numberFormat uses the static defaults', function () {
    $item = ArrayItem::make(['foo' => '1.000,23']);

    expect($item->numberFormat('foo'))->toBe('1000,23');
});

it('numberFormat accepts explicit decimals, decimal separator and thousands separator', function () {
    $item = ArrayItem::make(['foo' => '1.000,23']);

    expect($item->numberFormat('foo', 1, '.', ','))->toBe('1,000.2');
});

it('numberFormat falls back to overridden static configuration', function () {
    $originalDecimals = ArrayItem::$decimals;
    $originalSeparator = ArrayItem::$decimalSeparator;
    $originalThousands = ArrayItem::$thousandsSeparator;

    ArrayItem::$decimals = 1;
    ArrayItem::$decimalSeparator = '.';
    ArrayItem::$thousandsSeparator = ',';

    $item = ArrayItem::make(['foo' => '1.000,23']);

    try {
        expect($item->numberFormat('foo'))->toBe('1,000.2');
    } finally {
        ArrayItem::$decimals = $originalDecimals;
        ArrayItem::$decimalSeparator = $originalSeparator;
        ArrayItem::$thousandsSeparator = $originalThousands;
    }
});

it('number() returns a Number instance built from the parsed float', function () {
    $item = ArrayItem::make(['foo' => '1.000,23']);

    $number = $item->number('foo');

    expect($number)->toBeInstanceOf(Number::class)
        ->and($number->format())->toBe('1,000.23');
});

it('can return a collection from an array', function () {
    $item = ArrayItem::make(['foo' => [1, 2, 3]]);

    expect($item->collect('foo'))->toBeInstanceOf(Collection::class)
        ->and($item->collect('foo')->toArray())->toBe([1, 2, 3]);
});

it('collect wraps a scalar and defaults for a missing key', function () {
    $item = ArrayItem::make(['foo' => 'bar']);

    expect($item->collect('foo')->toArray())->toBe(['bar'])
        ->and($item->collect('missing')->toArray())->toBe([]);
});

it('can decode a json value', function () {
    $item = ArrayItem::make(['foo' => '{"bar":"baz"}']);

    expect($item->json('foo'))->toBe(['bar' => 'baz']);
});

it('can decode a json value into an ArrayItem', function () {
    $item = ArrayItem::make(['foo' => '{"bar":"baz"}']);

    $jsonItem = $item->jsonItem('foo');

    expect($jsonItem)->toBeInstanceOf(ArrayItem::class)
        ->and($jsonItem->get('bar'))->toBe('baz');
});

it('json returns null for invalid json', function () {
    $item = ArrayItem::make(['foo' => 'not json']);

    expect($item->json('foo'))->toBeNull();
});

it('can convert a value using a Convertable', function () {
    $item = ArrayItem::make(['foo' => 'bar']);

    expect($item->convert('foo', new UppercaseConverter))->toBe('BAR');
});
