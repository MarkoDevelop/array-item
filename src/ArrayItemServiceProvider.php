<?php

namespace Overthink\ArrayItem;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Overthink\ArrayItem\Commands\ArrayItemCommand;

class ArrayItemServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('array-item')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_array-item_table')
            ->hasCommand(ArrayItemCommand::class);
    }
}
