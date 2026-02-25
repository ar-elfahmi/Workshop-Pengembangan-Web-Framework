<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Purple Admin')</title>

    {{-- Style Global --}}
    @include('partials.styles')

    {{-- Style Page --}}
    @stack('page-styles')

</head>
