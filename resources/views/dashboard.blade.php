<x-layout title="Dashboard">
    @section('title', 'Dashboard')
    <x-slot:title>{{$title}}</x-slot:title>
    <section class="bg-gray-100 w-full relative px-4 py-4 sm:px-6">
        @if ($isProfileIncomplete)
        <div id="alert-border-2" class="flex w-full items-center p-4 mb-4 text-red-800 border-t-4 border-red-300 bg-red-50" role="alert">
            <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
            </svg>
            <div class="ms-3 text-sm font-medium">
                Mohon lengkapi data diri anda terlebih dahulu di halaman profile.
            </div>
        </div>
        @endif

        <div class="container mx-auto mt-4 p-6">
            <div class="flex justify-between items-center mb-4">
                <div id="welcome-message" class="text-2xl font-bold">Welcome, </div>
            </div>

            {{-- Tombol Install PWA --}}
            <div id="pwa-section" class="mt-8 p-6 rounded-lg bg-white shadow-md text-center hidden">
                <h2 class="text-xl font-semibold mb-2">Ingin akses lebih cepat?</h2>
                <p class="mb-4 text-gray-600">Install aplikasi ke perangkat Anda untuk pengalaman yang lebih nyaman dan cepat.</p>
                <button id="pwa-install-btn" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow transition duration-200 justify-self-center">
                    Install Aplikasi
                </button>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const name = "{{ Auth::user()->name }}";
            const welcomeElement = document.getElementById("welcome-message");
            let index = 0;

            function displayNextCharacter() {
                if (index < name.length) {
                    welcomeElement.innerHTML += name[index];
                    index++;
                    setTimeout(displayNextCharacter, 300); // Adjust the timing as needed
                } else {
                    welcomeElement.innerHTML = "Welcome, " + name;
                }
            }

            displayNextCharacter();
        });
    </script>
</x-layout>