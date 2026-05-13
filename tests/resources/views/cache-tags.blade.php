@cache('tagged-key', 60, ['blade-cache-test'])
    {{ $time->format('Y-m-d H:i:s') }}
@endcache