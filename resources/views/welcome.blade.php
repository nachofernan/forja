<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Forja de Recursos</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
        
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body {
                font-family: 'Inter', sans-serif;
            }
        </style>
        
        <!-- Livewire Styles -->
        @livewireStyles
    </head>
    <body class="<!-- bg-gray-100 --> bg-gradient-to-br from-slate-900 to-slate-800 min-h-screen">
        <div class="container mx-auto w-full h-full">
            <div class="mx-auto">
                <!-- <h1 class="text-3xl font-bold text-center mb-8 text-gray-800">Forja de Recursos</h1> -->
                
                <!-- Game Component -->
                <livewire:game />
            </div>
        </div>
        
        <!-- Livewire Scripts -->
        @livewireScripts
    </body>
</html>
