<?php

namespace RyanChandler\BladeCacheDirective\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use RyanChandler\BladeCacheDirective\BladeCacheDirectiveServiceProvider;

class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'RyanChandler\\BladeCacheDirective\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            BladeCacheDirectiveServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
        config()->set('cache.default', 'file');

        /*
        include_once __DIR__.'/../database/migrations/create_blade-cache-directive_table.php.stub';
        (new \CreatePackageTable())->up();
        */

        $app['config']->set('view.paths', [__DIR__ . '/resources/views']);
    }
}
