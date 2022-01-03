<!DOCTYPE html>
<html>
    <head>
        <title>Casino Admin</title>
        <script src="https://kit.fontawesome.com/23f13eab24.js" crossorigin="anonymous"></script>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, height=device-height, minimum-scale=1, maximum-scale=1, user-scalable=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!--
        <meta property="og:image" content="{{ asset('https://i.imgur.com/HEAm2j7.png') }}" />
        <meta property="og:image:secure_url" content="{{ asset('https://i.imgur.com/omJ7On3.png') }}" />
        <meta property="og:image:type" content="image/svg+xml" />
        <meta property="og:image:width" content="295" />
        <meta property="og:image:height" content="295" />
        !-->
        @if(config('app.debug'))
            <meta http-equiv="Expires" content="Mon, 26 Jul 1997 05:00:00 GMT">
            <meta http-equiv="Pragma" content="no-cache">
        @endif

        <link rel="icon" href="{{ asset('/favicon.svg') }}">
        <link rel="manifest" href="/manifest.json">

        <script type="text/javascript">
            window.Layout = {
                Backend: '{!! base64_encode(file_get_contents(public_path('css/admin/app.css'))) !!}'
            }
        </script>

        <script>
            window.Notifications = {
                vapidPublicKey: '{{ config('webpush.vapid.public_key') }}'
            };
        </script>

    </head>
    <body>
        <div id="app">
            <layout></layout>
        </div>

        <script src="{{ asset(mix('/js/admin/app.js')) }}"></script>

        @if(config('app.debug'))
            <script src="http://localhost:8098"></script>
        @endif
    </body>
</html>
