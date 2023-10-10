<?php

namespace Overthink\ArrayItem\Commands;

use Illuminate\Console\Command;

class ArrayItemCommand extends Command
{
    public $signature = 'array-item';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
