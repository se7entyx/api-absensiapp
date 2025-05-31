<x-layout title="Edit Izin">
    @section('title', 'Edit Izin')
    <x-slot:title>{{$title}}</x-slot:title>
    <section class="bg-gray-100  w-full px-4 py-4 sm:px-6">
        @if (session('success'))
        <div id="alert" class="relative flex items-center p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50  transition-opacity duration-500 ease-in-out opacity-100" role="alert">
            <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
            </svg>
            <span class="sr-only">Info</span>
            <div>
                <span class="font-medium">Pengajuan Izin berhasil dibuat!</span> Silahkan cek pada menu On Going.
            </div>
        </div>
        @endif

        @if (session('unfinished'))
        <div id="alert" class="relative flex items-center p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50  transition-opacity duration-500 ease-in-out opacity-100" role="alert">
            <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
            </svg>
            <span class="sr-only">Info</span>
            <div>
                <span class="font-medium">Pengajuan gagal dibuat!</span> Silahkan assign manager untuk user terlebih dahulu
            </div>
        </div>
        @endif

        <form action="{{ route('izin.update',$izin->id)}}" method="post" class="xl:px-32">
            @csrf
            @method('PUT')
            <div class="grid gap-6 mb-6 md:grid-cols-2">
                <div>
                    <label for="name" class="block mb-2 text-sm font-medium text-grayxl900  " disabled>Nama</label>
                    <input type="text" id="name" class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 cursor-not-allowed" disabled value="{{ $izin->user->name }}" />
                </div>
                <div>
                    <label for="department" class="block mb-2 text-sm font-medium text-gray-900 ">Department</label>
                    <input type="text" id="department" class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5  cursor-not-allowed" disabled value="{{ $izin->user->department->name }}" />
                </div>
            </div>
            <div class="mb-6">
                <label for="keterangan" class="block mb-2 text-sm font-medium text-gray-900 ">Keterangan Izin</label>
                <textarea id="keterangan" rows="4" name="keterangan" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500" placeholder="Masukan keterangan" required>{{$izin->keterangan}}</textarea>
            </div>
            <div class="mb-6">
                <label for="waktu_keluar" class="block mb-2 text-sm font-medium text-gray-900">Waktu Keluar</label>
                <input type="datetime-local" id="waktu_keluar" name="waktu_keluar" required
                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" value="{{ old('waktu_keluar', isset($izin) && $izin->waktu_keluar ? \Carbon\Carbon::parse($izin->waktu_keluar)->format('Y-m-d\TH:i') : '') }}">
            </div>

            <!-- Kembali -->
            <div class="mb-6">
                <label class="block mb-2 text-sm font-medium text-gray-900">Akan Kembali?</label>
                <div class="flex items-center space-x-4">
                    <label class="flex items-center">
                        <input type="radio" name="kembali" value="ya" class="kembali-radio text-blue-600" {{ old('kembali', isset($izin) ? $izin->kembali : '') === 'ya' ? 'checked' : '' }} required>
                        <span class="ml-2 text-gray-900">Ya</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="kembali" value="tidak" class="kembali-radio text-blue-600" {{ old('kembali', isset($izin) ? $izin->kembali : '') === 'tidak' ? 'checked' : '' }}>
                        <span class="ml-2 text-gray-900">Tidak</span>
                    </label>
                </div>
            </div>

            <!-- Waktu Kembali -->
            <div id="waktu_kembali_container" class="mb-6 {{ (old('kembali', $izin->kembali ?? '') !== 'ya') ? 'hidden' : '' }}">
                <label for="waktu_kembali" class="block mb-2 text-sm font-medium text-gray-900">Perkiraan Waktu Kembali</label>
                <input type="datetime-local" id="waktu_kembali" name="waktu_kembali"
                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" value="{{ old('waktu_kembali', isset($izin) && $izin->waktu_kembali ? \Carbon\Carbon::parse($izin->waktu_kembali)->format('Y-m-d\TH:i') : '') }}">
            </div>
            <div class="mb-4">
                <label for="revisi" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Revisi</label>
                <textarea name="revisi" id="revisi" rows="4" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 resize-none cursor-not-allowed" readonly>{{ $izin->revisi ?? '' }}</textarea>
            </div>
            <div class="flex justify-end">
                <button type="button" data-modal-target="popup-modal" data-modal-toggle="popup-modal" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center">Submit</button>
            </div>

            <div id="popup-modal" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full bg-black bg-opacity-50">
                <div class="relative p-4 w-full max-w-md max-h-full">
                    <div class="relative bg-white rounded-lg shadow ">
                        <button type="button" class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center " data-modal-hide="popup-modal">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                        <div class="p-4 md:p-5 text-center">
                            <svg class="mx-auto mb-4 text-gray-400 w-12 h-12" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            <h3 class="mb-5 text-lg font-normal text-gray-500 ">Apakah data yang diisi sudah benar?</h3>
                            <button data-modal-hide="popup-modal" type="button" class="py-2.5 px-5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-red-700 focus:z-10 focus:ring-4 focus:ring-gray-100 ">No, cancel</button>
                            <button data-modal-hide="popup-modal" type="submit" class="ms-3 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300  font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">
                                Yes, I'm sure
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
</x-layout>

<script>
    document.querySelectorAll('.kembali-radio').forEach(radio => {
        radio.addEventListener('change', function() {
            const container = document.getElementById('waktu_kembali_container');
            if (this.value === 'ya') {
                container.classList.remove('hidden');
            } else {
                container.classList.add('hidden');
            }
        });
    });
    setTimeout(() => {
        const alertElement = document.getElementById('alert');
        if (alertElement) {
            alertElement.classList.add('opacity-0');

            // Remove the alert after the transition (500ms)
            setTimeout(() => {
                alertElement.remove();
            }, 500); // Duration matches the CSS transition
        }
    }, 5000); // 5 seconds delay
</script>