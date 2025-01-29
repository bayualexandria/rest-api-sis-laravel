<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Mapel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MapelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $mapel;

    public function __construct()
    {
        $this->mapel = new Mapel();
    }

    public function index()
    {
        return response()->json(['data' => $this->mapel->all(), 'message' => 'Data berhasil ditampilkan', 'status' => 200], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'nama_mapel' => 'required',
            'kelas_id' => 'required',
            'guru_id' => 'required',
            'hari' => 'required',
            'date_start' => 'required',
            'date_end' => 'required'
        ], [
            'nama_mapel.required' => 'Nama mapel harus diisi',
            'kelas_id.required' => 'Kelas harus diisi',
            'guru_id.required' => 'Guru mapel harus diisi',
            'hari.required' => 'Hari harus diisi',
            'date_start' => 'Jam mulai harus diisi',
            'date_end' => 'Jam akhir harus diisi'
        ]);

        if ($validation->fails()) {
            return response()->json(['message' => $validation->errors(), 'status' => 403], 403);
        }
        $this->mapel->create($request->all());

        return response()->json(['data' => $request->all(), 'message' => 'Data mapel berhasil ditambahkan', 'status' => 200], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = $this->mapel->where('id', $id)->first();
        if (!$data) {
            return response()->json(['message' => 'Data yang anda cari tidak ditemukan', 'status' => 403], 403);
        }
        return response()->json(['data' => $data, 'message' => 'Data berhasil ditampilkan', 'status' => 200], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $mapel = $this->mapel->where('id', $id)->first();
        $data = [
            'nama_mapel' => $request->nama_mapel
                ? $request->nama_mapel
                : $mapel->nama_mapel,
            'kelas_id' => $request->kelas_id
                ? $request->kelas_id
                : $mapel->kelas_id,
            'guru_id' => $request->guru_id
                ? $request->guru_id
                : $mapel->guru_id,
            'hari' => $request->hari
                ? $request->hari
                : $mapel->hari
        ];

        $mapel->update($data);
        
        return response()->json([
            'data' => $data,
            'message' => 'Data berhasil diperbaharui',
            'status' => 200
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = $this->mapel->where('id', $id)->first();
        if (!$data) {
            return response()->json(['message' => 'Data yang anda cari tidak ada', 'status' => 403], 403);
        }
        $data->delete($id);
        return response()->json(['message' => 'Data mapel berhasil dihapus', 'status' => 200], 200);
    }
}
