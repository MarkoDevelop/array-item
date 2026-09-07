<?php

use Carbon\Carbon;
use Overthink\ArrayItem\ArrayItem;

it('can return carbon class from a formatted date string', function () {
    $item = ArrayItem::make(['foo' => now()->format('d.m.Y')]);

    expect($item->date('foo'))->toBeInstanceOf(Carbon::class);
});

it('date parses an iso string', function () {
    $item = ArrayItem::make(['foo' => '2020-10-10']);

    expect($item->date('foo'))->toBeInstanceOf(Carbon::class)
        ->and($item->date('foo')->format('Y-m-d'))->toBe('2020-10-10');
});

it('date passes through an existing Carbon instance', function () {
    $carbon = Carbon::parse('2020-10-10');
    $item = ArrayItem::make(['foo' => $carbon]);

    expect($item->date('foo'))->toBe($carbon);
});

it('date returns null when the value is null', function () {
    $item = ArrayItem::make([]);

    expect($item->date('missing'))->toBeNull();
});

it('dateFrom parses using an explicit input format', function () {
    $item = ArrayItem::make(['foo' => '10-10-2020']);

    expect($item->dateFrom('foo', 'd-m-Y')->format('Y-m-d'))->toBe('2020-10-10');
});

it('can return formatted date with an explicit format', function () {
    $item = ArrayItem::make(['foo' => '10.10.2020']);

    expect($item->dateFormat('foo', 'Y-m-d'))->toBe('2020-10-10');
});

it('dateFormat falls back to the static dateFormat', function () {
    $item = ArrayItem::make(['foo' => '2020-10-10']);

    expect($item->dateFormat('foo'))->toBe('10.10.2020');
});

it('dateFormat returns null when the value is null', function () {
    $item = ArrayItem::make([]);

    expect($item->dateFormat('missing'))->toBeNull();
});

it('timestamp parses a unix timestamp', function () {
    $item = ArrayItem::make(['foo' => 0]);

    expect($item->timestamp('foo'))->toBeInstanceOf(Carbon::class)
        ->and($item->timestamp('foo')->format('Y-m-d'))->toBe('1970-01-01');
});

it('timestamp returns null when the value is null', function () {
    $item = ArrayItem::make([]);

    expect($item->timestamp('missing'))->toBeNull();
});

it('timestampFormat formats with an explicit format', function () {
    $item = ArrayItem::make(['foo' => 0]);

    expect($item->timestampFormat('foo', 'Y-m-d'))->toBe('1970-01-01');
});

it('timestampFormat falls back to the static dateFormat', function () {
    $item = ArrayItem::make(['foo' => 0]);

    expect($item->timestampFormat('foo'))->toBe('01.01.1970');
});

it('timestampFormat returns null when the value is null', function () {
    $item = ArrayItem::make([]);

    expect($item->timestampFormat('missing'))->toBeNull();
});

it('timestamp renders in the configured default timezone, not UTC', function () {
    $original = date_default_timezone_get();
    date_default_timezone_set('Europe/Ljubljana');

    try {
        $item = ArrayItem::make(['foo' => 0]);

        expect($item->timestamp('foo')->format('Y-m-d H:i P'))->toBe('1970-01-01 01:00 +01:00');
    } finally {
        date_default_timezone_set($original);
    }
});
