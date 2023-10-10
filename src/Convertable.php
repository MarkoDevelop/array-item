<?php

namespace Overthink\ArrayItem;

interface Convertable
{
    public function convert(mixed $value): mixed;
}
