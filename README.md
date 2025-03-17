# Blade Cache Directive

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ryangjchandler/blade-cache-directive.svg?style=flat-square)](https://packagist.org/packages/ryangjchandler/blade-cache-directive)
[![Total Downloads](https://img.shields.io/packagist/dt/ryangjchandler/blade-cache-directive.svg?style=flat-square)](https://packagist.org/packages/ryangjchandler/blade-cache-directive)

Cache chunks of your Blade markup with ease.

## Installation

You can install the package via Composer:

```bash
composer require ryangjchandler/blade-cache-directive
```

You can publish the config file with:
```bash
php artisan vendor:publish --provider="RyanChandler\BladeCacheDirective\BladeCacheDirectiveServiceProvider" --tag="blade-cache-directive-config"
```

This is the contents of the published config file:

```php
return [

    'enabled' => env('BLADE_CACHE_DIRECTIVE_ENABLED', true),

    'ttl' => env('BLADE_CACHE_DIRECTIVE_TTL', 3600),

];
```

## Usage

This package adds a new `@cache` Blade directive. It accepts 2 arguments - the cache key and a TTL.

```blade
@cache('current_time', 30)
    {{ now() }}
@endcache
```

When used inside of a Blade template, the content between the 2 directives will be cached using Laravel's application cache. If a TTL (in seconds) isn't provided, the default TTL of **1 hour** will be used instead.

If you want to cache the content for a particular model, i.e. a `User` model, you can use string interpolation to change the key.

```blade
@cache("user_profile_{$user->id}")
    {{ $user->name }}
@endcache
```

When a new user is passed to this view, a separate cache entry will be created.

### Disabling caching

If you wish to disable caching when using the `@cache` directive (useful for local development and testing), you can set the `BLADE_CACHE_DIRECTIVE_ENABLED` environment variable to `false`.

Alternatively, publish the configuration file and modify the `enabled` entry accordingly.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Ryan Chandler](https://github.com/ryangjchandler)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
