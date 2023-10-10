<?php

namespace Overthink\ArrayItem\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Overthink\ArrayItem\ArrayItem
 */
class ArrayItem extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Overthink\ArrayItem\ArrayItem::class;
    }
}
