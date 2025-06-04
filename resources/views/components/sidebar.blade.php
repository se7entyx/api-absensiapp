<aside id="default-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen transition-transform -translate-x-full flex-shrink-0 sm:translate-x-0 bg-gray-900" aria-label="Sidebar">
    <div class="h-full flex flex-col justify-between px-3 py-4 overflow-y-auto bg-gray-50 ">
        <!-- <div class="space-y-2 p-4">
            <p class="flex items-center p-2 rounded hover:bg-gray-100">
                Presensi App
            </p>
        </div> -->
        <ul class="space-y-2 p-4">
            <li>
                <a href="{{route('profile')}}" class="flex items-center p-2 rounded hover:bg-gray-100">
                    <span class="ml-2">Profile</span>
                </a>
            </li>
            <li>
                <a href="{{route('dashboard')}}" class="flex items-center p-2 rounded hover:bg-gray-100">
                    <span class="ml-2">Dashboard</span>
                </a>
            </li>

            <li>
                <button type="button" class="flex items-center w-full p-2 rounded hover:bg-gray-100"
                    data-collapse-toggle="presensiDropdown">
                    <span class="ml-2">Presensi</span>
                    <svg class="w-4 h-4 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <ul id="presensiDropdown" class="hidden space-y-1 pl-6">
                    <li>
                        <a href="{{route('presensi.dinas')}}" class="block py-1 hover:underline">Presensi Dinas</a>
                    </li>
                    <li>
                        <a href="{{route('presensi.my')}}" class="block py-1 hover:underline">My Presensi</a>
                    </li>
                    @if (Auth::user()->department->name === 'HRD')
                    <li>
                        <a href="{{route('presensi.rekap')}}" class="block py-1 hover:underline">Rekap Presensi</a>
                    </li>
                    @endif
                </ul>
            </li>

            <li>
                <button type="button" class="flex items-center w-full p-2 rounded hover:bg-gray-100"
                    data-collapse-toggle="izinDropdown">
                    <span class="ml-2">Izin</span>
                    <svg class="w-4 h-4 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <ul id="izinDropdown" class="hidden space-y-1 pl-6">
                    <li>
                        <a href="{{route('izin.index')}}" class="block py-1 hover:underline">New Izin</a>
                    </li>
                    <li>
                        <a href="{{route('izin.ongoing')}}" class="block py-1 hover:underline">Ongoing</a>
                    </li>
                    @if (Auth::user()->department->name == 'HRD')
                    <li>
                        <a href="{{route('izin.rekap')}}" class="block py-1 hover:underline">Rekap Izin</a>
                    </li>
                    @endif
                </ul>
            </li>

            <li>
                <button type="button" class="flex items-center w-full p-2 rounded hover:bg-gray-100"
                    data-collapse-toggle="cutiDropdown">
                    <span class="ml-2">Cuti</span>
                    <svg class="w-4 h-4 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <ul id="cutiDropdown" class="hidden space-y-1 pl-6">
                    <li>
                        <a href="{{route('cuti.index')}}" class="block py-1 hover:underline">New Cuti</a>
                    </li>
                    <li>
                        <a href="{{route('cuti.ongoing')}}" class="block py-1 hover:underline">Ongoing</a>
                    </li>
                    @if (Auth::user()->department->name == 'HRD')
                    <li>
                        <a href="{{route('cuti.rekap')}}" class="block py-1 hover:underline">Rekap Cuti</a>
                    </li>
                    @endif
                </ul>
            </li>
            @if (Auth::user()->role == 'admin')
            <li>
                <button type="button" class="flex items-center w-full p-2 rounded hover:bg-gray-100"
                    data-collapse-toggle="masterDropdown">
                    <span class="ml-2">Master Data</span>
                    <svg class="w-4 h-4 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <ul id="masterDropdown" class="hidden space-y-1 pl-6">
                    <li>
                        <a href="{{route('user.index')}}" class="block py-1 hover:underline">User</a>
                    </li>
                    <li>
                        <a href="{{route('department.index')}}" class="block py-1 hover:underline">Department</a>
                    </li>
                    <li>
                        <a href="{{route('libur.index')}}" class="block py-1 hover:underline">Hari Libur</a>
                    </li>
                    <li>
                        <a href="{{route('kantor.index')}}" class="block py-1 hover:underline">Kantor</a>
                    </li>
                </ul>
            </li>
            @endif
            <li class="space-y-2">
                <button type="button" class="flex items-center p-2 w-full text-gray-900 rounded-lg  hover:bg-gray-100  group" data-modal-target="log-out-modal" data-modal-toggle="log-out-modal">
                    <i class="fa-solid fa-sign-out-alt"></i>
                    <span class="flex-1 text-left ms-3 whitespace-nowrap">Log Out</span>
                </button>
            </li>
        </ul>
    </div>
</aside>

<div id="log-out-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md">
        <div class="p-5 text-center">
            <svg class="mx-auto mb-4 text-gray-400 w-12 h-12" fill="none" viewBox="0 0 20 20">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            <h3 class="mb-5 text-lg font-normal text-gray-500">Apakah Anda yakin ingin logout?</h3>
            <div class="flex justify-center gap-4">
                <button type="button" class="px-5 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-100" data-modal-hide="log-out-modal">Batal</button>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="px-5 py-2 text-sm text-white bg-blue-600 rounded hover:bg-blue-700" data-modal-hide="log-out-modal">
                        Ya, Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const sidebar = document.getElementById('default-sidebar');
        if (window.innerWidth < 640) {
            sidebar.classList.add('-translate-x-full');
        }
    });
</script>