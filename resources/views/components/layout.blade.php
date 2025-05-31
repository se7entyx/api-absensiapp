<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Dashboard' }}</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body class="bg-gray-100 h-full flex overflow-x-hidden">
    <x-sidebar></x-sidebar>

    <div class="flex flex-grow flex-col sm:ml-64 bg-white overflow-hidden">
        <x-header>{{ $title }}</x-header>

        <main class="w-full flex justify-center sm:justify-start items-center sm:items-start">
            {{ $slot }}
        </main>
    </div>

    @vite('resources/js/app.js')
    <script src="https://unpkg.com/flowbite@latest/dist/flowbite.min.js"></script>
    <script src="https://unpkg.com/alpinejs" defer></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>

</html>