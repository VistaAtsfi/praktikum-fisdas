<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Praktikum Fisika Dasar') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="font-sans text-slate-900 antialiased">
        <div class="min-h-screen bg-gradient-to-br from-blue-950 via-blue-800 to-cyan-700 flex items-center justify-center px-4 py-10 relative overflow-hidden">

            <!-- Background decoration -->
            <div class="absolute -top-24 -left-24 w-80 h-80 rounded-full bg-cyan-300/20 blur-3xl"></div>
            <div class="absolute -bottom-24 -right-24 w-96 h-96 rounded-full bg-blue-300/20 blur-3xl"></div>
            <div class="absolute top-1/3 right-1/4 w-40 h-40 rounded-full bg-white/10 blur-2xl"></div>

            <div class="relative w-full max-w-md">
                <!-- Logo / Header -->
                <div class="text-center mb-8">
                    <a href="/" class="inline-flex items-center justify-center w-16 h-16 rounded-3xl bg-white text-blue-800 text-3xl shadow-xl mb-4">
                        ⚛
                    </a>

                    <h1 class="text-2xl font-extrabold text-white">
                        Praktikum Fisika Dasar
                    </h1>

                    <p class="text-blue-100 text-sm mt-1">
                        Laboratorium Layanan Dasar Fakultas Teknik
                    </p>
                </div>

                <!-- Auth card -->
                <div class="bg-white/95 backdrop-blur rounded-3xl shadow-2xl border border-white/30 overflow-hidden">
                    <div class="h-2 bg-gradient-to-r from-blue-700 via-cyan-500 to-indigo-700"></div>

                    <div class="px-7 py-8">
                        {{ $slot }}
                    </div>
                </div>

                <div class="text-center mt-6">
                    <a href="/" class="text-sm text-blue-100 hover:text-white font-semibold">
                        ← Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </body>
</html>