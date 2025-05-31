<?php

namespace App\Http\Controllers;

use App\Models\HariLibur;
use Illuminate\Http\Request;

class LiburController extends Controller
{
    public function index()
    {
        $liburs = HariLibur::orderBy('name')->paginate(20);
        // dd($users);
        return view('master-libur', ['title' => 'Master Libur', 'liburs' => $liburs]);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'tanggal' => 'required|date',
        ]);

        try {
            $department = new HariLibur();
            $department->name = $validated['name'];
            $department->tanggal = $validated['tanggal'];
            $department->save();
            return back()->with('success', 'Data berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan saat menambahkan data.']);
        }
    }

    public function update(Request $request, $id){
        $validated = $request->validate([
            'name' => 'required|max:255',
            'tanggal' => 'nullable|date',
        ]);

        $department = HariLibur::findOrFail($id);
        $department->name = $validated['name'];
        $department->tanggal = $validated['tanggal'];
        $department->save();

        return back()->with('status', 'Data berhasil diupdate.');
    }

    public function destroy($id)
    {
        $department = HariLibur::findOrFail($id);
        $department->delete();
        return back()->with('successdel', 'Delete successfull!');
    }
}
