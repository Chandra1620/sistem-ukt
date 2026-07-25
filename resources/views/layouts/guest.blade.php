<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0" style="background: linear-gradient(135deg, rgba(15, 43, 61, 0.42) 0%, rgba(10, 28, 42, 0.52) 100%), url('{{ asset('images/login-bg.png') }}'); background-size: cover; background-position: center; background-attachment: fixed;">
            <div>
                <a href="/" style="display:inline-block; background:#ffffff; padding:12px; border-radius:50%; box-shadow:0 10px 25px -5px rgba(0,0,0,0.4);">
                    <x-application-logo class="fill-current text-gray-500" style="display:block; width:96px; height:96px; object-fit:contain;" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 shadow-md overflow-hidden sm:rounded-lg" style="background: rgba(255, 255, 255, 0.62); backdrop-filter: blur(20px) saturate(150%); -webkit-backdrop-filter: blur(20px) saturate(150%); border: 1px solid rgba(255, 255, 255, 0.55);">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
