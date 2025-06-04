<x-layout title="Presensi Dinas">
    @section('title', 'Master User')
    <x-slot:title>{{$title}}</x-slot:title>
    <section class="p-4">
        <form id="presensiForm">
            <div class="mb-4">
                <label for="jenisPresensi" class="block font-medium">Jenis Presensi</label>
                <select id="jenisPresensi" name="jenis" class="w-full border rounded p-2" required>
                    <option value="ipg">IPG Group</option>
                    <option value="luar">Luar</option>
                </select>
            </div>
            <div class="mb-4" id="kantorWrapper">
                <label for="kantor" class="block font-medium">Pilih Kantor</label>
                <select id="kantor" name="kantor_id" class=" w-full border rounded p-2" required>
                    <option value="">-- Pilih Kantor --</option>
                    @foreach ($kantors as $kantor)
                    <option
                        value="{{ $kantor->id }}"
                        data-lat="{{ $kantor->lat }}"
                        data-lng="{{ $kantor->long }}">
                        {{ $kantor->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <button id="ambilFotoBtn" type="button" onclick="cekLokasi()" class="bg-blue-600 text-white px-4 py-2 rounded">
                Cek Lokasi & Verifikasi
            </button>

            <div id="loadingSpinner" class="mt-4 hidden flex items-center space-x-2">
                <svg class="animate-spin h-6 w-6 text-blue-600" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10"
                        stroke="currentColor" stroke-width="4" fill="none"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span class="text-blue-600">Memproses wajah...</span>
            </div>
        </form>

        <div id="result" class="mt-4 font-semibold"></div>
        <div id="cameraSection" class="mt-4 hidden">
            <video id="video" autoplay class="w-64 h-48 border rounded"></video>
            <canvas id="canvas" class="hidden"></canvas>
            <br>
            <button type="button" onclick="takePhoto()" class="mt-2 bg-green-600 text-white px-4 py-2 rounded">Ambil Foto</button>
        </div>
    </section>

    <script>
        let video = null;
        let attempts = 0;

        const btn = document.getElementById('ambilFotoBtn');
        const spinner = document.getElementById('loadingSpinner');

        const jenisSelect = document.getElementById('jenisPresensi');
        const kantorWrapper = document.getElementById('kantorWrapper');

        jenisSelect.addEventListener('change', function() {
            if (this.value === 'luar') {
                kantorWrapper.classList.add('hidden');
                document.getElementById('kantor').removeAttribute('required');
            } else {
                kantorWrapper.classList.remove('hidden');
                document.getElementById('kantor').setAttribute('required', 'required');
            }
        });

        jenisSelect.dispatchEvent(new Event('change'));

        function startCamera() {
            video = document.getElementById('video');
            navigator.mediaDevices.getUserMedia({
                    video: true
                })
                .then((stream) => {
                    video.srcObject = stream;
                })
                .catch((err) => {
                    alert("Gagal mengakses kamera: " + err);
                });
        }

        function takePhoto(forceSave = false) {
            const canvas = document.getElementById('canvas');
            const context = canvas.getContext('2d');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            spinner.classList.remove('hidden');

            // Convert to base64
            const imageData = canvas.toDataURL('image/png');
            const jenisPresensi = document.getElementById('jenisPresensi').value;
            const kantorId = jenisPresensi === 'ipg' ? document.getElementById('kantor').value : null;
            const latUser = window.currentLat;
            const lngUser = window.currentLng;

            // Kirim ke controller Laravel
            fetch("{{ route('presensi.verifikasi') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        image: imageData,
                        jenis: jenisPresensi,
                        kantor_id: kantorId,
                        lat: latUser,
                        lng: lngUser,
                        attempt: attempts + 1 // ⬅️ penting!
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert("Presensi berhasil!");
                        window.location.reload();
                    } else {
                        // attempts++;
                        if (attempts < 3 && !forceSave) {
                            alert("Wajah tidak cocok. Coba lagi (" + attempts + "/3)");
                            takePhoto(); // Ulangi foto
                        } else {
                            alert("Wajah tidak dikenali. Presensi tetap disimpan sebagai gagal.");
                            forceSave = true;

                            // Kirim ulang dengan force attempt 3
                            fetch("{{ route('presensi.verifikasi') }}", {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                                    },
                                    body: JSON.stringify({
                                        image: imageData,
                                        jenis: jenisPresensi,
                                        kantor_id: kantorId,
                                        lat: latUser,
                                        lng: lngUser,
                                        attempt: 3 // ⬅️ Paksa attempt terakhir
                                    })
                                })
                                .then(res => res.json())
                                .then(data => {
                                    if (data.success) {
                                        alert("Presensi disimpan sebagai gagal.");
                                        window.location.reload();
                                    } else {
                                        alert("Gagal menyimpan presensi.");
                                    }
                                    spinner.classList.add('hidden');
                                })
                                .catch(error => {
                                    console.error(error);
                                    alert("Terjadi kesalahan saat upload foto gagal.");
                                    spinner.classList.add('hidden');
                                });
                        }
                    }
                    spinner.classList.add('hidden');
                })
                .catch(error => {
                    console.error(error);
                    alert("Terjadi kesalahan saat mengirim foto.");
                    spinner.classList.add('hidden');
                });
        }

        function getDistance(lat1, lon1, lat2, lon2) {
            const R = 6371e3; // radius Bumi (meter)
            const toRad = deg => deg * Math.PI / 180;
            const dLat = toRad(lat2 - lat1);
            const dLon = toRad(lon2 - lon1);
            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c;
        }

        function cekLokasi() {
            const jenisPresensi = document.getElementById('jenisPresensi').value;
            const resultDiv = document.getElementById('result');

            if (jenisPresensi === 'ipg') {
                const select = document.getElementById('kantor');
                const selected = select.options[select.selectedIndex];

                if (!selected.value) {
                    alert('Silakan pilih kantor terlebih dahulu.');
                    return;
                }

                const latKantor = parseFloat(selected.dataset.lat);
                const lngKantor = parseFloat(selected.dataset.lng);

                if (!navigator.geolocation) {
                    alert("Geolocation tidak didukung oleh browser ini.");
                    return;
                }

                navigator.geolocation.getCurrentPosition(function(position) {
                    const latUser = position.coords.latitude;
                    const lngUser = position.coords.longitude;
                    window.currentLat = latUser;
                    window.currentLng = lngUser;
                    const accuracy = position.coords.accuracy;
                    const distance = getDistance(latKantor, lngKantor, latUser, lngUser);

                    if (accuracy > 150) {
                        resultDiv.innerHTML = `<span class="text-yellow-600">Lokasi tidak akurat (±${Math.round(accuracy)} m). Silakan aktifkan GPS dan coba lagi.</span>`;
                        return;
                    }

                    if (distance <= 100) {
                        resultDiv.innerHTML = `<span class="text-green-600">Anda berada di sekitar kantor. Silakan lanjut verifikasi wajah.</span>`;
                        document.getElementById('cameraSection').classList.remove('hidden');
                        startCamera();
                    } else {
                        resultDiv.innerHTML = `<span class="text-red-600">Anda terlalu jauh dari kantor (${Math.round(distance)} m).</span>`;
                    }
                }, function() {
                    alert("Gagal mendapatkan lokasi.");
                });

            } else if (jenisPresensi === 'luar') {
                // Tidak perlu cek kantor, langsung ambil lokasi dan verifikasi
                if (!navigator.geolocation) {
                    alert("Geolocation tidak didukung oleh browser ini.");
                    return;
                }

                navigator.geolocation.getCurrentPosition(function(position) {
                    const latUser = position.coords.latitude;
                    const lngUser = position.coords.longitude;
                    window.currentLat = position.coords.latitude;
                    window.currentLng = position.coords.longitude;
                    resultDiv.innerHTML = `<span class="text-green-600">Lokasi didapatkan. Silakan lanjut verifikasi wajah.</span>`;
                    document.getElementById('cameraSection').classList.remove('hidden');
                    startCamera();
                }, function() {
                    alert("Gagal mendapatkan lokasi.");
                });

            } else {
                alert("Silakan pilih jenis presensi.");
            }
        }
    </script>
</x-layout>