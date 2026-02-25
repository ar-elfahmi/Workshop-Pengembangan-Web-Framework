<!DOCTYPE html>
<html lang="en">

@include('partials.header')

<body>
<div class="container-scroller">

    {{-- Navbar --}}
    @include('partials.navbar')

    <div class="container-fluid page-body-wrapper">

        {{-- Sidebar --}}
        @include('partials.sidebar')

        <div class="main-panel">
            <div class="content-wrapper">

                {{-- Content --}}
                @yield('content')

            </div>

            {{-- Footer --}}
            @include('partials.footer')
        </div>

    </div>
</div>

{{-- Javascript Global --}}
@include('partials.scripts')

{{-- Javascript Page --}}
@stack('page-scripts')

</body>
</html>
