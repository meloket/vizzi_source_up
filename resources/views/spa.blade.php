@php
$config = [
    'appName' => config('app.name'),
    'locale' => $locale = app()->getLocale(),
    'locales' => config('app.locales'),
    'githubAuth' => config('services.github.client_id'),
    'domain' => $domain->id,
    'domain_title' => $domain->title,
    'custom_login' => $domain->custom_login
];
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-176810890-1"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());

            gtag('config', 'UA-176810890-1');
        </script>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>{{ $domain->title }} {{ config('app.name') }}</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <link rel="stylesheet" href="{{asset('assets/fonts/simple-line-icons/css/simple-line-icons.css')}}">
        <link rel="stylesheet" href="{{asset('assets/fonts/iconsmind-s/css/iconsminds.css')}}">
        <link rel="stylesheet" href="{{asset('assets/css/vendor/home.min.css')}}">
        <link rel="stylesheet" href="{{asset('assets/css/timezone/jquery.timezonewidget.css')}}">
        <!-- Chosen -->
        <link rel="stylesheet" href="{{asset('assets/chosen/chosen.css')}}">
        <link rel="stylesheet" href="{{ mix('dist/css/app.css') }}?v={{ ("" . env('BUILD_NUMBER')) }}">
        <link rel="stylesheet" href="/js/videojs/video-js.css"/>
        <script type="text/javascript" src="https://addevent.com/libs/atc/1.6.1/atc.min.js" async defer></script>
        <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    </head>
    <body class="rounded">
        <div id="app"></div>

        {{-- Global configuration object --}}
        <script>

window.config = @json($config);

// this is now "break glass in case of emergency"
 let seconds_hotreload = 30; // todo: make 90

 $(function () {
   window.setInterval(function () {
     $.get('/check-version', function (res) {
       console.log("HOT RELOAD?", res);
       if (parseInt(res) != parseInt('{{ env('BUILD_NUMBER') }}')) {
         document.location.reload();
       }
     });
   }, seconds_hotreload * 1000); // 1.5 minutes
 });

        </script>

        {{-- Load the application scripts --}}
        <script type="text/javascript" src="https://oss.sheetjs.com/sheetjs/xlsx.full.min.js"></script>
        <script type="text/javascript" src="{{asset('assets/timezone/jquery.timezonewidget.js')}}"></script>
        <!-- chosen -->
        <script src="{{asset('assets/chosen/chosen.jquery.min.js')}}" type="text/javascript"></script>
        <script src="{{ mix('dist/js/app.js') }}"></script>
        <script src="/js/videojs/video.js"></script>
    
        <!-- Dash.js -->
        <script src="/js/dashjs/dash.all.min.js"></script>
    
        <!-- videojs-contrib-dash script -->
        <script src="/js/videojs-dash/videojs-dash.min.js"></script>
    </body>
</html>
