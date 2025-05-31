<?php

namespace App\Http\Controllers;

use App\Exports\PresensiExport;
use App\Models\Kantor;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        return Excel::download(new PresensiExport($filters), 'data-presensi.xlsx');
    }
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:success,failed'
        ]);
        $presensi = Presensi::findOrFail($id);
        $presensi->status = $validated['status'];
        if($presensi->image){
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
        $image = $request->input('image');
        $kantorId = $request->input('kantor_id');
        $jenis = $request->input('jenis');
        $lat = $request->input('lat');
        $lng = $request->input('lng');
        $forceSave = $request->input('force_save', false);

        $user = Auth::user();

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
