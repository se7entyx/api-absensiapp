<x-layout title="Master User">
    @section('title', 'Master User')
    <x-slot:title>{{$title}}</x-slot:title>
    <section class="bg-gray-100 w-full relative px-4 py-4 sm:px-6">
        <div id="loadingIndicator" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-100">
            <div class="bg-white p-4 rounded shadow text-center">
                <svg class="animate-spin h-5 w-5 text-blue-500 mx-auto mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <p>Loading...</p>
            </div>
        </div>
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
                    <input name="search" type="search" id="default-search" value="{{ old('search') }}" class="block w-full p-4 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 " placeholder="Search User Name" autocomplete="off" />
                    <button type="submit" class="text-white absolute right-20 bottom-2.5 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2  ">Search</button>
                    <a href="{{route('user.index')}}">
                        <button class="text-white absolute right-2.5 bottom-2.5 bg-gray-500 hover:bg-gray-600 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-4 py-2 ">Clear</button>
                    </a>
                </div>
            </form>
            <div x-data="{ openCreate: false, openEdit: false, selectedUserId: null }" class="flex justify-end mb-4 mr-4">
                <button onclick="openCreateModal()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                    + Add User
                </button>
            </div>
        </div>

        <!-- Tabel User -->
        <div class="overflow-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">No</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">UID</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Username</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Name</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Department</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Email</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Role</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">@sortablelink('status','Status')</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Foto</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Tanda Tangan</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-600">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($users as $key => $user)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $users->firstItem() + $key}}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $user['uid']}}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{$user['username']}}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{$user['name']}}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $user['department']['name'] ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{$user['email']}}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{$user['role']}}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            @if ($user->status == 'active')
                            <p class="text-green-500"> {{$user->status}} </p>
                            @else
                            <p class="text-red-500"> {{$user->status}} </p>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{$user['image']}}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{$user['signature']}}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 space-x-2">
                            <button type="button" id="edit-button" data-modal-target="editModal" data-modal-toggle="editModal" data-user-id="{{ $user['id'] }}" data-user-uid="{{ $user['uid'] }}" data-user-name="{{ $user['name'] }}" data-user-username="{{ $user['username'] }}" data-user-email="{{ $user['email'] }}" data-user-image="{{ $user['image'] }}" data-user-signature="{{ $user['signature'] }}" data-user-department="{{ $user['department_id'] ? $user['department_id'] : '' }}" data-user-status="{{ $user['status'] }}" data-user-role="{{$user['role']}}" class="text-indigo-600 hover:underline">Edit</button>

                            <form action="{{ route('user.destroy', $user['id']) }}"
                                method="POST" class="inline-block"
                                onsubmit="return confirm('Yakin ingin menghapus user ini?');">
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
                <h2 class="text-xl font-bold">Add User</h2>
                <form id="addForm" method="post" action="{{route('user.store')}}" enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-2">
                        <div>
                            <label class="block text-sm">Username</label>
                            <input type="text" class="w-full border rounded px-3 py-2" name="username">
                        </div>
                        <div>
                            <label class="block text-sm">Name</label>
                            <input type="text" class="w-full border rounded px-3 py-2" name="name">
                        </div>
                        <div>
                            <label class="block text-sm">UID</label>
                            <input type="text" class="w-full border rounded px-3 py-2" name="uid">
                        </div>
                        <div>
                            <label class="block text-sm">Department</label>
                            <select class="w-full border rounded px-3 py-2" name="department">
                                <option value="">Select</option>
                                @foreach ($departments as $department)
                                <option value="{{$department['id']}}">{{$department['name']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm">Email</label>
                            <input type="email" class="w-full border rounded px-3 py-2" name="email">
                        </div>
                        <div>
                            <label class="block text-sm">Role</label>
                            <select class="w-full border rounded px-3 py-2" name="role">
                                <option value="">Select</option>
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm">Status</label>
                            <select class="w-full border rounded px-3 py-2" name="status">
                                <option value="">Select</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm">Foto</label>
                            <input type="file" class="w-full border rounded px-3 py-2" multiple name="foto[]">
                        </div>
                        <div>
                            <label class="block text-sm">Signature</label>
                            <input type="file" class="w-full border rounded px-3 py-2" name="ttd">
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
                <h2 class="text-lg font-bold mb-4">Edit User</h2>
                <form id="editForm" method="post" enctype="multipart/form-data" action="{{ route('user.update', ':id') }}">
                    @csrf

                    <div class="space-y-2">
                        <div>
                            <label class="block text-sm">Username</label>
                            <input type="text" class="w-full border rounded px-3 py-2" name="username" id="editUsername">
                        </div>
                        <div>
                            <label class="block text-sm">Name</label>
                            <input type="text" class="w-full border rounded px-3 py-2" name="name" id="editName">
                        </div>
                        <div>
                            <label class="block text-sm">UID</label>
                            <input type="text" class="w-full border rounded px-3 py-2" name="uid" id="editUid">
                        </div>
                        <div>
                            <label class="block text-sm">Department</label>
                            <select class="w-full border rounded px-3 py-2" name="department" id="editDepartment">
                                <option value="">Select</option>
                                @foreach ($departments as $department)
                                <option value="{{$department['id']}}">{{$department['name']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm">Email</label>
                            <input type="email" class="w-full border rounded px-3 py-2" name="email" id="editEmail">
                        </div>
                        <div>
                            <label class="block text-sm">Password</label>
                            <input type="password" class="w-full border rounded px-3 py-2" name="password" id="editPassword">
                        </div>
                        <div>
                            <label class="block text-sm">Role</label>
                            <select class="w-full border rounded px-3 py-2" name="role" id="editRole">
                                <option value="">Select</option>
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm">Status</label>
                            <select class="w-full border rounded px-3 py-2" name="status" id="editStatus">
                                <option value="">Select</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm">Foto</label>
                            <input type="file" class="w-full border rounded px-3 py-2" multiple name="foto[]" id="editFoto">
                        </div>
                        <div>
                            <label class="block text-sm">Tanda Tangan</label>
                            <input type="file" class="w-full border rounded px-3 py-2" name="ttd" id="editTtd">
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
            {{ $users->links() }}
        </div>
        <!-- <pre>{{ get_class($users) }}</pre> -->

    </section>

    <script>
        document.getElementById('editForm').addEventListener('submit', function() {
            document.getElementById('loadingIndicator').classList.remove('hidden');
        });
        document.getElementById('addForm').addEventListener('submit', function() {
            document.getElementById('loadingIndicator').classList.remove('hidden');
        });
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[data-user-id]').forEach(button => {
                button.addEventListener('click', function() {
                    console.log(this.getAttribute('data-user-id'));
                    const id = this.getAttribute('data-user-id');
                    const uid = this.getAttribute('data-user-uid');
                    const name = this.getAttribute('data-user-name');
                    const username = this.getAttribute('data-user-username');
                    const role = this.getAttribute('data-user-role');
                    const status = this.getAttribute('data-user-status');
                    const image = this.getAttribute('data-user-image');
                    const department = this.getAttribute('data-user-department');
                    const signature = this.getAttribute('data-user-signature');
                    const email = this.getAttribute('data-user-email');
                    console.log('User ID:', id);
                    console.log('Name:', name);
                    console.log('Username:', username);
                    console.log('Email:', email);
                    console.log('Role:', role);
                    // console.log('Tahun Masuk:', tahun_masuk);

                    const updateForm = document.getElementById('editForm');
                    if (updateForm) {
                        const url = "{{ route('user.update', ':id') }}".replace(':id', id);
                        updateForm.action = url;
                        console.log(url);

                        document.getElementById('editName').value = name;
                        document.getElementById('editUsername').value = username;
                        document.getElementById('editUid').value = uid;
                        document.getElementById('editEmail').value = email

                        const departmentSelect = document.getElementById('editDepartment');
                        if (departmentSelect) {
                            Array.from(departmentSelect.options).forEach(option => {
                                option.selected = option.value === department;
                            });
                        }
                        const statusSelect = document.getElementById('editStatus');
                        if (statusSelect) {
                            Array.from(statusSelect.options).forEach(option => {
                                option.selected = option.value === status;
                            });
                        }
                        const roleSelect = document.getElementById('editRole');
                        if (roleSelect) {
                            Array.from(roleSelect.options).forEach(option => {
                                option.selected = option.value === role;
                            });
                        }
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