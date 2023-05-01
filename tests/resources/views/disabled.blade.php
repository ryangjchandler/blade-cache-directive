@cache('now', now()->addDay())
    {{ now()->format('H:i:s') }}
@endcache
