@cache('forever-key', 'forever')
    {{ $time->format('Y-m-d H:i:s') }}
@endcache