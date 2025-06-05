<?php

namespace App\Http\Controllers;

use App\Mail\CreateCuti;
use App\Mail\NotifCutiHRD;
use App\Mail\NotifCutiUser;
use App\Models\Cuti;
use App\Models\Department;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\HariLibur;
use App\Models\User;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class CutiController extends Controller
{
    public function index()
    {
        return view('cuti-new', ['title' => 'New Cuti']);
    }
    public function hitungHariKerja(Request $request)
    {
        $start = Carbon::parse($request->start);
        $end = Carbon::parse($request->end);

        // Ambil semua tanggal hari libur
        $hariLibur = HariLibur::whereBetween('tanggal', [$start, $end])
            ->pluck('tanggal')
            ->map(fn($date) => Carbon::parse($date)->toDateString())
            ->toArray();

        $totalDays = 0;

        while ($start->lte($end)) {
            if (!$start->isWeekend() && !in_array($start->toDateString(), $hariLibur)) {
                $totalDays++;
            }
            $start->addDay();
        }

        return response()->json(['total_days' => $totalDays]);
    }
    public function store(Request $request)
    {
        $id = Auth::id();
        $validated = $request->validate([
            'keterangan' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'total_days' => 'required|numeric|min:0'
        ]);

        // Cek jika total_days = 0
        if ($validated['total_days'] == 0) {
            return redirect()->back()->withInput()->with('error', 'Total hari kerja tidak boleh 0.');
        }
        $cuti = new Cuti();
        $cuti->user_id = $id;
        $cuti->keterangan = $validated['keterangan'];
        $cuti->start_date = $validated['start_date'];
        $cuti->end_date = $validated['end_date'];
        $cuti->jumlah_hari = $validated['total_days'];
        $cuti->status = 'acc0';
        $cuti->save();

        $manager = Auth::user()->department->leader;
        $msg = $cuti;
        $subject = "Pengajuan Cuti Karyawan";
        $to = $manager->email;
        Mail::to($to)->send(new CreateCuti($msg, $subject));

        return redirect()->back()->with('success', 'Data berhasil disimpan.');
    }
    public function onGoing()
    {
        $user = Auth::user();
        $leaders = Department::pluck('user_id')->toArray();
        if (in_array(Auth::id(), $leaders)) {
            $userIdsInSameDept = User::where('department_id', $user->department_id)->pluck('id');

            $izins = Cuti::where(function ($query) use ($userIdsInSameDept, $user) {
                $query->whereIn('user_id', $userIdsInSameDept)
                    ->orWhere('user_id', $user->id);
            })->latest()->paginate(10);
        } elseif ($user->department && $user->department->name == 'HRD') {
            $izins = Cuti::latest()->paginate(10);
        } else {
            $izins = Cuti::where('user_id', Auth::id())->latest()->paginate(10);
        }
        return view('cuti-ongoing', ['title' => 'On Going - Cuti', 'cutis' => $izins]);
    }

    public function edit($id)
    {
        $izin = Cuti::where('id', $id)->first();
        return view('cuti-edit', ['title' => 'Edit Cuti', 'cuti' => $izin]);
    }

    public function update(Request $request, $id)
    {
        // $id = Auth::id();
        $validated = $request->validate([
            'keterangan' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'total_days' => 'required|numeric|min:0'
        ]);

        // Cek jika total_days = 0
        if ($validated['total_days'] == 0) {
            return redirect()->back()->withInput()->with('error', 'Total hari kerja tidak boleh 0.');
        }
        $cuti = Cuti::where('id', $id)->first();
        $cuti->user_id = Auth::id();
        $cuti->keterangan = $validated['keterangan'];
        $cuti->start_date = $validated['start_date'];
        $cuti->end_date = $validated['end_date'];
        $cuti->jumlah_hari = $validated['total_days'];
        $cuti->status = 'acc0';
        $cuti->save();

        return redirect()->back()->with('success', 'Data berhasil disimpan.');
    }

    public function destroy($id)
    {
        $cuti = Cuti::where('id', $id)->first();
        $cuti->delete();
        return back()->with('successdel', 'Delete successfull!');
    }

    public function print($id)
    {
        $cuti = Cuti::findOrFail($id);
        $html = view('cuti-print', compact('cuti'))->render();
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $output = $dompdf->output();
        $filePath = storage_path('app/public/cuti.pdf');
        file_put_contents($filePath, $output);

        return $dompdf->stream($filePath, ['Attachment' => 0]);
    }
    public function approveIndex($id)
    {
        $izin = Cuti::where('id', $id)->first();
        return view('cuti-approve', ['title' => 'Approval Surat Cuti', 'cuti' => $izin]);
    }
    public function approve($id)
    {
        $cuti = Cuti::where('id', $id)->first();
        $cuti->status = 'acc1';
        $cuti->save();
        $msg = $cuti;
        $subject = "Pengajuan Surat Cuti";
        $to = $cuti->user->email;
        Mail::to($to)->send(new NotifCutiUser($msg, $subject));

        $users = User::whereHas('department', function ($query) {
            $query->where('name', 'HRD');
        })->get();
        foreach ($users as $user) {
            $to = $user->email;
            Mail::to($to)->send(new NotifCutiHRD($msg, $subject));
        }
        return redirect()->route('cuti.ongoing')->with('success', 'Pengajuan cuti approved.');
    }
    public function reject($id, Request $request)
    {
        $validated = $request->validate([
            'revisi' => 'required|string'
        ]);
        $izin = Cuti::where('id', $id)->first();
        $izin->revisi = $validated['revisi'];
        $izin->status = 'acc-1';
        $izin->save();
        $msg = $izin;
        $to = $izin->user->email;
        $subject = "Pengajuan Surat Izin";
        Mail::to($to)->send(new NotifCutiUser($msg, $subject));
        return redirect()->route('cuti.ongoing')->with('success', 'Pengajuan cuti rejected.');
    }
    public function rekap(){
        $izins = Cuti::latest()->filter(request(['search','start_keluar','end_keluar','start_created','end_created']))->sortable()->paginate(15);
        return view('cuti-rekap', ['title' => 'Rekap Cuti', 'cutis' => $izins]);
    }
}
