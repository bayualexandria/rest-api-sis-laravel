<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\HistoryKelas;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class KelasController extends Controller
{

    protected $kelas;
    protected $historyKelas;

    function __construct()
    {
        $this->kelas = new Kelas();
        $this->historyKelas = new HistoryKelas();
    }

    public function index()
    {

        $kelas = DB::table('history_kelas')
            ->join('kelas', 'kelas_id', 'kelas.id')
            ->join('guru', 'wali_kelas', 'guru.id')
            ->join('semester', 'semester_id', 'semester.id')
            ->select('history_kelas.id', 'kelas.nama_kelas as kelas', 'kelas.jurusan', 'guru.nama as wali_kelas', 'guru.nip as no_induk_guru', 'semester.semester', 'semester.tahun_pelajaran')
            ->get();

        return response()->json([
            'data' => $kelas,
            'message' => 'Data kelas berhasil ditampilkan',
            'status' => 200
        ], 200);
    }

    public function getKelas()
    {
        return response()->json(['data' => $this->kelas->all(), 'message' => 'Data kelas ditampilkan!', 'status' => 200], 200);
    }

    public function show(String $kelas)
    {
        $data =
            DB::table('history_kelas')
            ->join('kelas', 'kelas_id', 'kelas.id')
            ->join('guru', 'wali_kelas', 'guru.id')
            ->join('semester', 'semester_id', 'semester.id')
            ->select('history_kelas.id', 'kelas.nama_kelas as kelas', 'kelas.jurusan', 'guru.nama as wali_kelas', 'guru.nip as no_induk_guru', 'semester.semester', 'semester.tahun_pelajaran')
            ->where('history_kelas.id', $kelas)
            ->first();
        if (!$data) {
            return response()->json(['message' => 'Data yang anda cari tidak ditemukan!', 'status' => 404], 404);
        }
        return response()->json(['data' => $data, 'message' => 'Data yang telah ditampilkan!', 'status' => 200], 200);
    }

    function getDataKelasByIdGuru(String $guru)
    {
        $data =
            DB::table('history_kelas')
            ->join('kelas', 'kelas_id', 'kelas.id')
            ->join('guru', 'wali_kelas', 'guru.id')
            ->join('semester', 'semester_id', 'semester.id')
            ->select('history_kelas.id', 'kelas.nama_kelas as kelas', 'kelas.jurusan', 'guru.nama as wali_kelas', 'guru.nip as no_induk_guru', 'semester.semester', 'semester.tahun_pelajaran')
            ->where('guru.nip', $guru)
            ->get();
        if (!$data) {
            return response()->json(['message' => 'Data yang anda cari tidak ditemukan!', 'status' => 404], 404);
        }
        return response()->json(['data' => $data, 'message' => 'Data yang telah ditampilkan!', 'status' => 200], 200);
    }

    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'kelas_id' => 'required',
            'wali_kelas' => 'required',
            'semester_id' => 'required'
        ], [
            'kelas_id.required' => 'Kelas harus diisi',
            'wali_kelas.required' => 'Wali kelas harus diisi',
            'semester_id.required' => 'Semester harus diisi'
        ]);

        if ($validation->fails()) {
            return response()->json(['message' => $validation->errors(), 'status' => 403], 403);
        }
        $this->historyKelas->create($request->all());

        return response()->json(['message' => 'Data kelas berhasil ditambahkan!', 'status' => 200], 200);
    }

    public function update(String $kelas, Request $request)
    {

        $data = $this->historyKelas->where('id', $kelas)->first();
        $data->update([
            'kelas_id' => $request->kelas_id ? $request->kelas_id : $data->kelas_id,
            'wali_kelas' => $request->wali_kelas ? $request->wali_kelas : $data->wali_kelas,
            'semester_id' => $request->semester_id ? $request->semester_id : $data->semester_id
        ]);

        return response()->json(['message' => 'Data kelas berhasil diperbaharui!', 'status' => 200], 200);
    }

    public function destroy(String $kelas)
    {

        $data = $this->historyKelas->where('id', $kelas)->first();
        $data->delete();

        return response()->json(['message' => 'Data kelas berhasil dihapus!', 'status' => 200], 200);
    }
}
