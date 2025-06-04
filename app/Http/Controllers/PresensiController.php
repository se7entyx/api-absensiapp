<?php

namespace App\Http\Controllers;

use App\Exports\PresensiExport;
use App\Models\Kantor;
use App\Models\Presensi;
use App\Models\User;
use Cloudinary\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class PresensiController extends Controller
{
    public function dinas()
    {
        $kantors = Kantor::orderBy('name')->get();
        return view('presensi-dinas', ['title' => 'Presensi Dinas', 'kantors' => $kantors]);
    }
    public function export(Request $request)
    {
        $filters = $request->only(['search', 'start_date', 'end_date', 'type', 'kantor_id']);
        $mode = $request->get('mode', 'rekap');
        if ($mode === 'my') {
            $filters['user_id'] = Auth::id(); // hanya presensi milik sendiri
        }
        return Excel::download(new PresensiExport($filters), 'data-presensi.xlsx');
    }
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:success,failed'
        ]);
        $presensi = Presensi::findOrFail($id);
        $presensi->status = $validated['status'];
        if ($presensi->image) {
            Storage::delete('public/' . $presensi->image);
            $presensi->image = null;
        }
        $presensi->save();
        return redirect()->route('presensi.rekap')->with('success', 'Presensi updated successfully.');
    }
    public function my()
    {
        $presensis = Presensi::with('user')->where('user_id', Auth::id())->filter(request(['start_date', 'end_date', 'type', 'kantor_id']))->sortable()->latest()->paginate(10);
        $kantors = Kantor::orderBy('name')->get();
        return view('presensi-my', ['title' => 'My Presensi', 'presensis' => $presensis, 'kantors' => $kantors]);
    }
    public function rekap()
    {
        $presensis = Presensi::with('user')->filter(request(['search', 'start_date', 'end_date', 'type', 'kantor_id']))->sortable()->latest()->paginate(10);
        $kantors = Kantor::orderBy('name')->get();
        return view('presensi-rekap', ['title' => 'Presensi - Rekap', 'presensis' => $presensis, 'kantors' => $kantors]);
    }

    public function verifikasi(Request $request)
    {
        $request->validate([
            'image' => 'required|string',
            // 'id' => 'required|integer',
            'kantor_id' => 'nullable',
            'jenis' => 'nullable|string',
            'lat' => 'nullable',
            'lng' => 'nullable',
            'attempt' => 'required|integer' // kirim attempt ke-berapa
        ]);

        $user = Auth::user();
        $base64Image = $request->image;
        $attempt = $request->attempt;

        // Set nama folder dataset berdasarkan username
        $cloudinaryPath = $user->username;

        if (!$user->image) {
            return response()->json(['success' => false, 'message' => 'Dataset belum tersedia untuk user ini.'], 404);
        }

        // Coba verifikasi dengan Flask
        $response = Http::post("https://fluent-intensely-foal.ngrok-free.app/verify", [
            'image' => $base64Image,
            'dataset_path' => $cloudinaryPath,
        ]);

        if (!$response->successful()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghubungi server verifikasi.',
                'response' => $response->body()
            ], $response->status());
        }

        $result = $response->json()['result'] ?? false;

        if ($result === true) {
            // Presensi success
            $today = now();
            $presensi = Presensi::firstOrNew([
                'user_id' => $user->id,
                'created_at' => $today->toDateString()
            ]);

            $presensi->type = 'dinas-' . $request->jenis;
            $presensi->status = 'success';
            $presensi->kantor_id = $request->kantor_id;
            $presensi->lat = $request->lat;
            $presensi->long = $request->lng;
            $presensi->save();

            return response()->json([
                'success' => true,
                'message' => 'Presensi berhasil',
                'presensi' => $presensi
            ]);
        }

        // Gagal 3 kali
        if ($attempt >= 3) {
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));
            $tempPath = storage_path('app/temp_failed_' . uniqid() . '.jpg');
            file_put_contents($tempPath, $imageData);

            // Upload ke Cloudinary
            try {
                $cloudinary = new Cloudinary([
                    'cloud' => [
                        'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                        'api_key'    => env('CLOUDINARY_API_KEY'),
                        'api_secret' => env('CLOUDINARY_API_SECRET'),
                    ],
                    'url' => ['secure' => true]
                ]);

                $uploadedFile = $cloudinary->uploadApi()->upload($tempPath, [
                    'folder' => 'failed',
                    'public_id' => $user->username . '_' . now()->format('Ymd_His')
                ]);

                $imageUrl = $uploadedFile['secure_url'];
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Upload Cloudinary gagal.',
                    'error' => $e->getMessage()
                ], 500);
            } finally {
                if (file_exists($tempPath)) {
                    unlink($tempPath);
                }
            }

            // Simpan presensi failed
            $presensi = Presensi::firstOrNew([
                'user_id' => $user->id,
                'created_at' => now()->toDateString()
            ]);
            $presensi->type = 'dinas-' . $request->jenis;
            $presensi->status = 'failed';
            $presensi->image = $imageUrl;
            $presensi->kantor_id = $request->kantor_id;
            $presensi->lat = $request->lat;
            $presensi->long = $request->lng;
            $presensi->save();

            return response()->json([
                'success' => true,
                'message' => 'Wajah gagal diverifikasi setelah 3x. Presensi disimpan sebagai gagal.',
                'presensi' => $presensi
            ]);
        }

        // Belum 3x gagal
        return response()->json([
            'success' => false,
            'message' => 'Verifikasi wajah gagal. Coba lagi.',
            'recognized' => false
        ]);
    }


    // public function verifikasi(Request $request)
    // {
    //     $image = $request->input('image');
    //     $kantorId = $request->input('kantor_id');
    //     $jenis = $request->input('jenis');
    //     $lat = $request->input('lat');
    //     $lng = $request->input('lng');
    //     $forceSave = $request->input('force_save', false);

    //     $user = Auth::user();

    //     $today = now();
    //     $presensi = Presensi::where('user_id', $user->id)
    //         ->whereDate('created_at', $today)
    //         ->first();

    //     if ($presensi) {
    //         return response()->json(['success' => false, 'message' => 'Presensi sudah dibuat']);
    //     }

    //     if (!$image || !$user) {
    //         return response()->json(['success' => false, 'message' => 'Data tidak lengkap']);
    //     }

    //     // Tentukan path
    //     $foto = $user->image;
    //     $datasetPath = storage_path('app/public/' . $foto);
    //     $fileName = 'temp_' . time() . '.jpg';
    //     $testImagePath = storage_path('app/public/tmp/' . $fileName);

    //     // Pastikan folder temp ada
    //     if (!file_exists(dirname($testImagePath))) {
    //         mkdir(dirname($testImagePath), 0755, true);
    //     }

    //     // Decode base64 dan simpan sebagai JPG
    //     $image = str_replace('data:image/png;base64,', '', $image);
    //     $image = str_replace('data:image/jpeg;base64,', '', $image);
    //     $image = str_replace(' ', '+', $image);
    //     file_put_contents($testImagePath, base64_decode($image));

    //     // Jalankan Python script
    //     $scriptPath = base_path('scripts/face_recognition.py');
    //     $escapedDataset = escapeshellarg($datasetPath);
    //     $escapedImage = escapeshellarg($testImagePath);
    //     $command = "python $scriptPath $escapedDataset $escapedImage";

    //     $output = trim(shell_exec($command));
    //     $lines = explode("\n", trim($output));
    //     $lastLine = end($lines);
    //     $recognized = ($lastLine === "true");

    //     if (!$recognized && !$forceSave) {
    //         // Belum mau simpan, beri kesempatan coba lagi
    //         return response()->json(['success' => false, 'message' => 'Wajah tidak cocok', 'output' => $output]);
    //     }

    //     $presensi = new Presensi();
    //     $presensi->user_id = $user->id;
    //     $presensi->type = "dinas-$jenis";
    //     $presensi->status = $recognized ? 'success' : 'failed';
    //     $presensi->kantor_id = $kantorId;
    //     $presensi->lat = $lat;
    //     $presensi->long = $lng;

    //     if (!$recognized) {
    //         $failPath = 'failed/' . $user->username . '_' . time() . '.jpg';
    //         $storageFailPath = storage_path('app/public/' . $failPath);

    //         if (!file_exists(dirname($storageFailPath))) {
    //             mkdir(dirname($storageFailPath), 0755, true);
    //         }
    //         copy($testImagePath, $storageFailPath);
    //         $presensi->image = $failPath;
    //     }

    //     $presensi->save();

    //     // Hapus gambar sementara
    //     if (file_exists($testImagePath)) {
    //         unlink($testImagePath);
    //         Storage::delete('public/tmp/' . $fileName);
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'output' => $output,
    //         'recognized' => $recognized
    //     ]);
    // }
}
