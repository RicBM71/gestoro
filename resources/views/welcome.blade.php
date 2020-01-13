<!doctype html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="author" content="Sanaval Tecnología">
        <meta name="description" content="Desarrollo web de aplicaciones a medida con Laravel y VueJs. Alojamos tu
            aplicación en un servidor propio. Desarrollo profesional, adaptado a tus necesidades.">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name') }} </title>

        <!-- Fonts -->
        <link href='https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Material+Icons' rel="stylesheet">

        <!-- Styles -->
        {{-- <link href="https://unpkg.com/vuetify/dist/vuetify.min.css" rel="stylesheet"> --}}

        <script>
            window.Laravel = {!! json_encode([
                'csrfToken' => csrf_token(),
                'siteName'  => config('app.name'),
                'apiDomain' => '/api'
                //'apiDomain' => config('app.url').'/api'
            ]) !!}
        </script>
    </head>
    <body>
        <div id="app">
            <v-app id="inspire" >
                    <router-view></router-view>
            </v-app>
        </div>

        <script src="{{ mix('/js/app.js') }}"></script>

    </body>
</html>
