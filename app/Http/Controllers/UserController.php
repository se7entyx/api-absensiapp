<?php

namespace App\Http\Controllers;
// use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('department')->filter(request(['search']))->sortable()->latest()->paginate(5)->withQueryString();
        $departments = Department::all();
        return view('master-user', ['title' => 'Master User', 'users' => $users, 'departments' => $departments]);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'uid' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'email' => 'required|email',
            'role' => 'required|in:admin,user',
            'status' => 'required|in:active,inactive',
            'foto.*' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'ttd' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        try {
            $user = new User();
            $user->username = $validated['username'];
            $user->name = $validated['name'];
            $user->uid = $validated['uid'];
            $user->department_id = $validated['department'];
            $user->email = $validated['email'];
            $user->role = $validated['role'];
            $user->status = $validated['status'];
            $user->password = Hash::make('password');
            // Log::info('Validasi berhasil:', $validated);

            if ($request->hasFile('foto')) {
                $folderName = 'foto/' . $user->username;
                foreach ($request->file('foto') as $photo) {
                    $filename = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                    $photo->storeAs($folderName, $filename, 'public');
                }
                $user->image = $folderName;
            }


            if ($request->hasFile('ttd')) {
                $signature = $request->file('ttd');
                $signatureName = 'ttd_' . time() . '.' . $signature->getClientOriginalExtension();
                $filePath = $signature->storeAs('ttd', $signatureName, 'public');
                $user->signature = $filePath;
            }

            $user->save();
            return back()->with('success', 'Data berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan saat menambahkan data.']);
        }
    }

    public function update(Request $request, $id) {
        $validated = $request->validate([
            'username' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'uid' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'email' => 'required|email',
            'password' => 'nullable|string|min:8',
            'role' => 'required|in:admin,user',
            'status' => 'required|in:active,inactive',
            'foto.*' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'ttd' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user = User::findOrFail($id);

        $user->username = $validated['username'];
        $user->name = $validated['name'];
        $user->uid = $validated['uid'];
        $user->department_id = $validated['department'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];
        $user->status = $validated['status'];
        // $user->save();

        if ($request->hasFile('foto')) {
            $folderName = 'foto/' . $user->username;

            // Delete existing directory if it exists
            if ($user->image) {
                Storage::disk('public')->deleteDirectory($user->image);
            }

            // Save new files
            foreach ($request->file('foto') as $photo) {
                $filename = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                $photo->storeAs($folderName, $filename, 'public');
            }

            $user->image = $folderName;
        }


        if ($request->hasFile('ttd')) {
            if ($user->signature) {
                Storage::delete('public/' . $user->signature);
            }
            $signature = $request->file('ttd');
            $signatureName = 'ttd_' . time() . '.' . $signature->getClientOriginalExtension();
            $filePath = $signature->storeAs('ttd', $signatureName, 'public');
            $user->signature = $filePath;
        }

        if (!is_null($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();
        return back()->with('status', 'Data berhasil diedit.');

    }
    public function destroy($id) {
        $user = User::findOrFail($id);

        if ($user->image) {
            Storage::disk('public')->deleteDirectory($user->image);
        }

        if ($user->signature) {
            Storage::delete('public/' . $user->signature);
        }

        $user->delete();

        return back()->with('successdel', 'Delete successfull!');
    }
}
