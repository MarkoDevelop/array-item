<?php

use Overthink\ArrayItem\Number;

it('can be created via make and cast to string', function () {
    $number = Number::make(1234.56);

    expect($number)->toBeInstanceOf(Number::class)
        ->and((string) $number)->toBe('1234.56');
});

it('can format a number', function () {
    expect(Number::make(1234.56)->format())->toBe('1,234.56');
});

it('can format a number as a percentage', function () {
    expect(Number::make(50)->percentage())->toBe('50%');
});

it('can format a file size', function () {
    expect(Number::make(1024)->fileSize(precision: 0))->toBe('1 KB');
});

it('can abbreviate a number', function () {
    expect(Number::make(1200)->abbreviate())->toBe('1K');
});

it('can format a number for humans', function () {
    expect(Number::make(1200)->forHumans())->toBe('1 thousand');
});

it('can clamp a number', function () {
    expect(Number::make(15)->clamp(0, 10))->toBe(10)
        ->and(Number::make(-5)->clamp(0, 10))->toBe(0)
        ->and(Number::make(5)->clamp(0, 10))->toBe(5);
});

it('can spell a number', function () {
    expect(extension_loaded('intl'))->toBeTrue('ext-intl is required for this test');

    expect(Number::make(42)->spell())->toBe('forty-two');
})->skip(! extension_loaded('intl'), 'ext-intl not installed');

it('can return the ordinal of a number', function () {
    expect(Number::make(3)->ordinal())->toBe('3rd');
})->skip(! extension_loaded('intl'), 'ext-intl not installed');

it('defaults currency formatting to EUR', function () {
    expect(Number::make(1234.56)->currency())->toBe('€1,234.56');
})->skip(! extension_loaded('intl'), 'ext-intl not installed');

it('can format currency in another currency', function () {
    expect(Number::make(1234.56)->currency('USD'))->toBe('$1,234.56');
})->skip(! extension_loaded('intl'), 'ext-intl not installed');

it('withLocale can be called statically', function () {
    $result = Number::withLocale('de', fn () => Number::make(1234.56)->format());

    expect($result)->toBeString();
})->skip(! extension_loaded('intl'), 'ext-intl not installed');

it('useLocale returns the instance for chaining', function () {
    $number = Number::make(1234.56);

    expect($number->useLocale('en'))->toBe($number);
});
