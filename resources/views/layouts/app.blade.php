<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, interactive-widget=overlays-content">
    <title>{{ $title ?? config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.boxicons.com" crossorigin>
    <link rel="preconnect" href="https://tetunori.github.io" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <link href='https://cdn.boxicons.com/3.0.8/fonts/basic/boxicons.min.css' rel='stylesheet'>
    <link href='https://cdn.boxicons.com/3.0.8/fonts/filled/boxicons-filled.min.css' rel='stylesheet'>
    <link href='https://cdn.boxicons.com/3.0.8/fonts/brands/boxicons-brands.min.css' rel='stylesheet'>
    <link href='https://tetunori.github.io/fluent-emoji-webfont/dist/FluentEmojiColor.css' rel='stylesheet'>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="fixed top-0 left-0 h-[var(--app-height,100svh)] w-full flex justify-center bg-gray-100 font-sans text-gray-800 overflow-hidden select-none">
    <div class="relative flex h-full w-full flex-col overflow-hidden shadow-xl">
        <livewire:components.header-bar />

        <main class="no-scrollbar max-w-[900px] mx-auto w-full flex-1 overflow-y-auto pb-28">
            {{ $slot }}
        </main>

        <x-ui.nav-bar />
    </div>

    @livewireScripts
</body>
</html>
