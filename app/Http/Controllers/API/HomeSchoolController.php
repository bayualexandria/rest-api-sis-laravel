<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ProfileSekolah;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HomeSchoolController extends Controller
{
    protected $sekolah;

    function __construct()
    {
        $this->sekolah = new ProfileSekolah();
    }

    function getDataSekolah()
    {
        $data =
        $this->sekolah->where('id', 1)->first();
        if ($data) {
            return response()->json(['data' => $data, 'message' => 'Data berhasil ditampilkan!', 'status' => 200], 200);
        }
        return response()->json(['message' => 'Data tidak ditemukan!', 'status' => 404], 404);
    }

    function updateDataSekolah(Request $request)
    {

        $profileSekolah
            = $this->sekolah->where('id', 1)->first();

        $profileSekolah->update([
            'nama_sekolah' => $request->nama_sekolah
                ? $request->nama_sekolah
                : $profileSekolah->nama_sekolah,
            'no_telp' => $request->no_telp
                ? $request->no_telp
                : $profileSekolah->no_telp,
            'alamat_sekolah' => $request->alamat_sekolah
                ? $request->alamat_sekolah
                : $profileSekolah->alamat_sekolah,
            'akreditasi' => $request->akreditasi
                ? $request->akreditasi
                : $profileSekolah->akreditasi
        ]);
        return response()->json(['message' => 'Data berhasil diupdate', 'status' => 200], 200);
    }
}
