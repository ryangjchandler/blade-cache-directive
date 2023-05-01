<?php

namespace RyanChandler\BladeCacheDirective;

use Illuminate\Support\Facades\Blade;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class BladeCacheDirectiveServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('blade-cache-directive')
            ->hasConfigFile();
    }

    public function packageBooted()
    {
        Blade::directive('cache', function ($expression) {
            return "<?php
                \$__cache_directive_arguments = [{$expression}];

                if (count(\$__cache_directive_arguments) === 2) {
                    [\$__cache_directive_key, \$__cache_directive_ttl] = \$__cache_directive_arguments;
                } else {
                    [\$__cache_directive_key] = \$__cache_directive_arguments;
                    \$__cache_directive_ttl = config('blade-cache-directive.ttl');
                }

                if (config('blade-cache-directive.enabled') && \Illuminate\Support\Facades\Cache::has(\$__cache_directive_key)) {
                    echo \Illuminate\Support\Facades\Cache::get(\$__cache_directive_key);
                } else {
                    \$__cache_directive_buffering = true;

                    ob_start();
            ?>";
        });

        Blade::directive('endcache', function () {
            return "<?php
                    \$__cache_directive_buffer = ob_get_clean();

                    \Illuminate\Support\Facades\Cache::put(\$__cache_directive_key, \$__cache_directive_buffer, \$__cache_directive_ttl);

                    echo \$__cache_directive_buffer;

                    unset(\$__cache_directive_key, \$__cache_directive_ttl, \$__cache_directive_buffer, \$__cache_directive_buffering, \$__cache_directive_arguments);
                }
            ?>";
        });
    }
}
