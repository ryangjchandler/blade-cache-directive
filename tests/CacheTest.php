<?php

namespace RyanChandler\BladeCacheDirective\Tests;

use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\Test;

class CacheTest extends TestCase
{
    protected $first_value;

    protected $second_value;

    protected $third_value;

    protected function setUp(): void
    {
        parent::setUp();

        $this->first_value = now();
        $this->second_value = now()->subDays(20);
        $this->third_value = now()->subDays(60);
    }

    #[Test]
    public function the_cache_directive_will_render_the_same_view_before_ttl_expired()
    {
        $time = $this->first_value;
        $this->assertEquals($this->first_value->format('Y-m-d H:i:s'), $this->renderView('cache', compact('time')));

        $time = $this->second_value;
        $this->assertEquals($this->first_value->format('Y-m-d H:i:s'), $this->renderView('cache', compact('time')));
    }

    #[Test]
    public function the_cache_directive_will_render_other_view_after_ttl_expired()
    {
        $time = $this->first_value;
        $this->assertEquals($this->first_value->format('Y-m-d H:i:s'), $this->renderView('cache', compact('time')));

        sleep(2);
        $time = $this->second_value;
        $this->assertNotEquals($this->first_value, $this->second_value);
        $this->assertEquals($this->second_value->format('Y-m-d H:i:s'), $this->renderView('cache', compact('time')));
    }

    #[Test]
    public function the_cache_directive_can_be_cached_forever(): void
    {
        Cache::flush();

        $time = $this->first_value;

        $first = $this->renderView('cache-forever', compact('time'));

        Carbon::setTestNow(now()->addYears(5));

        $time = $this->second_value;

        $second = $this->renderView('cache-forever', compact('time'));

        $this->assertEquals($first, $second);
    }

    #[Test]
    public function the_cache_directive_supports_cache_tags(): void
    {
        if (! Cache::supportsTags()) {
            $this->markTestSkipped('Cache store does not support tags.');
        }

        Cache::flush();

        $time = $this->first_value;

        $first = $this->renderView('cache-tags', compact('time'));

        Cache::tags(['blade-cache-test'])->flush();

        $time = $this->second_value;

        $second = $this->renderView('cache-tags', compact('time'));

        $this->assertNotEquals($first, $second);
    }

    #[Test]
    public function the_cache_directive_can_be_disabled()
    {
        config()->set('blade-cache-directive.enabled', false);

        $first = $this->renderView('disabled');

        Carbon::setTestNow(now()->addMinute());

        $second = $this->renderView('disabled');

        $this->assertNotEquals($first, $second);
    }

    #[Test]
    public function the_cache_directive_uses_default_ttl_when_null_is_provided(): void
    {
        Cache::flush();

        // On force un TTL très court pour le test
        config()->set('blade-cache-directive.ttl', 1);

        $time = $this->first_value;

        $first = $this->renderView('cache-null-ttl', compact('time'));

        // On attend que le TTL par défaut expire
        sleep(2);

        $time = $this->second_value;

        $second = $this->renderView('cache-null-ttl', compact('time'));

        $this->assertNotEquals($first, $second);
    }
    
    protected function renderView($view, $parameters = [])
    {
        Artisan::call('view:clear');

        if (is_string($view)) {
            $view = view($view)->with($parameters);
        }

        return trim((string) ($view));
    }
}
