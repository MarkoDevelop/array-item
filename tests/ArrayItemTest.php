<?php

use Illuminate\Support\Collection;
use Overthink\ArrayItem\ArrayItem;

it('can return simple item', function () {
    $item = new ArrayItem(['foo' => 'bar']);

    expect($item->get('foo'))->toBe('bar');
});

it('can be created via make', function () {
    $item = ArrayItem::make(['foo' => 'bar']);

    expect($item)->toBeInstanceOf(ArrayItem::class)
        ->and($item->get('foo'))->toBe('bar');
});

it('can be constructed from another ArrayItem', function () {
    $item = new ArrayItem(['foo' => 'bar']);
    $copy = ArrayItem::make($item);

    expect($copy)->not->toBe($item)
        ->and($copy->toArray())->toBe(['foo' => 'bar']);
});

it('runs the default() hook when constructed', function () {
    $item = new class(['foo' => 'bar']) extends ArrayItem
    {
        public function default(array $attributes): array
        {
            return array_merge(['baz' => 'qux'], $attributes);
        }
    };

    expect($item->toArray())->toBe(['baz' => 'qux', 'foo' => 'bar']);
});

it('can get with dot notation and default', function () {
    $item = ArrayItem::make(['foo' => ['bar' => 'baz']]);

    expect($item->get('foo.bar'))->toBe('baz')
        ->and($item->get('foo.missing', 'fallback'))->toBe('fallback');
});

it('can get by resolving a callable', function () {
    $item = ArrayItem::make(['foo' => 'bar']);

    expect($item->get(fn (ArrayItem $item) => $item->get('foo')))->toBe('bar');
});

it('getOr falls back to default when value is empty', function () {
    $item = ArrayItem::make(['zero' => 0, 'empty_string' => '', 'empty_array' => [], 'false' => false, 'set' => 'value']);

    expect($item->getOr('zero', 'default'))->toBe('default')
        ->and($item->getOr('empty_string', 'default'))->toBe('default')
        ->and($item->getOr('empty_array', 'default'))->toBe('default')
        ->and($item->getOr('false', 'default'))->toBe('default')
        ->and($item->getOr('set', 'default'))->toBe('value')
        ->and($item->getOr('missing', fn ($item, $key) => "computed:{$key}"))->toBe('computed:missing');
});

it('can set attribute dynamically', function () {
    $item = ArrayItem::make(['foo' => 'bar'])
        ->set('foo', 'baz');

    expect($item->get('foo'))->toBe('baz');
});

it('can set with dot notation', function () {
    $item = ArrayItem::make([])->set('foo.bar', 'baz');

    expect($item->get('foo.bar'))->toBe('baz');
});

it('resolves closures passed as the value to set', function () {
    $item = ArrayItem::make([])->set('foo', fn (ArrayItem $item) => 'resolved');

    expect($item->get('foo'))->toBe('resolved');
});

it('can replace all attributes via a callable key', function () {
    $item = ArrayItem::make(['foo' => 'bar'])
        ->set(fn (ArrayItem $item) => ['baz' => 'qux']);

    expect($item->toArray())->toBe(['baz' => 'qux']);
});

it('can merge attributes via a callable key using merge argument', function () {
    $item = ArrayItem::make(['foo' => 'bar'])
        ->set(fn (ArrayItem $item) => ['baz' => 'qux'], merge: true);

    expect($item->toArray())->toBe(['foo' => 'bar', 'baz' => 'qux']);
});

it('merge() is a shorthand for set with merge true', function () {
    $item = ArrayItem::make(['foo' => 'bar'])
        ->merge(fn (ArrayItem $item) => ['baz' => 'qux']);

    expect($item->toArray())->toBe(['foo' => 'bar', 'baz' => 'qux']);
});

it('can check if attribute exists', function () {
    $item = ArrayItem::make(['foo' => 'bar']);

    expect($item->has('foo'))->toBeTrue()
        ->and($item->has('bar'))->toBeFalse();
});

it('only keeps the given keys', function () {
    $item = ArrayItem::make(['foo' => 'bar', 'baz' => 'qux', 'other' => 1]);

    expect($item->only('foo')->toArray())->toBe(['foo' => 'bar']);

    $item = ArrayItem::make(['foo' => 'bar', 'baz' => 'qux', 'other' => 1]);

    expect($item->only(['foo', 'baz'])->toArray())->toBe(['foo' => 'bar', 'baz' => 'qux']);

    $item = ArrayItem::make(['foo' => 'bar', 'baz' => 'qux']);

    expect($item->only(collect(['foo']))->toArray())->toBe(['foo' => 'bar']);
});

it('only can remap keys', function () {
    $item = ArrayItem::make(['foo' => 'bar', 'baz' => 'qux']);

    expect($item->only(['foo' => 'renamed'])->toArray())->toBe(['renamed' => 'bar']);
});

it('can return remove attribute from array', function () {
    $item = ArrayItem::make(['foo' => 'bar', 'baz' => 'qux']);

    expect($item->remove('foo')->toArray())->toBe(['baz' => 'qux']);
});

it('remove accepts an array or a collection', function () {
    $item = ArrayItem::make(['foo' => 'bar', 'baz' => 'qux', 'other' => 1]);

    expect($item->remove(['foo', 'baz'])->toArray())->toBe(['other' => 1]);

    $item = ArrayItem::make(['foo' => 'bar', 'baz' => 'qux']);

    expect($item->remove(collect(['foo']))->toArray())->toBe(['baz' => 'qux']);
});

it('can convert to array, collection, json and string', function () {
    $item = ArrayItem::make(['foo' => 'bar']);

    expect($item->toArray())->toBe(['foo' => 'bar'])
        ->and($item->toCollection())->toBeInstanceOf(Collection::class)
        ->and($item->toCollection()->toArray())->toBe(['foo' => 'bar'])
        ->and($item->jsonSerialize())->toBe(['foo' => 'bar'])
        ->and($item->toJson())->toBe('{"foo":"bar"}')
        ->and((string) $item)->toBe('{"foo":"bar"}');
});

it('supports ArrayAccess', function () {
    $item = ArrayItem::make(['foo' => 'bar']);

    expect($item['foo'])->toBe('bar')
        ->and(isset($item['foo']))->toBeTrue()
        ->and(isset($item['missing']))->toBeFalse();

    $item['foo'] = 'baz';
    expect($item['foo'])->toBe('baz');

    unset($item['foo']);
    expect(isset($item['foo']))->toBeFalse();
});

it('supports Conditionable', function () {
    $item = ArrayItem::make(['foo' => 'bar'])
        ->when(true, fn (ArrayItem $item) => $item->set('conditional', 'yes'))
        ->unless(true, fn (ArrayItem $item) => $item->set('unless', 'no'));

    expect($item->has('conditional'))->toBeTrue()
        ->and($item->has('unless'))->toBeFalse();
});

it('supports Macroable', function () {
    ArrayItem::macro('shout', function (string $key) {
        /** @var ArrayItem $this */
        return mb_strtoupper($this->get($key));
    });

    $item = ArrayItem::make(['foo' => 'bar']);

    expect($item->shout('foo'))->toBe('BAR');
});
