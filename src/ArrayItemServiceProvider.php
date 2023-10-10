<?php

namespace Overthink\ArrayItem;

use Overthink\ArrayItem\Commands\ArrayItemCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

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
