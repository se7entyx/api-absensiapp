<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use Cloudinary\Api\Admin\AdminApi;
use Cloudinary\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    // 
    public function index()
    {
        $userr = Auth::user();
        $id = $userr->id;
        $user = User::with('department')->find($id);
        $photos = collect();

        if (!empty($user->image)) {
            try {
                $api = new AdminApi();
                $folder = $user->image; // contoh: foto/raf123

                $resources = $api->assets([
                    'type' => 'upload',
                    'prefix' => $folder . '/', // penting: harus pakai slash
                    'max_results' => 100 // bisa ditambah sesuai kebutuhan
                ]);

                $photos = collect($resources['resources'])->pluck('secure_url');
            } catch (\Exception $e) {
                Log::error('Cloudinary fetch error: ' . $e->getMessage());
            }
        }

        $isProfileIncomplete = empty($user->email) || empty($user->signature) || empty($user->department_id) || empty($user->image);
        $departments = Department::all();

        return view('profile', [
            'title' => "Profile",
            'isProfileIncomplete' => $isProfileIncomplete,
            'departments' => $departments,
            'user' => $user,
            'photos' => $photos
        ]);
    }
    public function changePassword(Request $request)
    {
        $credentials = $request->validate([
            'new_password' => 'required|min:8|max:255',
            'confirm_password' => 'required|min:8|max:255'
        ]);

        if ($request->new_password !== $request->confirm_password) {
            return back()->withErrors(['confirm_password' => 'The new password and confirm password do not match.']);
        }

        $user = User::find(Auth::user()->id);
        $user->password = Hash::make($credentials['new_password']);
        $user->save();
        return back()->with('status', 'Password successfully changed.');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|unique:users,username,' . Auth::id(),
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'foto.*' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'ttd' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);
        $cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key'    => env('CLOUDINARY_API_KEY'),
                'api_secret' => env('CLOUDINARY_API_SECRET'),
            ],
            'url' => ['secure' => true]
        ]);
        $user = User::findOrFail(Auth::id());
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        if ($user->username !== $validated['username']) {
            $oldUsername = $user->username;
            $newUsername = $validated['username'];

            $oldFolder = 'foto/' . $oldUsername;
            $newFolder = 'foto/' . $newUsername;

            // Rename folder jika folder lama ada
            // if (Storage::disk('public')->exists($oldFolder)) {
            //     Storage::disk('public')->makeDirectory($newFolder); // pastikan folder baru dibuat
            //     $files = Storage::disk('public')->files($oldFolder);

            //     foreach ($files as $file) {
            //         $filename = basename($file);
            //         Storage::disk('public')->move($file, $newFolder . '/' . $filename);
            //     }

            //     Storage::disk('public')->deleteDirectory($oldFolder); // hapus folder lama
            // }

            // Update path image dan username
            $user->image = $newFolder;
            $user->username = $newUsername;
        }

        // if ($request->hasFile('foto')) {
        //     $folderName = 'foto/' . $user->username;

        //     if ($user->image) {
        //         Storage::disk('public')->deleteDirectory($user->image);
        //     }

        //     // Save new files
        //     foreach ($request->file('foto') as $photo) {
        //         $filename = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
        //         $photo->storeAs($folderName, $filename, 'public');
        //     }

        //     $user->image = $folderName;
        // }
        if ($request->hasFile('foto')) {
            $folderName = 'foto/' . $user->username;

            // Delete all old photos
            if ($user->image) {
                try {
                    $adminApi = new AdminApi();
                    $resources = $adminApi->assets([
                        'type' => 'upload',
                        'prefix' => $user->image . '/',
                    ]);

                    foreach ($resources['resources'] as $resource) {
                        $cloudinary->uploadApi()->destroy($resource['public_id'], [
                            'resource_type' => 'image'
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('Cloudinary delete error (foto lama): ' . $e->getMessage());
                }
            }

            foreach ($request->file('foto') as $photo) {
                $filename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                $publicId = date('Y-m-d_His') . '_' . uniqid() . '_' . $filename;

                try {
                    $cloudinary->uploadApi()->upload(
                        $photo->getRealPath(),
                        [
                            'folder' => $folderName,
                            'public_id' => $publicId
                        ]
                    );
                } catch (\Exception $e) {
                    Log::error('Cloudinary upload error (foto): ' . $e->getMessage());
                }
            }

            $user->image = $folderName;
        }


        // if ($request->hasFile('ttd')) {
        //     if ($user->signature) {
        //         Storage::delete('public/' . $user->signature);
        //     }
        //     $signature = $request->file('ttd');
        //     $signatureName = 'ttd_' . time() . '.' . $signature->getClientOriginalExtension();
        //     $filePath = $signature->storeAs('ttd', $signatureName, 'public');
        //     $user->signature = $filePath;
        // }
        if ($request->hasFile('ttd')) {
            if ($user->signature) {
                try {
                    $publicId = pathinfo(parse_url($user->signature, PHP_URL_PATH), PATHINFO_FILENAME);
                    $cloudinary->uploadApi()->destroy('ttd/' . $publicId);
                } catch (\Exception $e) {
                    Log::error('Cloudinary delete error (ttd): ' . $e->getMessage());
                }
            }

            $signature = $request->file('ttd');
            $publicId = 'ttd_' . date('Y-m-d_His');

            try {
                $uploadResult = $cloudinary->uploadApi()->upload(
                    $signature->getRealPath(),
                    [
                        'folder' => 'ttd',
                        'public_id' => $publicId
                    ]
                );
                $user->signature = $uploadResult['secure_url'];
            } catch (\Exception $e) {
                Log::error('Cloudinary upload error (ttd): ' . $e->getMessage());
            }
        }
        $user->save();
        return back()->with('success', 'Update successful!');
    }
}
