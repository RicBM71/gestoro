<!doctype html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="author" content="Sanaval Tecnología">
        <meta name="description" content="Laravel Vue SPA">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name') }} </title>

        <!-- Fonts -->
        <link href='https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Material+Icons' rel="stylesheet">

        <!-- Styles -->
        {{-- <link href="https://unpkg.com/vuetify/dist/vuetify.min.css" rel="stylesheet"> --}}

        <script>
            window.Laravel = {!! json_encode([
                'siteName'  => config('app.name'),
            ]) !!}
        </script>
    </head>
    <body>
        <div id="app">
            <v-app id="inspire">
                    <router-view></router-view>
            </v-app>
        </div>

        <script src="{{ mix('/js/app.js') }}"></script>

    </body>
</html>
