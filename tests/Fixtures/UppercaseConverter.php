<?php

namespace Overthink\ArrayItem\Tests\Fixtures;

use Overthink\ArrayItem\Convertable;

class UppercaseConverter implements Convertable
{
    public function convert(mixed $value): mixed
    {
        return mb_strtoupper((string) $value);
    }
}
