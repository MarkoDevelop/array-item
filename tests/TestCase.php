<?php

namespace Overthink\ArrayItem\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Overthink\ArrayItem\ArrayItemServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            ArrayItemServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
    }
}
