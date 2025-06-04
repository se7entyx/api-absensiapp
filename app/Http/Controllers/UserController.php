<?php

namespace App\Http\Controllers;
// use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Cloudinary\Api\Admin\AdminApi;
use Cloudinary\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('department')->filter(request(['search']))->sortable()->latest()->paginate(15)->withQueryString();
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

            if ($request->hasFile('foto')) {
                $allowedfileExtension = ['jpg', 'png', 'jpeg'];
                $files = $request->file('foto');
                $path = 'foto/' . $user->username; // contoh: foto/raf123

                foreach ($files as $file) {
                    $gextension = $file->getClientOriginalExtension();
                    $gcheck = in_array($gextension, $allowedfileExtension);

                    if ($gcheck) {
                        $file_extension = $file->getClientOriginalName();
                        $fileName = pathinfo($file_extension, PATHINFO_FILENAME);
                        $publicId = date('Y-m-d_His') . '_' . $fileName;

                        try {
                            $cloudinary->uploadApi()->upload(
                                $file->getRealPath(),
                                [
                                    'folder' => $path,
                                    'public_id' => $publicId,
                                ]
                            );
                            // Tidak perlu simpan URL satu per satu
                        } catch (\Exception $e) {
                            Log::error('Cloudinary upload error: ' . $e->getMessage());
                            continue;
                        }
                    }
                }

                // Cukup simpan nama folder di database
                $user->image = $path; // hasilnya: foto/raf123
            }

            if ($request->hasFile('ttd')) {
                // $signature = $request->file('ttd');
                $signatureName = 'ttd_' . time();
                $folderName = 'ttd';
                $publicId = date('Y-m-d_His') . '_' . $signatureName;
                try {
                    // Upload gambar baru
                    $uploadedFile = $cloudinary->uploadApi()->upload(
                        $request->file('ttd')->getRealPath(),
                        [
                            'folder' => $folderName,
                            'public_id' => $publicId,
                        ]
                    );
                } catch (\Exception $e) {
                    Log::error('Cloudinary upload error (update): ' . $e->getMessage());
                    return back()->with('error', 'Gagal upload gambar ke Cloudinary.');
                }
                $user->signature = $uploadedFile['secure_url'];
            }
            $user->save();
            return back()->with('success', 'Data berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan saat menambahkan data.']);
        }
    }

    public function update(Request $request, $id)
    {
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

        if ($request->hasFile('foto')) {
            $folderName = 'foto/' . $user->username;

            // Hapus foto lama di Cloudinary
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

            // Upload foto baru
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

            // Simpan folder di DB
            $user->image = $folderName;
        }

        if ($request->hasFile('ttd')) {
            if ($user->signature) {
                try {
                    $publicId = pathinfo($user->signature, PATHINFO_FILENAME);
                    $cloudinary->uploadApi()->destroy('ttd/' . $publicId);
                } catch (\Exception $e) {
                    Log::error('Cloudinary delete error (ttd): ' . $e->getMessage());
                }
            }

            $signature = $request->file('ttd');

            $signatureName = 'ttd_' . time();
            $folderName = 'ttd';
            $publicId = date('Y-m-d_His') . '_' . $signatureName;
            try {
                $uploadedFile = $cloudinary->uploadApi()->upload(
                    $signature->getRealPath(),
                    [
                        'folder' => $folderName,
                        'public_id' => $publicId
                    ]
                );
                $user->signature = $uploadedFile['secure_url'];
            } catch (\Exception $e) {
                Log::error('Cloudinary upload error (ttd): ' . $e->getMessage());
            }
        }

        $user->save();
        return back()->with('status', 'Data berhasil diedit.');
    }
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $adminApi = new AdminApi();
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

        if ($user->signature) {
            // Storage::delete('public/' . $user->signature);
            try {
                $publicId = pathinfo($user->signature, PATHINFO_FILENAME);
                $cloudinary->uploadApi()->destroy('ttd/' . $publicId);
            } catch (\Exception $e) {
                Log::error('Cloudinary delete error (ttd): ' . $e->getMessage());
            }
        }

        $user->delete();

        return back()->with('successdel', 'Delete successfull!');
    }
}
