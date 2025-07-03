<?php

namespace App\Http\Controllers;

use App\Models\Kantor;
use Illuminate\Http\Request;

class KantorController extends Controller
{
    public function index()
    {
        $liburs = Kantor::orderBy('name')->paginate(20);
        return view('master-kantor', ['title' => 'Master Kantor', 'kantors' => $liburs]);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'lat' => 'nullable|numeric',
            'long' => 'nullable|numeric',
            'radius' => 'required|numeric'
        ]);

        $kantor = new Kantor();
        $kantor->name = $validated['name'];
        $kantor->lat = $validated['lat'];
        $kantor->long = $validated['long'];
        $kantor->radius = $validated['radius'];
        $kantor->save();

        return redirect()->back()->with('success', 'Kantor berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $kantor = Kantor::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'lat' => 'nullable|numeric',
            'long' => 'nullable|numeric',
            'radius' => 'required|numeric'
        ]);

        // $kantor = new Kantor();
        $kantor->name = $validated['name'];
        $kantor->lat = $validated['lat'];
        $kantor->long = $validated['long'];
        $kantor->radius = $validated['radius'];
        $kantor->save();

        return redirect()->back()->with('success', 'Kantor berhasil diedit.');
    }

    public function destroy($id)
    {
        $department = Kantor::findOrFail($id);
        $department->delete();
        return back()->with('successdel', 'Delete successfull!');
    }
}
