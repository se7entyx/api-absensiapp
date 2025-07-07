<?php

namespace App\Http\Controllers;

use App\Exports\PresensiExport;
use App\Exports\PresensiMultiSheetExport;
use App\Models\Kantor;
use App\Models\Presensi;
use App\Models\User;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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
        // dd($filters);
        return Excel::download(new PresensiMultiSheetExport($filters), 'data-presensi.xlsx');
    }
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:success,failed'
        ]);
        $presensi = Presensi::findOrFail($id);
        $presensi->status = $validated['status'];
        if ($presensi->image) {
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

            try {
                $filename = pathinfo(parse_url($presensi->image, PHP_URL_PATH), PATHINFO_FILENAME);
                $dirname  = trim(dirname(parse_url($presensi->image, PHP_URL_PATH)), '/');
                $publicId = $dirname . '/' . $filename;

                $cloudinary->uploadApi()->destroy($publicId);
            } catch (\Exception $e) {
                Log::error('Cloudinary delete error (presensi image): ' . $e->getMessage());
            }

            $presensi->image = null;
        }
        $presensi->save();
        return redirect()->route('presensi.rekap')->with('success', 'Presensi updated successfully.');
    }
    public function my()
    {
        $presensis = Presensi::with('user', 'kantor')->where('user_id', Auth::id())->filter(request(['start_date', 'end_date', 'type', 'kantor_id']))->sortable()->latest()->paginate(10)->withQueryString();
        $kantors = Kantor::orderBy('name')->get();
        return view('presensi-my', ['title' => 'My Presensi', 'presensis' => $presensis, 'kantors' => $kantors]);
    }
    public function rekap()
    {
        $presensis = Presensi::with('user', 'kantor')->filter(request(['search', 'start_date', 'end_date', 'type', 'kantor_id']))->sortable()->latest()->paginate(10)->withQueryString();
        $kantors = Kantor::orderBy('name')->get();
        // die($presensis);
        // dd($presensis->total(), $presensis->count(), $presensis->lastPage());
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

        $today = now();
        $presensi = Presensi::where('user_id', $user->id)
            ->whereDate('created_at', $today)
            ->first();

        if ($presensi->check_out) {
            return response()->json([
                'success' => false,
                'message' => 'Presensi sudah dilakukan hari ini',
                'presensi' => $presensi,
                'done' => 'true'
            ]);
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
            $today = now();

            $presensi = Presensi::where('user_id', $user->id)
                ->whereDate('created_at', $today)
                ->first();

            if ($presensi) {
                if (is_null($presensi->check_out)) {
                    // Jika sudah check-in tapi belum check-out → isi check-out
                    $presensi->check_out = now();
                    $presensi->save();

                    return response()->json([
                        'success' => true,
                        'message' => 'Presensi check-out berhasil',
                        'presensi' => $presensi,
                    ]);
                } else {
                    // Sudah check-in dan check-out → tidak perlu presensi lagi
                    return response()->json([
                        'success' => false,
                        'message' => 'Presensi hari ini sudah lengkap',
                        'presensi' => $presensi,
                        'done' => true
                    ]);
                }
            } else {
                // Presensi baru (check-in)
                $presensi = new Presensi();
                $presensi->user_id = $user->id;
                $presensi->type = 'dinas-' . $request->jenis;
                $presensi->status = 'success';
                $presensi->kantor_id = $request->kantor_id;
                $presensi->lat = $request->lat;
                $presensi->long = $request->lng;
                $presensi->created_at = now(); // Check-in waktu sekarang
                $presensi->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Presensi Check In berhasil',
                    'presensi' => $presensi
                ]);
            }
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
            $today = now();

            $presensi = Presensi::where('user_id', $user->id)
                ->whereDate('created_at', $today)
                ->first();

            if ($presensi) {
                if (is_null($presensi->check_out)) {
                    // Jika sudah check-in tapi belum check-out → isi check-out
                    $presensi->check_out = now();
                    $presensi->type = 'dinas-' . $request->jenis;
                    $presensi->status = 'failed';
                    $presensi->kantor_id = $request->kantor_id;
                    $presensi->lat = $request->lat;
                    $presensi->long = $request->lng;
                    $presensi->save();

                    return response()->json([
                        'success' => true,
                        'message' => 'Presensi check-out berhasil',
                        'presensi' => $presensi,
                    ]);
                } else {
                    // Sudah check-in dan check-out → tidak perlu presensi lagi
                    return response()->json([
                        'success' => false,
                        'message' => 'Presensi hari ini sudah lengkap',
                        'presensi' => $presensi,
                        'done' => true
                    ]);
                }
            } else {
                // Presensi baru (check-in)
                $presensi = new Presensi();
                $presensi->user_id = $user->id;
                $presensi->type = 'dinas-' . $request->jenis;
                $presensi->status = 'failed';
                $presensi->kantor_id = $request->kantor_id;
                $presensi->lat = $request->lat;
                $presensi->long = $request->lng;
                $presensi->created_at = now(); // Check-in waktu sekarang
                $presensi->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Presensi Check In berhasil',
                    'presensi' => $presensi
                ]);
            }
        }

        // Belum 3x gagal
        return response()->json([
            'success' => false,
            'message' => 'Verifikasi wajah gagal. Coba lagi.',
            'recognized' => false
        ]);
    }
}
