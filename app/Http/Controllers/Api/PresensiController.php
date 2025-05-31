<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use App\Models\User;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as FacadesAuth;
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

    public function verifyFace(Request $request)
    {
        return response()->json([
                'success' => true,
                'message' => 'Presensi check-out berhasil',
            ]);
        $request->validate([
            'uid' => 'required|string',
            'foto' => 'required|string' // base64
        ]);

        $uid = $request->uid;
        $base64Image = $request->foto;

        // Cari user berdasarkan UID
        $user = User::where('uid', $uid)->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User tidak ditemukan'], 404);
        }

        // Simpan foto sementara di storage
        $filename = 'face_' . uniqid() . '.jpg';
        $tempPath = storage_path("app/public/tmp/$filename");

        if (!file_exists(dirname($tempPath))) {
            mkdir(dirname($tempPath), 0755, true);
        }

        $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));
        file_put_contents($tempPath, $imageData);

        // Panggil script Python
        $folderWajah = storage_path("app/public/{$user->foto}"); // contoh: foto/username
        $cmd = 'python ' . base_path("scripts/face_recognition/face_recognition.py") . ' ' .
            escapeshellarg($folderWajah) . ' ' .
            escapeshellarg($tempPath);
        $output = trim(shell_exec($cmd));

        // Hapus file foto setelah digunakan
        unlink($tempPath);

        // Cek hasil verifikasi
        if ($output !== "true") {
            return response()->json(['success' => false, 'message' => 'Verifikasi wajah gagal'], 403);
        }

        // Lanjutkan presensi
        $today = now();
        $presensi = Presensi::where('user_id', $user->id)
            ->whereDate('created_at', $today)
            ->first();

        if (!$presensi) {
            $presensi = new Presensi();
            $presensi->user_id = $user->id;
            $presensi->type = 'site';
            $presensi->check_in = now();
            $presensi->save();

            return response()->json([
                'success' => true,
                'message' => 'Presensi check-in berhasil',
                'presensi' => $presensi
            ]);
        } elseif (!$presensi->check_out) {
            $presensi->check_out = now();
            $presensi->save();

            return response()->json([
                'success' => true,
                'message' => 'Presensi check-out berhasil',
                'presensi' => $presensi
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Presensi hari ini sudah lengkap',
            'presensi' => $presensi
        ]);
    }
    public function verifikasi(Request $request)
    {
        $image = $request->input('image');
        $kantorId = $request->input('kantor_id');
        $jenis = $request->input('jenis');
        $lat = $request->input('lat');
        $lng = $request->input('lng');
        $forceSave = $request->input('force_save', false);
        $id = $request->input('id');

        $user = User::where('id',$id)->first();

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
