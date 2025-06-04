<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use App\Models\User;
use Cloudinary\Cloudinary;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PresensiController extends Controller
{
    public function readUid($uid)
    {
        $user = User::with('department')->where('uid', $uid)->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User tidak ditemukan'], 404);
        }

        $presensi = Presensi::where('user_id', $user->id)
            ->whereDate('created_at', now())
            ->first();

        return response()->json([
            'uid' => $uid,
            'user' => $user,
            'presensi' => $presensi
        ]);
    }

    // public function verifyFace(Request $request)
    // {
    //     $request->validate([
    //         'uid' => 'required|string',
    //         'foto' => 'required|string' // base64
    //     ]);

    //     $uid = $request->uid;
    //     $base64Image = $request->foto;
    //     Log::info('UID: ' . $request->uid);
    //     Log::info('Foto: ' . substr($request->foto, 0, 100));

    //     // Cari user berdasarkan UID
    //     $user = User::where('uid', $uid)->first();
    //     if (!$user) {
    //         return response()->json(['success' => false, 'message' => 'User tidak ditemukan'], 404);
    //     }

    //     if (!$user->image || !file_exists(storage_path('app/public/' . $user->image))) {
    //         return response()->json(['success' => false, 'message' => 'Folder dataset tidak ditemukan'], 404);
    //     }

    //     // Simpan foto sementara di storage
    //     $filename = 'face_' . time() . '.jpg';
    //     $tempPath = storage_path("app/public/tmp/" . $filename);

    //     if (!file_exists(dirname($tempPath))) {
    //         mkdir(dirname($tempPath), 0755, true);
    //     }

    //     // $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));
    //     // file_put_contents($tempPath, $imageData);
    //     $image = str_replace('data:image/png;base64,', '', $base64Image);
    //     $image = str_replace('data:image/jpeg;base64,', '', $image);
    //     $image = str_replace(' ', '+', $image);
    //     file_put_contents($tempPath, base64_decode($image));

    //     // return response()->json([
    //     //     'success' => true,
    //     //     'message' => 'Presensi check-out berhasil5',
    //     // ]);

    //     // Panggil script Python
    //     $folderWajah = storage_path('app/public/' . $user->image); // contoh: foto/username
    //     // $cmd = 'python ' . base_path("scripts/face_recognition.py") . ' ' .
    //     //     escapeshellarg($folderWajah) . ' ' .
    //     //     escapeshellarg($tempPath);
    //     // $output = trim(shell_exec($cmd));

    //     $response = Http::post("https://5319-139-228-86-115.ngrok-free.app/verify", [
    //         'image' => $image,
    //         'dataset_path' => $user->image,
    //     ]);
    //     return response()->json($response->json(), $response->status());

    //     if ($response->successful()) {
    //         return response()->json($response->json());
    //     } else {
    //         return response()->json([
    //             'error' => 'Gagal menghubungi server verifikasi.',
    //             'detail' => $response->body()
    //         ], $response->status());
    //     }
    //     // $scriptPath = base_path('scripts/face_recognition.py');
    //     // $escapedDataset = escapeshellarg($folderWajah);
    //     // $escapedImage = escapeshellarg($tempPath);
    //     // $command = "python $scriptPath $escapedDataset $escapedImage";
    //     // Hapus file foto setelah digunakan
    //     // unlink($tempPath);

    //     $output = trim(shell_exec($command));
    //     $lines = explode("\n", trim($output));
    //     $lastLine = end($lines);
    //     $recognized = ($lastLine === "true");
    //     Log::info("Output Python: " . $output);
    //     if (file_exists($tempPath)) {
    //         unlink($tempPath);
    //     }
    //     // Cek hasil verifikasi
    //     if (!$recognized) {
    //         return response()->json(['success' => false, 'message' => 'Verifikasi wajah gagal', 'output' => $output], 403);
    //     }

    //     // Lanjutkan presensi
    //     $today = now();
    //     $presensi = Presensi::where('user_id', $user->id)
    //         ->whereDate('created_at', $today)
    //         ->first();

    //     if (!$presensi) {
    //         $presensi = new Presensi();
    //         $presensi->user_id = $user->id;
    //         $presensi->type = 'site';
    //         // $presensi->check_in = now();
    //         $presensi->status = $recognized ? 'success' : 'failed';
    //         $presensi->save();

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Presensi check-in berhasil',
    //             'presensi' => $presensi,
    //             'output' => $output
    //         ]);
    //     } elseif (!$presensi->check_out) {
    //         $presensi->check_out = now();
    //         $presensi->status = $recognized ? 'success' : 'failed';
    //         $presensi->save();

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Presensi check-out berhasil',
    //             'presensi' => $presensi,
    //             'output' => $output
    //         ]);
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Presensi hari ini sudah lengkap',
    //         'presensi' => $presensi,
    //         'output' => $output
    //     ]);
    // }
    public function verifyFace(Request $request)
    {
        $request->validate([
            'uid' => 'required|string',
            'foto' => 'required|string' // base64
        ]);

        $uid = $request->uid;
        $base64Image = $request->foto;
        $forceFailed = $request->boolean('force_failed');

        Log::info('UID: ' . $uid);
        Log::info('Foto: ' . substr($base64Image, 0, 100));

        $user = User::where('uid', $uid)->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User tidak ditemukan'], 404);
        }

        if (!$user->image) {
            return response()->json(['success' => false, 'message' => 'Folder dataset tidak ditemukan'], 404);
        }

        $cloudinaryPath = $user->username;

        // Siapkan Cloudinary instance
        $cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key'    => env('CLOUDINARY_API_KEY'),
                'api_secret' => env('CLOUDINARY_API_SECRET'),
            ],
            'url' => [
                'secure' => true
            ]
        ]);

        if ($forceFailed) {
            // Decode base64 jadi file sementara
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));
            $tempPath = storage_path('app/temp_failed_' . uniqid() . '.png');
            file_put_contents($tempPath, $imageData);

            try {
                $folderName = 'failed';
                $publicId = $user->username . '_' . now()->format('Ymd_His');

                $uploadedFile = $cloudinary->uploadApi()->upload(
                    $tempPath,
                    [
                        'folder' => $folderName,
                        'public_id' => $publicId
                    ]
                );

                $imageUrl = $uploadedFile['secure_url'];
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal upload ke Cloudinary.',
                    'error' => $e->getMessage()
                ], 500);
            } finally {
                // Hapus file sementara
                if (file_exists($tempPath)) {
                    unlink($tempPath);
                }
            }

            // Simpan presensi failed
            $today = now();
            $presensi = Presensi::where('user_id', $user->id)
                ->whereDate('created_at', $today)
                ->first();

            if (!$presensi) {
                $presensi = new Presensi();
                $presensi->user_id = $user->id;
                $presensi->type = 'site';
                $presensi->status = 'failed';
                $presensi->image = $imageUrl;
                $presensi->save();
            }else{
                $presensi->check_out = now();
                $presensi->status = 'failed';
                $presensi->image = $imageUrl;
                $presensi->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Verifikasi gagal setelah 3x. Presensi dicatat sebagai gagal.',
                'presensi' => $presensi
            ]);
        }

        // Kirim ke Flask API
        $response = Http::post("https://fluent-intensely-foal.ngrok-free.app/verify", [
            'image' => $base64Image,
            'dataset_path' => $cloudinaryPath,
        ]);

        Log::info($response->body());

        if (!$response->successful()) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghubungi server verifikasi.',
                'detail' => $response->body()
            ], $response->status());
        }

        $data = $response->json();
        $isRecognized = $data['result'] ?? false;

        if (!$isRecognized) {
            return response()->json([
                'success' => false,
                'message' => 'Verifikasi wajah gagal',
                'response' => $data
            ], 403);
        }

        // Presensi success
        $today = now();
        $presensi = Presensi::where('user_id', $user->id)
            ->whereDate('created_at', $today)
            ->first();

        if (!$presensi) {
            $presensi = new Presensi();
            $presensi->user_id = $user->id;
            $presensi->type = 'site';
            $presensi->status = 'success';
            $presensi->save();

            return response()->json([
                'success' => true,
                'message' => 'Presensi check-in berhasil',
                'presensi' => $presensi,
                'response' => $data
            ]);
        } elseif (!$presensi->check_out) {
            $presensi->check_out = now();
            $presensi->status = 'success';
            $presensi->save();

            return response()->json([
                'success' => true,
                'message' => 'Presensi check-out berhasil',
                'presensi' => $presensi,
                'response' => $data
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Presensi hari ini sudah lengkap',
            'presensi' => $presensi,
            'response' => $data
        ]);
    }

    public function verifikasi(Request $request)
    {
        $image = $request->input('image');
        $kantorId = $request->input('kantor_id');
        $jenis = $request->input('jenis');
        $lat = $request->input('lat');
        $lng = $request->input('lng');
        $forceSave = $request->input('force_save');
        $id = $request->input('id');

        $user = User::findOrFail($id);

        $today = now();
        $presensi = Presensi::where('user_id', $user->id)
            ->whereDate('created_at', $today)
            ->first();

        if ($presensi) {
            return response()->json(['success' => false, 'message' => 'Presensi sudah dibuat']);
        }

        if (!$image || !$user) {
            return response()->json(['success' => false, 'message' => 'Data tidak lengkap']);
        }

        // Tentukan path
        $foto = $user->image;
        $datasetPath = storage_path('app/public/' . $foto);
        $fileName = 'temp_' . time() . '.jpg';
        $testImagePath = storage_path('app/public/tmp/' . $fileName);

        // Pastikan folder temp ada
        if (!file_exists(dirname($testImagePath))) {
            mkdir(dirname($testImagePath), 0755, true);
        }

        // Decode base64 dan simpan sebagai JPG
        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace('data:image/jpeg;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        file_put_contents($testImagePath, base64_decode($image));

        // Jalankan Python script
        $scriptPath = base_path('scripts/face_recognition.py');
        $escapedDataset = escapeshellarg($datasetPath);
        $escapedImage = escapeshellarg($testImagePath);
        $command = "python $scriptPath $escapedDataset $escapedImage";

        $output = trim(shell_exec($command));
        $lines = explode("\n", trim($output));
        $lastLine = end($lines);
        $recognized = ($lastLine === "true");

        if (!$recognized && !$forceSave) {
            // Belum mau simpan, beri kesempatan coba lagi
            return response()->json(['success' => false, 'message' => 'Wajah tidak cocok', 'output' => $output]);
        }

        $presensi = new Presensi();
        $presensi->user_id = $user->id;
        $presensi->type = "dinas-$jenis";
        $presensi->status = $recognized ? 'success' : 'failed';
        $presensi->kantor_id = $kantorId;
        $presensi->lat = $lat;
        $presensi->long = $lng;

        if (!$recognized) {
            $failPath = 'failed/' . $user->username . '_' . time() . '.jpg';
            $storageFailPath = storage_path('app/public/' . $failPath);

            if (!file_exists(dirname($storageFailPath))) {
                mkdir(dirname($storageFailPath), 0755, true);
            }
            copy($testImagePath, $storageFailPath);
            $presensi->image = $failPath;
        }

        $presensi->save();

        // Hapus gambar sementara
        if (file_exists($testImagePath)) {
            unlink($testImagePath);
            Storage::delete('public/tmp/' . $fileName);
        }

        return response()->json([
            'success' => true,
            'output' => $output,
            'recognized' => $recognized
        ]);
    }
}
