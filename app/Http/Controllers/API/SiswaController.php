<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\User;
use App\Notifications\ChangedPassword;
use App\Notifications\ChangeEmailSiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Notifications\EmailVerification;
use Illuminate\Support\Facades\Validator;

class SiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $siswa;
    protected $user;

    function __construct()
    {
        $this->siswa = new Siswa();
        $this->user = new User();
    }

    public function index()
    {
        $user = Siswa::all();
        return response()->json(['data' => $user, 'message' => 'Success', 'status' => 200], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'nis' => 'required|numeric|unique:users,username',
            'nama' => 'required',
            'email' => 'required|email|unique:users',
            'jenis_kelamin' => 'required',
            'no_hp' => 'required|numeric|unique:siswa,no_hp',
            'alamat' => 'required'
        ], [
            'nis.required' => 'NIS harus diisi',
            'nis.numeric' => 'NIS yang anda masukan bukan angka',
            'nis.unique' => 'NIS yang anda masukan sudah terdaftar pada user lain',
            'nama.required' => 'Nama harus diisi',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Email yang anda masukan tidak sesuai format @mail.com',
            'email.unique' => 'Email yang anda masukan sudah terdaftar pada user lain',
            'jenis_kelamin.required' => 'Jenis kelamin harus diisi',
            'no_hp.required' => 'No. Handphone harus diisi',
            'no_hp.numeric' => 'No. Handphone yang anda masukan bukan angka',

            'alamat' => 'Alamat harus diisi'
        ]);

        if ($validation->fails()) {
            return response()->json(['message' => $validation->errors(), 'status' => 403], 403);
        }
        if ($this->user->where('username', $request->nis)->first()) {
            return response()->json([
                'message' => "Data siswa dengan NIS {$request->nis} sudah terdaftar pada user lain!",
                'status' => 403,
            ], 403);
        }

        $dataSiswa = [
            'nis' => $request->nis,
            'nama' => $request->nama,
            'jenis_kelamin' => $request->jenis_kelamin,
            'no_hp' => $request->no_hp,
            'image_profile' => 'assets/images/users.png',
            'alamat' => $request->alamat
        ];
        $dataUser = [
            'username' => $request->nis,
            'email' => $request->email,
            'name' => $request->nama,
            'password' => bcrypt($request->nis),
            'status_id' => 3
        ];
        $siswa = $this->siswa->create($dataSiswa);
        $user = $this->user->create($dataUser);
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->plainTextToken;
        $user->notify(new EmailVerification($user));
        return response()->json([
            'data' => $siswa,
            'message' => "User dengan nama {$request->nama} berhasil ditambahkan!",
            'accessToken' => $token,
            'status' => 201,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $nis)
    {
        $siswa = Siswa::where('nis', $nis)->first();
        if ($siswa) {
            return response()->json(['data' => $siswa, 'message' => 'Data siswa dengan nis ' . $nis . ' berhasil ditampilkan', 'status' => 200], 200);
        }
        return response()->json(['message' => 'Data tidak ditemukan', 'status' => 403], 403);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $nis)
    {
        $siswa = Siswa::where('nis', $nis)->first();
        if ($request->file('image_profile')) {
            if (!$siswa->image_profile) {

                $image = $request->file('image_profile');
                $image->storeAs('public/assets/images/nis/' . $nis . '/', $image->hashName());
            }

            if ($siswa->image_profile != 'assets/images/users.png') {
                Storage::delete('public/' . $siswa->image_profile);
            }

            $image = $request->file('image_profile');
            $image->storeAs('public/assets/images/siswa/' . $nis . '/', $image->hashName());
        }

        $data = [
            'nis' => $nis,
            'nama' => $request->nama
                ? $request->nama
                : $siswa->nama,
            'jenis_kelamin' => $request->jenis_kelamin
                ? $request->jenis_kelamin
                : $siswa->jenis_kelamin,
            'no_hp' => $request->no_hp
                ? $request->no_hp
                : $siswa->no_hp,
            'image_profile' => $request->file('image_profile') ? 'assets/images/siswa/' . $nis . '/' . $image->hashName() : $siswa->image_profile,
            'alamat' => $request->alamat
                ? $request->alamat
                : $siswa->alamat
        ];

        $dataSiswa = $siswa->update($data);
        $user = $this->user->where('username', $nis)->first();
        $user->update(['name' => $request->nama
            ? $request->nama
            : $user->name]);

        return response()->json(['data' => $data, 'process' => $dataSiswa, 'message' => 'Data berhasil diubah', 'status' => 200], 200);
    }

    public function changeEmailSiswa(Request $request, String $nis)
    {
        $validation = Validator::make($request->all(), [
            'email' => 'unique:users,email',
        ], [
            'email.unique' => 'Email yang anda masukan sudah terdaftar pada user lain',
        ]);

        if ($validation->fails()) {
            return response()->json(['message' => $validation->errors(), 'status' => 403], 403);
        }

        $user = $this->user->where('username', $nis)->first();

        $user->update(['email' => $request->email, 'email_verified_at' => null]);
        $user->notify(new ChangeEmailSiswa($user));
        return response()->json(['data' => $user, 'message' => 'Silahkan cek Email anda untuk melakukan konfirmasi perubahan Email!', 'status' => 200], 200);
    }

    public function changePasswordSiswa(Request $request, String $nis)
    {
        $validation = Validator::make($request->all(), [
            'password' => 'required|min:8',
        ], [
            'password.required' => 'Password harus diisi!',
            'password.min' => 'Password minimal harus 8 karakter!',
        ]);

        if ($validation->fails()) {
            return response()->json(['message' => $validation->errors(), 'status' => 403], 403);
        }
        $user = $this->user->where('username', $nis)->first();
        $user->update(['password' => bcrypt($request->password)]);
        $user->notify(new ChangedPassword($user, $request->password));
        return response()->json(['data' => $user, 'message' => 'Password berhasil diubah!', 'status' => 200], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $nis)
    {
        $siswa = Siswa::where('nis', $nis)->first();
        $user = User::where('username', $nis)->first();
        if (!$siswa) {
            return response()->json(['message' => 'Data yang anda hapus tidak ada!', 'status' => 403], 403);
        }
        $siswa->delete();
        $user->delete();
        return response()->json(['message' => "Data dengan NIS {$nis} berhasil dihapus!", 'status' => 200], 200);
    }

    function showDeleteDataSiswa()
    {

        $user = Siswa::onlyTrashed()->get();
        return response()->json(['data' => $user, 'message' => 'Data berhasil ditampilkan', 'status' => 200], 200);
    }

    function restoreDataSiswa()
    {
        Siswa::query()->restore();
        User::query()->restore();
        return response()->json(['message' => "Data Siswa berhasil dikembalikan dari trash!", 'status' => 200], 200);
    }

    function restoreDataSiswaById(string $nis)
    {
        Siswa::withTrashed()
            ->where('nis', $nis)
            ->restore();
        User::withTrashed()
            ->where('username', $nis)
            ->restore();

        return response()->json(['message' => "Data dengan NIS {$nis} berhasil dikembalikan dari trash!", 'status' => 200], 200);
    }

    function deletePermanenDataSiswaById(string $nis)
    {
        Siswa::withTrashed()->where('nis', $nis)->forceDelete();
        User::withTrashed()->where('username', $nis)->forceDelete();

        return response()->json(['message' => "Data dengan NIS {$nis} berhasil dihapus secara permanent", 'status' => 200], 200);
    }

    function deletePermanenDataSiswa()
    {
        Siswa::onlyTrashed()->forceDelete();
        User::onlyTrashed()->forceDelete();

        return response()->json(['message' => "Data siswa berhasil dihapus secara permanent", 'status' => 200], 200);
    }
}
