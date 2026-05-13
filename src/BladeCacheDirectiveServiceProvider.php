<?php

declare(strict_types=1);

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

                \$__cache_directive_key = \$__cache_directive_arguments[0];
               
                if (isset(\$__cache_directive_arguments[1])) {
                  \$__cache_directive_ttl = \$__cache_directive_arguments[1];
                } else {
                  \$__cache_directive_ttl = config('blade-cache-directive.ttl');
                }

                if (isset(\$__cache_directive_arguments[2])) {
                    \$__cache_directive_tags = (array) \$__cache_directive_arguments[2];
                } else {
                    \$__cache_directive_tags = [];
                }

                \$__cache_store = empty(\$__cache_directive_tags)
                    ? \Illuminate\Support\Facades\Cache::store()
                    : \Illuminate\Support\Facades\Cache::tags(\$__cache_directive_tags);
                
                if (
                    config('blade-cache-directive.enabled') &&
                    \$__cache_store->has(\$__cache_directive_key)
                ) {
                    echo \$__cache_store->get(\$__cache_directive_key);
                } else {
                    ob_start();
            ?>";
        });

        Blade::directive('endcache', function () {
            return "<?php
                    \$__cache_directive_buffer = ob_get_clean();

                    if (\$__cache_directive_ttl === 'forever') {
                        \$__cache_store->forever(
                            \$__cache_directive_key,
                            \$__cache_directive_buffer
                        );
                    } else {
                        \$__cache_store->put(
                            \$__cache_directive_key,
                            \$__cache_directive_buffer,
                            \$__cache_directive_ttl
                        );
                    }

                    echo \$__cache_directive_buffer;

                    unset(
                        \$__cache_directive_key,
                        \$__cache_directive_ttl,
                        \$__cache_directive_tags,
                        \$__cache_directive_buffer,
                        \$__cache_store,
                        \$__cache_directive_arguments
                    );
                }
            ?>";
        });
    }
}
