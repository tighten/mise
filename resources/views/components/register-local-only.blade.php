@isset($register_local_only_providers)
    @if (count($register_local_only_providers) > 0)
        if ($this->app->environment('local'))
        {
            @foreach ($register_local_only_providers as $code)
                {!! $code !!}
            @endforeach
        }
    @endif
@endisset
