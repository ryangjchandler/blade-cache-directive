<?php

namespace RyanChandler\BladeCacheDirective\Tests;

use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;

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

    /** @test */
    public function the_cache_directive_will_render_the_same_view_before_ttl_expired()
    {
        $time = $this->first_value;
        $this->assertEquals($this->first_value->format('Y-m-d H:i:s'), $this->renderView('cache', compact('time')));

        $time = $this->second_value;
        $this->assertEquals($this->first_value->format('Y-m-d H:i:s'), $this->renderView('cache', compact('time')));
    }

    /** @test */
    public function the_cache_directive_will_render_other_view_after_ttl_expired()
    {
        $time = $this->first_value;
        $this->assertEquals($this->first_value->format('Y-m-d H:i:s'), $this->renderView('cache', compact('time')));

        sleep(2);
        $time = $this->second_value;
        $this->assertNotEquals($this->first_value, $this->second_value);
        $this->assertEquals($this->second_value->format('Y-m-d H:i:s'), $this->renderView('cache', compact('time')));
    }

    /** @test */
    public function the_cache_directive_can_be_disabled()
    {
        config()->set('blade-cache-directive.enabled', false);

        $first = $this->renderView('disabled');

        Carbon::setTestNow(now()->addMinute());

        $second = $this->renderView('disabled');

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
