<?php

namespace RyanChandler\BladeCacheDirective;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use RyanChandler\BladeCacheDirective\Commands\BladeCacheDirectiveCommand;

class BladeCacheDirectiveServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('blade-cache-directive')
            ->hasConfigFile()
            ->hasViews();
    }
}
