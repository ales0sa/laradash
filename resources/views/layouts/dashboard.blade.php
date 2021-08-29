
<!DOCTYPE html>
<html lang="{{ App::getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="/favicon-16x16.png" type="image/png" />
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="public-path" content="{{ asset('/') }}">
    <!-- <meta name="storage-path" content="{{ asset(Storage::url('/')) }}"> -->
    <meta name="decimal-separator" content="{{ __config_var('decimal_separator') }}">
    <meta name="thousands-separator" content="{{ __config_var('thousands_separator') }}">
    <meta name="3c3aazbg5" content="{{ floatval(ini_get('upload_max_filesize')) * 1024 }}">
    <meta name="f983jd020" content="{{ floatval(ini_get('post_max_size')) * 1024 }}">

    <title>{{ config('app.name', 'Panel') }}</title>



<link href="/css/layout.css" rel="stylesheet" />
<link type="text/css" rel="stylesheet" href="https://unpkg.com/primeicons/primeicons.css"  />
<script src="https://kit.fontawesome.com/a82e74739c.js" crossorigin="anonymous"></script>
</head>
<body >

    @if (Auth::check())
    <div id="app" class="">
    </div>

        <!-- Scripts -->
        <script src="{{ asset('js/dashboard.js') }}?{{ $assets_version }}" defer></script>


    <script>

            window.logo            = '{{ Storage::url(__cf("header_logo")) }}'
            window.authUser        = {!! json_encode(Auth::user()); !!};


     </script>

<script>
  @auth
    window.Permissions = {!! json_encode(Auth::user()->permissions, true) !!};
  @else
    window.Permissions = [];
  @endauth
</script>

    @else
    @yield('content')
         <script>window.authUser=null;</script>
    @endif


</body>

</html>
