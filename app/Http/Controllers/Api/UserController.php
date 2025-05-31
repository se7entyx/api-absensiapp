<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function getUsers()
    {
        $users = User::with('department')->orderBy('name')->get();
        $departments = Department::orderBy('name')->get();
        return response()->json([
            'users' => $users,
            'departments' => $departments
        ]);
    }

    public function store(Request $request)
    {
        // dd('masuk');
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
        // User::create($validated);

        return response()->json(['message' => 'User created successfully'], 201);
    }

    public function update(Request $request, $id)
    {
        // dd('masuk');
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


        // ($validated);

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

        return response()->json(['message' => 'User updated successfully']);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->image) {
            Storage::disk('public')->deleteDirectory($user->image);
        }

        if ($user->signature) {
            Storage::delete('public/' . $user->signature);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully'], 200);
    }
}
