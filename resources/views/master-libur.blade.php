<x-layout title="Master Libur">
    @section('title', 'Master Libur')
    <x-slot:title>{{$title}}</x-slot:title>
    <section class="bg-gray-100 w-full relative px-4 py-4 sm:px-6">
        @if ($errors->any())
        <div id="error-alert" class="relative flex w-full items-center p-4 mb-4 text-red-800 border border-red-300 bg-red-50 dark:text-red-400 dark:bg-gray-800 dark:border-red-800" role="alert">
            <!-- Progress line (border) at the top -->
            <div id="error-progress-bar" class="absolute top-0 left-0 h-1 bg-red-500" style="width: 100%; transition: width 5s linear;"></div>

            <!-- Icon -->
            <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
            </svg>

            <!-- Alert message -->
            <div class="ms-3 text-sm font-medium">
                Please fix the following errors:
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>

            <!-- Close button in the top-right corner -->
            <button type="button" id="close-error-alert" class="absolute top-2 right-2 bg-red-50 text-red-500 rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-200 dark:bg-gray-800 dark:text-red-400 dark:hover:bg-gray-700" aria-label="Close">
                <span class="sr-only">Close</span>
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
        @endif

        @if(session('success'))
        <div id="success-alert" class="hidden relative flex w-full items-center p-4 mb-4 text-green-800 border border-green-300 bg-green-50 dark:text-green-400 dark:bg-gray-800 dark:border-green-800" role="alert">
            <!-- Progress line (border) at the top -->
            <div id="progress-bar" class="absolute top-0 left-0 h-1 bg-green-500" style="width: 100%; transition: width 5s linear;"></div>

            <!-- Icon -->
            <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
            </svg>

            <!-- Alert message -->
            <div class="ms-3 text-sm font-medium">
                Data berhasil ditambahkan.
            </div>

            <!-- Close button in the top-right corner -->
            <button type="button" id="close-alert" class="absolute top-2 right-2 bg-green-50 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 dark:bg-gray-800 dark:text-green-400 dark:hover:bg-gray-700" aria-label="Close">
                <span class="sr-only">Close</span>
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
        @endif

        @if(session('successdel'))
        <div id="success-alert" class="hidden relative flex w-full items-center p-4 mb-4 text-green-800 border border-green-300 bg-green-50 dark:text-green-400 dark:bg-gray-800 dark:border-green-800" role="alert">
            <!-- Progress line (border) at the top -->
            <div id="progress-bar" class="absolute top-0 left-0 h-1 bg-green-500" style="width: 100%; transition: width 5s linear;"></div>

            <!-- Icon -->
            <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
            </svg>

            <!-- Alert message -->
            <div class="ms-3 text-sm font-medium">
                Data berhasil dihapus.
            </div>

            <!-- Close button in the top-right corner -->
            <button type="button" id="close-alert" class="absolute top-2 right-2 bg-green-50 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 dark:bg-gray-800 dark:text-green-400 dark:hover:bg-gray-700" aria-label="Close">
                <span class="sr-only">Close</span>
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
        @endif

        @if(session('status'))
        <div id="success-alert" class="hidden relative flex w-full items-center p-4 mb-4 text-green-800 border border-green-300 bg-green-50 dark:text-green-400 dark:bg-gray-800 dark:border-green-800" role="alert">
            <!-- Progress line (border) at the top -->
            <div id="progress-bar" class="absolute top-0 left-0 h-1 bg-green-500" style="width: 100%; transition: width 5s linear;"></div>

            <!-- Icon -->
            <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
            </svg>

            <!-- Alert message -->
            <div class="ms-3 text-sm font-medium">
                Data berhasil diupdate.
            </div>

            <!-- Close button in the top-right corner -->
            <button type="button" id="close-alert" class="absolute top-2 right-2 bg-green-50 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 dark:bg-gray-800 dark:text-green-400 dark:hover:bg-gray-700" aria-label="Close">
                <span class="sr-only">Close</span>
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
        @endif
        <!-- Tombol Add User -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
            <!-- Search Bar on the Left (responsive) -->
            <form class="w-full sm:w-2/3 max-w-lg sm:mr-4 m-0 p-0">
                @csrf
                <label for="default-search" class="mb-2 text-sm font-medium text-gray-900 sr-only ">Search</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 " aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                        </svg>
                    </div>
                    <input name="search" type="search" id="default-search" value="{{ old('search') }}" class="block w-full p-4 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 " placeholder="Search Libur Name" autocomplete="off" />
                    <button type="submit" class="text-white absolute right-20 bottom-2.5 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2  ">Search</button>
                    <a href="{{route('libur.index')}}">
                        <button class="text-white absolute right-2.5 bottom-2.5 bg-gray-500 hover:bg-gray-600 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-4 py-2 ">Clear</button>
                    </a>
                </div>
            </form>
            <div x-data="{ openCreate: false, openEdit: false, selectedUserId: null }" class="flex justify-end mb-4 mr-4">
                <button onclick="openCreateModal()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                    + Add Hari Libur
                </button>
            </div>
        </div>

        <!-- Tabel User -->
        <div class="overflow-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">No</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Nama</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Tanggal</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($liburs as $key => $libur)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $liburs->firstItem() + $key}}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $libur->name}}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $libur->tanggal}}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 space-x-2">
                            <button type="button" id="edit-button" data-modal-target="editModal" data-modal-toggle="editModal" data-user-id="{{ $libur->id }}" data-user-name="{{ $libur->name }}" data-user-tanggal="{{ $libur->tanggal }}" class="text-indigo-600 hover:underline">Edit</button>

                            <form action="{{ route('libur.destroy', $libur->id) }}"
                                method="POST" class="inline-block"
                                onsubmit="return confirm('Yakin ingin menghapus libur ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Modal Create -->
        <div id="createModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
            <div class="bg-white rounded-lg w-full max-w-md p-6 space-y-4">
                <h2 class="text-xl font-bold">Add Libur</h2>
                <form method="post" action="{{route('libur.store')}}">
                    @csrf
                    <div class="space-y-2">
                        <div>
                            <label class="block text-sm">Name</label>
                            <input type="text" class="w-full border rounded px-3 py-2" name="name">
                        </div>
                        <div>
                            <label class="block text-sm">Tanggal</label>
                            <input type="date" class="w-full border rounded px-3 py-2" name="tanggal">
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end space-x-2">
                        <button type="button" onclick="closeCreateModal()" class="px-4 py-2 bg-gray-300 rounded">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Edit -->
        <div id="editModal" class="fixed hidden inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
            <div class="bg-white p-6 rounded shadow w-full max-w-md">
                <h2 class="text-lg font-bold mb-4">Edit Libur</h2>
                <form id="editForm" method="post" action="{{ route('libur.update', ':id') }}">
                    @csrf
                    <div class="space-y-2">
                        <div>
                            <label class="block text-sm">Name</label>
                            <input type="text" class="w-full border rounded px-3 py-2" name="name" id="editName">
                        </div>
                        <div>
                            <label class="block text-sm">Tanggal</label>
                            <input type="date" class="w-full border rounded px-3 py-2" name="tanggal" id="editTanggal">
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end space-x-2">
                        <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-gray-300 rounded">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Update</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="p-4">
            {{ $liburs->links() }}
        </div>

    </section>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[data-user-id]').forEach(button => {
                button.addEventListener('click', function() {
                    console.log(this.getAttribute('data-user-id'));
                    const id = this.getAttribute('data-user-id');
                    const name = this.getAttribute('data-user-name');
                    const tanggal = this.getAttribute('data-user-tanggal');
                    // console.log('User ID:', id);
                    // console.log('Name:', name);
                    // console.log('Username:', username);
                    // console.log('Email:', email);
                    // console.log('Role:', role);
                    // console.log('Tahun Masuk:', tahun_masuk);

                    const updateForm = document.getElementById('editForm');
                    if (updateForm) {
                        const url = "{{ route('libur.update', ':id') }}".replace(':id', id);
                        updateForm.action = url;
                        console.log(url);

                        document.getElementById('editName').value = name;
                        document.getElementById('editTanggal').value = tanggal;
                    }
                    const updateModal = document.getElementById('editModal');
                    if (updateModal) {
                        updateModal.classList.remove('hidden');
                    }
                });
            });
            const errorAlertBox = document.getElementById('error-alert');
            const errorCloseButton = document.getElementById('close-error-alert');
            const errorProgressBar = document.getElementById('error-progress-bar');

            if (errorAlertBox) {
                errorProgressBar.style.transition = 'none';
                errorProgressBar.style.width = '100%'; // Start full

                setTimeout(() => {
                    errorProgressBar.style.transition = 'width 5s linear';
                    errorProgressBar.style.width = '0'; // Shrink to 0 over 5 seconds

                    // Hide the alert after the progress bar animation completes
                    setTimeout(() => {
                        errorAlertBox.classList.add('hidden');
                    }, 5000); // Matches the duration of the animation (5s)
                }, 100); // Small delay to make sure DOM is ready for transition
            }

            // Close the error alert manually when the close button is clicked
            if (errorCloseButton) {
                errorCloseButton.addEventListener('click', function() {
                    errorAlertBox.classList.add('hidden');
                });
            }

            const alertBox = document.getElementById('success-alert');
            const closeButton = document.getElementById('close-alert');
            const progressBar = document.getElementById('progress-bar');

            function showAlert() {
                alertBox.classList.remove('hidden');
                progressBar.style.transition = 'none'; // Disable transition to reset
                progressBar.style.width = '100%'; // Start full

                // Delay to let the browser process width change, then start animation
                setTimeout(() => {
                    progressBar.style.transition = 'width 5s linear'; // Enable transition
                    progressBar.style.width = '0'; // Shrink to 0 over 5 seconds
                }, 100); // Short delay to allow DOM update

                // Automatically hide the alert after 5 seconds
                setTimeout(() => {
                    alertBox.classList.add('hidden');
                }, 5100); // Delay slightly longer than the transition
            }

            // Close the alert when the close button is clicked
            closeButton.addEventListener('click', () => {
                alertBox.classList.add('hidden');
            });

            showAlert();
        });

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        function openCreateModal() {
            document.getElementById('createModal').classList.remove('hidden');
        }

        function closeCreateModal() {
            document.getElementById('createModal').classList.add('hidden');
        }
    </script>
</x-layout>