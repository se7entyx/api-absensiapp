<x-layout title="On Going - Cuti">
    @section('title', 'On Going - Cuti')
    <x-slot:title>{{$title}} </x-slot:title>
    <section class="w-full relative px-4 py-4 sm:px-6 bg-gray-100">
        @if ($errors->any())
        <div id="error-alert" class="relative flex w-full items-center p-4 mb-4 text-red-800 border border-red-300 bg-red-50 rounded-md shadow-sm" role="alert">
            <div id="error-progress-bar" class="absolute top-0 left-0 h-1 bg-red-500 rounded-t-md" style="width: 100%; transition: width 5s linear;"></div>

            <svg class="flex-shrink-0 w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
            </svg>

            <div class="ml-3 text-sm font-medium">
                Please fix the following errors:
                <ul class="mt-1">
                    @foreach ($errors->all() as $error)
                    <li>- {{ $error }}</li>
                    @endforeach
                </ul>
            </div>

            <button type="button" id="close-error-alert" class="absolute top-2 right-2 bg-red-50 text-red-500 rounded-lg p-1.5 hover:bg-red-200 " aria-label="Close">
                <span class="sr-only">Close</span>
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
        @endif

        <!-- Success Alert -->
        @if (session('success'))
        <div id="success-alert" class="relative flex w-full items-center p-4 mb-4 text-green-800 border border-green-300 bg-green-50  rounded-md shadow-sm" role="alert">
            <div id="progress-bar" class="absolute top-0 left-0 h-1 bg-green-500 rounded-t-md" style="width: 100%; transition: width 5s linear;"></div>

            <svg class="flex-shrink-0 w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
            </svg>

            <div class="ml-3 text-sm font-medium">
                {{ session('success') }}
            </div>

            <button type="button" id="close-alert" class="absolute top-2 right-2 bg-green-50 text-green-500 rounded-lg p-1.5 hover:bg-green-200 " aria-label="Close">
                <span class="sr-only">Close</span>
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
        @endif
        <div class="overflow-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 ">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 ">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            No
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Nama
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Tanggal dibuat
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Start Cuti
                        </th>
                        <th scope="col" class="px-6 py-3">
                            End Cuti
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Keterangan
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Jumlah Hari
                        </th>
                        
                        <th scope="col" class="px-6 py-3">
                            Aksi
                        </th>
                    </tr>
                </thead>
                @foreach ($cutis as $cuti)
                <tbody>
                    <tr class="bg-white border-b hover:bg-gray-50 ">
                        <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                            {{ ($cutis->currentPage() - 1) * $cutis->perPage() + $loop->iteration }}
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                            {{ $cuti->user->name }}
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                            {{ $cuti->created_at->format('d M Y, H:m') }}
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                            {{ \Carbon\Carbon::parse($cuti->start_date)->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                            {{ \Carbon\Carbon::parse($cuti->end_date)->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                            {{ $cuti->keterangan }}
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                            {{ $cuti->jumlah_hari }}
                        </td>
                        <td class="px-6 py-4 flex space-x-3">
                            <button data-modal-target="timeline-modal" data-modal-toggle="timeline-modal" data-original-icon data-status="{{ $cuti->status }}" data-create="{{ $cuti->created_at }}" data-keterangan="{{$cuti->keterangan}}" data-keluar="{{$cuti->start_date}}" data-waktu-kembali="{{$cuti->end_date}}" class="block text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center " type="button">
                                Status
                            </button>
                            <a href="{{route('cuti.print',$cuti->id)}}" target="_blank" rel="noopener noreferrer">
                                <button class="block text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center " type="button">
                                    Details
                                </button>
                            </a>
                            @if ($cuti->status === 'acc0' && $cuti->user_id === Auth::id())
                            <a href="{{route('cuti.edit',$cuti->id)}}">
                                <button class="block text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center " type="button">
                                    Edit
                                </button>
                            </a>
                            @endif
                            @if ($cuti->status === 'acc-1' && $cuti->user_id === Auth::id())
                            <a href="{{route('cuti.edit',$cuti->id)}}">
                                <button class="block text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center " type="button">
                                    Edit
                                </button>
                            </a>
                            @endif
                            @if ($cuti->status === 'acc0' && Auth::id() === $cuti->user->department->user_id )
                            <a href="{{route('cuti.approveIndex',$cuti->id)}}">
                                <button class="block text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center " type="button">
                                    Approve
                                </button>
                            </a>
                            @endif
                            @if (($cuti->status === 'acc0' || $cuti->status === 'acc-1') && $cuti->user_id === Auth::id())
                            <button class="block text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center " type="button" onclick="openModal()" data-modal-target="deleteModal" data-modal-toggle="deleteModal"
                                data-delete-id="{{ $cuti->id }}">
                                Delete
                            </button>
                            @endif
                        </td>
                    </tr>
                </tbody>
                @endforeach
            </table>
        </div>

        <div id="timeline-modal" tabindex="-1" aria-hidden="true" class="bg-black bg-opacity-50 hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative p-4 w-full max-w-md max-h-full">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow ">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t ">
                        <h3 class="text-lg font-semibold text-gray-900 ">
                            Status
                        </h3>
                        <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm h-8 w-8 ms-auto inline-flex justify-center items-center " data-modal-toggle="timeline-modal">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>
                    <!-- Modal body -->
                    <div class="p-7 md:p-10">
                        <ol class="relative text-gray-500 border-s border-gray-200 ">
                            <li class="mb-10 ms-7 step">
                                <span class="absolute flex items-center justify-center w-8 h-8 bg-green-200 rounded-full -start-4 ring-4 ring-white ">
                                    <svg class="w-3.5 h-3.5 text-green-500 " aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 12">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5.917 5.724 10.5 15 1.5" />
                                    </svg>
                                </span>
                                <h3 class="font-medium leading-tight">Data berhasil di upload</h3>
                                <p class="text-sm">Data telah di upload pada tanggal</p>
                            </li>
                            <li class="mb-10 ms-7 step">
                                <span class="absolute flex items-center justify-center w-8 h-8 bg-gray-100 rounded-full -start-4 ring-4 ring-white ">
                                    <svg class="w-3.5 h-3.5 text-gray-500 " aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 16">
                                        <path d="M18 0H2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2ZM6.5 3a2.5 2.5 0 1 1 0 5 2.5 2.5 0 0 1 0-5ZM3.014 13.021l.157-.625A3.427 3.427 0 0 1 6.5 9.571a3.426 3.426 0 0 1 3.322 2.805l.159.622-6.967.023ZM16 12h-3a1 1 0 0 1 0-2h3a1 1 0 0 1 0 2Zm0-3h-3a1 1 0 1 1 0-2h3a1 1 0 1 1 0 2Zm0-3h-3a1 1 0 1 1 0-2h3a1 1 0 1 1 0 2Z" />
                                    </svg>
                                </span>
                                <h3 class="font-medium leading-tight">Menunggu konfirmasi dari Pihak Purchasing</h3>
                                <p class="text-sm">Data sedang diproses</p>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div id="deletemodal" tabindex="-1" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="relative p-4 w-full max-w-md max-h-full">
                <div class="relative bg-white rounded-lg shadow-lg  transition-transform transform duration-300 scale-100">
                    <button type="button" class="absolute top-3 right-3 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center" onclick="closeModal()">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                    <div class="p-6 text-center">
                        <h3 class="mb-4 text-lg font-semibold text-gray-800 ">Confirmation</h3>
                        <p class="mb-5 text-md text-gray-600 ">Are you sure you want to delete this?</p>
                        <form method="POST" action="#" id="deleteform" class="mt-4">
                            @csrf
                            @method('DELETE')
                            <div class="flex justify-center space-x-4">
                                <button type="button" onclick="closeModal()" class="px-5 py-2.5 text-sm font-medium text-gray-900 bg-gray-200 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-4 focus:ring-gray-200 ">Cancel</button>
                                <button type="submit" class="text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-300 rounded-lg text-sm px-5 py-2.5 transition duration-200">Confirm</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-4">
            {{ $cutis->links() }}
        </div>
    </section>
</x-layout>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('[data-delete-id]').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-delete-id');
                console.log(id);
                const deleteForm = document.getElementById('deleteform');
                if (deleteForm) {
                    deleteForm.action = "cuti.destroy".replace(':id', id);
                }
            });
        });
        // For the error alert
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

    function openModal() {
        document.getElementById('deletemodal').classList.remove('hidden');
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
    }

    function closeModal() {
        document.getElementById('deletemodal').classList.add('hidden');
        document.body.style.overflow = ''; // Restore original overflow
    }
    document.querySelectorAll('button[data-modal-target="timeline-modal"]').forEach(button => {
        button.addEventListener('click', function() {
            const status = this.getAttribute('data-status');
            let created_at = formatDate(this.getAttribute('data-create'));
            let purchasing_confirm_date = formatDate(this.getAttribute('data-purchasing-it'));
            let user_confirm_date = formatDate(this.getAttribute('data-using-date'));
            let manager_confirm_date = formatDate(this.getAttribute('data-confirm-manager'));

            document.querySelectorAll('.step').forEach(step => {
                step.querySelector('span').className = 'absolute flex items-center justify-center w-8 h-8 bg-gray-100 rounded-full -start-4 ring-4 ring-white dark:ring-gray-900 dark:bg-gray-700';
                step.querySelector('svg').className = 'w-3.5 h-3.5 text-gray-500 dark:text-gray-400';
            });

            if (status === 'acc0') {
                setStepComplete(0, 'Data berhasil di upload', `Data dibuat pada ${created_at}`);
                setStepInProgress1(1, 'Menunggu konfirmasi dari Manager', 'Konfirmasi akan dikirimi ke email anda');
                // setDefault(3, 'Menunggu konfirmasi dari Manager', 'Data masih diproses');
            } else if (status === 'acc1') {
                setStepComplete(0, 'Data berhasil di upload', `Data dibuat pada ${created_at}`);
                setStepComplete(1, 'Konfirmasi dari manager selesai','Email telah dikirim ke HRD');
            } else if (status === 'acc-1') {
                setStepInProgress1(0, 'Terdapat kesalahan pada data Surat Cuti', 'Menunggu revisi data Surat Cuti');
                displayRedCross(1, 'Permintaan ditolak oleh Manager', `Silahkan menghubungi Manager`)
            }
        });
    });

    function formatDate(dateString) {
        const date = new Date(dateString);
        const options = {
            day: 'numeric',
            month: 'short',
            year: 'numeric'
        };

        const formattedDate = date.toLocaleDateString('en-GB', options); // '26 Sept 2024'
        const formattedTime = date.toLocaleTimeString('en-GB', {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false // 24-hour format
        }); // '14:30'

        return `${formattedDate} ${formattedTime}`; // '26 Sept 2024 14:30'
    }

    function setStepComplete(stepIndex, title, sub) {
        const step = document.querySelectorAll('.step')[stepIndex];
        step.querySelector('span').className = 'absolute flex items-center justify-center w-8 h-8 bg-green-200 rounded-full -start-4 ring-4 ring-white dark:ring-gray-900 dark:bg-green-900';
        step.querySelector('svg').className = 'w-3.5 h-3.5 text-green-500 dark:text-green-400';
        step.querySelector('span').innerHTML = `<svg class="w-3.5 h-3.5 text-green-500 dark:text-green-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 12">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5.917 5.724 10.5 15 1.5" />
                  </svg>`
        step.querySelector('h3').textContent = title;
        step.querySelector('p').innerHTML = sub;
    }

    function setStepInProgress1(stepIndex, title, sub) {
        const step = document.querySelectorAll('.step')[stepIndex];
        step.querySelector('span').className = 'absolute flex items-center justify-center w-8 h-8 bg-yellow-200 rounded-full -start-4 ring-4 ring-white dark:ring-gray-900 dark:bg-yellow-600';
        step.querySelector('svg').className = 'w-3.5 h-3.5 text-yellow-500 dark:text-yellow-400';
        step.querySelector('span').innerHTML = `<svg class="w-3.5 h-3.5 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 16">
                    <path d="M18 0H2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2ZM6.5 3a2.5 2.5 0 1 1 0 5 2.5 2.5 0 0 1 0-5ZM3.014 13.021l.157-.625A3.427 3.427 0 0 1 6.5 9.571a3.426 3.426 0 0 1 3.322 2.805l.159.622-6.967.023ZM16 12h-3a1 1 0 0 1 0-2h3a1 1 0 0 1 0 2Zm0-3h-3a1 1 0 1 1 0-2h3a1 1 0 1 1 0 2Zm0-3h-3a1 1 0 1 1 0-2h3a1 1 0 1 1 0 2Z" />
                  </svg>`
        step.querySelector('h3').textContent = title;
        step.querySelector('p').innerHTML = sub;
    }

    function setStepInProgress2(stepIndex, title, sub) {
        const step = document.querySelectorAll('.step')[stepIndex];
        step.querySelector('span').className = 'absolute flex items-center justify-center w-8 h-8 bg-yellow-200 rounded-full -start-4 ring-4 ring-white dark:ring-gray-900 dark:bg-yellow-600';
        step.querySelector('svg').className = 'w-3.5 h-3.5 text-yellow-500 dark:text-yellow-400';
        step.querySelector('span').innerHTML = `<svg class="w-3.5 h-3.5 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                    <path d="M16 1h-3.278A1.992 1.992 0 0 0 11 0H7a1.993 1.993 0 0 0-1.722 1H2a2 2 0 0 0-2 2v15a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2Zm-3 14H5a1 1 0 0 1 0-2h8a1 1 0 0 1 0 2Zm0-4H5a1 1 0 0 1 0-2h8a1 1 0 1 1 0 2Zm0-5H5a1 1 0 0 1 0-2h2V2h4v2h2a1 1 0 1 1 0 2Z" />
                  </svg>`
        step.querySelector('h3').textContent = title;
        step.querySelector('p').innerHTML = sub;
    }

    function displayRedCross(stepIndex, title, sub) {
        const step = document.querySelectorAll('.step')[stepIndex];
        step.querySelector('span').className = 'absolute flex items-center justify-center w-8 h-8 bg-red-200 rounded-full -start-4 ring-4 ring-white dark:ring-gray-900 dark:bg-red-900';
        step.querySelector('svg').className = 'w-3.5 h-3.5 text-red-500 dark:text-red-400';
        step.querySelector('svg').innerHTML = `<svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 16">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1l14 14m0-14L1 15" />
              </svg>`;
        step.querySelector('h3').textContent = title;
        step.querySelector('p').innerHTML = sub;
    }

    function setDefault(stepIndex, title, sub) {
        const step = document.querySelectorAll('.step')[stepIndex];
        step.querySelector('span').className = 'absolute flex items-center justify-center w-8 h-8 bg-gray-100 rounded-full -start-4 ring-4 ring-white dark:ring-gray-900 dark:bg-gray-700';
        step.querySelector('svg').className = 'w-3.5 h-3.5 text-yellow-500 dark:text-yellow-400';
        step.querySelector('span').innerHTML = `<svg class="w-3.5 h-3.5 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                    <path d="M16 1h-3.278A1.992 1.992 0 0 0 11 0H7a1.993 1.993 0 0 0-1.722 1H2a2 2 0 0 0-2 2v15a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2Zm-3 14H5a1 1 0 0 1 0-2h8a1 1 0 0 1 0 2Zm0-4H5a1 1 0 0 1 0-2h8a1 1 0 1 1 0 2Zm0-5H5a1 1 0 0 1 0-2h2V2h4v2h2a1 1 0 1 1 0 2Z" />
                  </svg>`
        step.querySelector('h3').textContent = title;
        step.querySelector('p').innerHTML = sub;
    }
</script>