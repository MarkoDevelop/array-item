<?php

namespace Overthink\ArrayItem;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ArrayItemServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('array-item')
            ->hasConfigFile();
    }
}
