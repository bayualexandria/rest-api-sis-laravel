<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\User;
use App\Notifications\ChangedEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Notifications\EmailVerification;
use Illuminate\Support\Facades\DB;

class GuruController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $guru;
    protected $user;

    function __construct()
    {
        $this->guru = new Guru();
        $this->user = new User();
    }
    public function index()
    {
        $guru = DB::table('guru')
            ->join('users', 'nip', 'users.username')
            ->join('status_user', 'users.status_id', 'status_user.id')
            ->select('guru.id', 'guru.nip', 'guru.nama', 'guru.jenis_kelamin', 'guru.no_hp', 'guru.image_profile', 'guru.alamat', 'status_user.status')
            ->orderBy('guru.created_at', 'desc')
            ->get();
        return response()->json(['data' => $guru, 'message' => `Data it's ok`, 'status' => 200], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'nip' => 'required|min:18|numeric|unique:guru',
            'nama' => 'required',
            'email' => 'required|email|unique:users',
            'jenis_kelamin' => 'required',
            'no_hp' => 'required|unique:guru',
            'alamat' => 'required',
            'status' => 'required'
        ], [
            'nip.required' => 'NIP harus diisi',
            'nip.min' => 'NIP minimal 18 karakter',
            'nip.unique' => 'NIP yang anda masukan sudah terdaftar',
            'nip.numeric' => 'Yang anda masukan bukan NIP',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Email yang anda masukan tidak sesuai',
            'email.unique' => 'Email yang anda masukan sudah terdaftar pada akun lain',
            'nama.required' => 'Nama harus diisi',
            'jenis_kelamin.required' => 'Jenis kelamin harus diisi',
            'no_hp.required' => 'No. Handphone harus diisi',
            'no_hp.unique' => 'No. Handphone yang anda masukan sudah terdaftar',
            'alamat.required' => 'Alamat harus diisi',
            'status.required' => 'Status user harus diisi'
        ]);

        if ($validation->fails()) {
            return response()->json(['message' => $validation->errors(), 'status' => 403], 403);
        }

        if (User::where('username', $request->nip)->first()) {
            return response()->json([
                'message' => "Data guru dengan NIP {$request->nip} sudah terdaftar pada user lain!",
                'status' => 403,
            ], 403);
        }

        $data = [
            'nip' => $request->nip,
            'nama' => $request->nama,
            'jenis_kelamin' => $request->jenis_kelamin,
            'no_hp' => $request->no_hp,
            'image_profile' => 'assets/images/users.png',
            'alamat' => $request->alamat
        ];
        $dataUser = [
            'username' => $request->nip,
            'email' => $request->email,
            'name' => $request->nama,
            'password' => bcrypt($request->nip),
            'status_id' => $request->status
        ];
        $guru = $this->guru->create($data);
        $user = $this->user->create($dataUser);

        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->plainTextToken;
        $user->notify(new EmailVerification($user));
        return response()->json([
            'data' => $guru,
            'message' => "User dengan nama {$request->nama} berhasil ditambahkan!",
            'accessToken' => $token,
            'status' => 201,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $nip)
    {
        $guru = DB::table('guru')
            ->join('users', 'nip', 'users.username')
            ->join('status_user', 'users.status_id', 'status_user.id')
            ->select('guru.nip', 'guru.nama', 'guru.jenis_kelamin', 'guru.no_hp', 'guru.image_profile', 'guru.alamat', 'status_user.status')
            ->where('guru.nip', $nip)
            ->first();
        if ($guru) {
            return response()->json(['data' => $guru, 'message' => 'Data guru dengan nip ' . $nip . ' berhasil ditampilkan', 'status' => 200], 200);
        }
        return response()->json(['message' => 'Data tidak ditemukan', 'status' => 403], 403);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $nip)
    {

        $validation = Validator::make($request->all(), [
            'image_profile' => 'mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'image_profile.mimes' => 'Format yang anda masukan bukan foto (jpeg,png,jg,gif)',
            'image_profile.max' => 'Maksimal kapasitas foto 2MB',
        ]);

        if ($validation->fails()) {
            return response()->json(['message' => $validation->errors(), 'status' => 403], 403);
        }

        $guru = Guru::where('nip', $nip)->first();
        $user = User::where('username', $nip)->first();

        if ($request->email == $user->email) {
            return response()->json(['message' => 'Email yang anda masukan sudah terdaftar!', 'status' => 403], 403);
        }

        if ($request->file('image_profile')) {

            if (!$guru->image_profile) {
                $image = $request->file('image_profile');
                $image->storeAs('public/assets/images/guru/' . $nip . '/', $image->hashName());
            }

            if ($guru->image_profile != 'assets/images/users.png') {
                Storage::delete('public/' . $guru->image_profile);
            }

            $image = $request->file('image_profile');
            $image->storeAs('public/assets/images/guru/' . $nip . '/', $image->hashName());
        }



        $data = [
            'nip' => $nip,
            'nama' => $request->nama
                ? $request->nama
                : $guru->nama,
            'jenis_kelamin' => $request->jenis_kelamin
                ? $request->jenis_kelamin
                : $guru->jenis_kelamin,
            'no_hp' => $request->no_hp
                ? $request->no_hp
                : $guru->no_hp,
            'image_profile' => $request->file('image_profile') ? 'assets/images/guru/' . $nip . '/' . $image->hashName() : $guru->image_profile,
            'alamat' => $request->alamat
                ? $request->alamat
                : $guru->alamat
        ];
        $dataUser = [
            'username' => $nip,
            'email' => $request->email ? $request->email : $user->email,
            'name' => $request->nama
                ? $request->nama
                : $user->name,
            'password' => $request->email ? bcrypt($nip) : $user->password,
            'status_id' => $request->status ? $request->status : $user->status_id
        ];
        $dataGuru = $guru->update($data);
        if ($request->email) {

            $user->update($dataUser);
            $user->notify(new ChangedEmail($user));
            return response()->json(['data' => $data, 'process' => $dataGuru, 'message' => 'Data berhasil diubah! Silahkan cek email anda untuk melihat hasil perubahan.', 'status' => 200], 200);
        }
        $user->update($dataUser);
        return response()->json(['data' => $data, 'process' => $dataGuru, 'message' => 'Data berhasil diubah', 'status' => 200], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $nip)
    {
        $guru = Guru::where('nip', $nip)->first();
        $user = User::where('username', $nip)->first();
        if (!$guru) {
            return response()->json(['message' => 'Data yang anda hapus tidak ada!', 'status' => 403], 403);
        }
        $guru->delete();
        $user->delete();
        return response()->json(['message' => "Data dengan NIP {$nip} berhasil dihapus!", 'status' => 200], 200);
    }

    function showDeleteDataGuru()
    {
        $user = Guru::onlyTrashed()->get();
        return response()->json(['data' => $user, 'message' => 'Data berhasil ditampilkan', 'status' => 200], 200);
    }

    function restoreDataGuru()
    {
        Guru::query()->restore();
        User::query()->restore();
        return response()->json(['message' => "Data Guru berhasil dikembalikan dari trash!", 'status' => 200], 200);
    }

    function restoreDataGuruById(string $nis)
    {
        Guru::withTrashed()
            ->where('nis', $nis)
            ->restore();
        User::withTrashed()
            ->where('username', $nis)
            ->restore();

        return response()->json(['message' => "Data dengan NIS {$nis} berhasil dikembalikan dari trash!", 'status' => 200], 200);
    }

    function deletePermanenDataGuruById(string $nis)
    {
        Guru::withTrashed()->where('nis', $nis)->forceDelete();
        User::withTrashed()->where('username', $nis)->forceDelete();

        return response()->json(['message' => "Data dengan NIS {$nis} berhasil dihapus secara permanent", 'status' => 200], 200);
    }

    function deletePermanenDataGuru()
    {
        Guru::onlyTrashed()->forceDelete();
        User::onlyTrashed()->forceDelete();

        return response()->json(['message' => "Data Guru berhasil dihapus secara permanent", 'status' => 200], 200);
    }
}
