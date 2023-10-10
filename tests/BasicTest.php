<?php

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Overthink\ArrayItem\ArrayItem;

it('can return simple item', function () {
    $item = new ArrayItem(['foo' => 'bar']);

    expect($item->get('foo'))->toBe('bar');
});

it('can set attribute dynamically', function () {
    $item = ArrayItem::make(['foo' => 'bar'])
        ->set('foo', 'baz');

    expect($item->get('foo'))->toBe('baz');
});

it('can check if attribute exists', function () {
    $item = ArrayItem::make(['foo' => 'bar']);

    expect($item->has('foo'))->toBeTrue()
        ->and($item->has('bar'))->toBeFalse();
});

it('can return string class', function () {
    $item = ArrayItem::make(['foo' => 'bar']);

    expect($item->string('foo'))->toBeInstanceOf(Stringable::class);
});

it('can return carbon class', function () {
    $item = ArrayItem::make(['foo' => now()->format('d.m.Y')]);

    expect($item->date('foo'))->toBeInstanceOf(Carbon::class);
});

it('can return formatted date', function () {
    $item = ArrayItem::make(['foo' => '10.10.2020']);

    expect($item->dateFormat('foo', 'Y-m-d'))->toBe('2020-10-10');
});

it('can return formatted number', function () {
    $item = ArrayItem::make(['foo' => '1.000,23']);

    expect($item->number('foo'))->toBe('1000,23');
});

it('can return float from string number', function () {
    $item = ArrayItem::make(['foo' => '1.000,23']);

    expect($item->float('foo'))->toBe(1000.23);
});

it('can return collection from array', function () {
    $item = ArrayItem::make(['foo' => [1, 2, 3]]);

    expect($item->collect('foo'))->toBeInstanceOf(Collection::class)
        ->and($item->collect('foo')->toArray())->toBe([1, 2, 3]);
});

it('can return remove attribute from array', function () {
    $item = ArrayItem::make(['foo' => 'bar', 'baz' => 'qux']);

    expect($item->remove('foo')->toArray())->toBe(['baz' => 'qux']);
});



