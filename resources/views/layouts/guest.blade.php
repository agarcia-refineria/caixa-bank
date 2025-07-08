<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <link rel="icon" href="{{ asset('img/favicon.svg') }}" type="image/png">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="locale" content="{{ app()->getLocale() }}-{{ strtoupper(app()->getLocale()) }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/fonts.css'])

        <!-- Scripts -->
        @vite(['resources/js/app.js', 'resources/css/app.css'])

        @vite(['resources/css/select2.css'])
        @vite(['resources/css/datatable.css'])
    </head>
    <body class="font-Inter text-main2 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-main1">
            <x-site.flash-messages />

            <div>
                <a href="/" class="block lg:w-[400px] h-[200px]">
                    <x-application-logo />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-main2 shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
