<?php

namespace App\Http\Controllers;

use App\Mail\CreateIzin;
use App\Mail\NotifIzinUser;
use App\Models\Department;
use App\Models\Izin;
use App\Models\User;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class IzinController extends Controller
{
    public function index()
    {
        // dd(Auth::user()->department->user_id);
        return view('izin-new', ['title' => 'New Izin']);
    }
    public function edit($id)
    {
        $izin = Izin::where('id', $id)->first();
        return view('izin-edit', ['title' => 'Edit Izin', 'izin' => $izin]);
    }
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'keterangan' => 'required|string',
            'kembali' => 'required|string|in:ya,tidak',
            'waktu_keluar' => 'required|date',
            'waktu_kembali' => 'nullable|date'
        ]);
        $izin = Izin::where('id', $id)->first();
        $izin->keterangan = $validated['keterangan'];
        $izin->waktu_keluar = $validated['waktu_keluar'];
        $izin->kembali = $validated['kembali'];
        if ($validated['kembali'] == 'ya') {
            $izin->waktu_kembali = $validated['waktu_kembali'];
        } else {
            $izin->waktu_kembali = null;
        }
        $izin->revisi = null;
        // $izin->approved_hrd = null;
        $izin->status = 'acc0';
        $izin->save();
        return redirect()->route('izin.ongoing')->with('success', 'Pengajuan izin updated successfully.');
    }
    public function destroy($id)
    {
        $izin = Izin::where('id', $id)->first();
        $izin->delete();
        return back()->with('successdel', 'Delete successfull!');
    }
    public function approveIndex($id)
    {
        $izin = Izin::where('id', $id)->first();
        return view('izin-approve', ['title' => 'Approval Surat Izin', 'izin' => $izin]);
    }
    public function approve($id)
    {
        $izin = Izin::where('id', $id)->first();
        if ($izin->status == 'acc0') {
            $izin->status = 'acc1';
            $izin->save();
            $users = User::whereHas('department', function ($query) {
                $query->where('name', 'HRD');
            })->get();
            $msg = $izin;
            $subject = "Pengajuan Surat Izin";

            foreach ($users as $user) {
                $to = $user->email;
                Mail::to($to)->send(new CreateIzin($msg, $subject));
            }
            $to = $izin->user->email;
            Mail::to($to)->send(new NotifIzinUser($msg, $subject));
        } elseif ($izin->status == 'acc1') {
            $izin->status = 'acc2';
            $izin->approved_hrd = Auth::id();
            $izin->save();
            $msg = $izin;
            $to = $izin->user->email;
            $subject = "Pengajuan Surat Izin";
            Mail::to($to)->send(new NotifIzinUser($msg, $subject));
        }
        return redirect()->route('izin.ongoing')->with('success', 'Pengajuan izin approved.');
    }
    public function reject(Request $request, $id)
    {
        $validated = $request->validate([
            'revisi' => 'required|string'
        ]);
        $izin = Izin::where('id', $id)->first();
        $izin->revisi = $validated['revisi'];
        if ($izin->status == 'acc0') {
            $izin->status = 'acc-1';
        } elseif ($izin->status == 'acc1') {
            $izin->status == 'acc-2';
            // $izin->approved_hrd = Auth::id();
        }
        $izin->save();
        $msg = $izin;
        $to = $izin->user->email;
        $subject = "Pengajuan Surat Izin";
        Mail::to($to)->send(new NotifIzinUser($msg, $subject));
        return redirect()->route('izin.ongoing')->with('success', 'Pengajuan izin rejected.');
    }

    public function print($id){
        $izin = Izin::findOrFail($id);
        $html = view('izin-print', compact('izin'))->render();
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $output = $dompdf->output();
        $filePath = storage_path('app/public/izin.pdf');
        file_put_contents($filePath, $output);

        return $dompdf->stream($filePath, ['Attachment' => 0]);
    }
    public function store(Request $request)
    {
        if (!Auth::user()->department->leader) {
            return back()->with('unfinished', 'Manager untuk user belum diassign');
        }
        $validated = $request->validate([
            'keterangan' => 'required|string',
            'kembali' => 'required|string|in:ya,tidak',
            'waktu_keluar' => 'required|date',
            'waktu_kembali' => 'nullable|date'
        ]);
        $izin = new Izin();
        $izin->keterangan = $validated['keterangan'];
        $izin->user_id = Auth::id();
        $izin->status = 'acc0';
        $izin->waktu_keluar = $validated['waktu_keluar'];
        $izin->kembali = $validated['kembali'];
        if ($validated['kembali'] == 'ya') {
            $izin->waktu_kembali = $validated['waktu_kembali'];
        }
        $izin->save();

        $manager = Auth::user()->department->leader;

        $msg = $izin;
        $subject = "Pengajuan Surat Izin Keluar";
        $to = $manager->email;
        Mail::to($to)->send(new CreateIzin($msg, $subject));
        // Mail::to(Auth::user()->email)->send(new NotifIzinUser($msg, $subject));

        return redirect()->route('izin.index')->with('success', 'Pengajuan izin created successfully.');
    }

    public function onGoing()
    {
        $user = Auth::user();
        $leaders = Department::pluck('user_id')->toArray();
        if (in_array(Auth::id(), $leaders)) {
            $userIdsInSameDept = User::where('department_id', $user->department_id)->pluck('id');

            $izins = Izin::where(function ($query) use ($userIdsInSameDept, $user) {
                $query->whereIn('user_id', $userIdsInSameDept)
                    ->orWhere('user_id', $user->id);
            })->latest()->paginate(10);

        }elseif($user->department && $user->department->name == 'HRD'){
            $izins = Izin::latest()->paginate(10);
        } else {
            $izins = Izin::where('user_id', Auth::id())->latest()->paginate(10);
        }
        return view('izin-ongoing', ['title' => 'On Going - Izin', 'izins' => $izins]);
    }

    public function rekap(){
        $izins = Izin::latest()->filter(request(['search','start_keluar','end_keluar','start_created','end_created']))->sortable()->paginate(10);
        return view('izin-rekap', ['title' => 'Rekap Izin', 'izins' => $izins]);
    }
}
