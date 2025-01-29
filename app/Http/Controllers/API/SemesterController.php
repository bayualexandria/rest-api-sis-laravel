<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SemesterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $semester;

    function __construct()
    {
        $this->semester = new Semester();
    }

    public function index()
    {
        return response()->json([
            'data' => $this->semester->all(),
            'message' => 'Data semester berhasil ditampilkan',
            'status' => 200
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'semester' => "required",
            'tahun_pelajaran' => "required"
        ], [
            'semester.required' => "Semester harus diisi",
            "tahun_pelajaran.required" => "Tahun pelajaran harus diisi"
        ]);

        if ($validation->fails()) return response()->json(['message' => $validation->errors(), 'status' => 403], 403);


        $this->semester->create([
            'semester' => $request->semester,
            'tahun_pelajaran' => $request->tahun_pelajaran
        ]);

        return response()->json(['message' => 'Data semester berhasil ditambahkan!', 'status' => 200], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Semester $semester)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Semester $semester)
    {
        $validation = Validator::make($request->all(), [
            'semester' => "required",
            'tahun_pelajaran' => "required"
        ], [
            'semester.required' => "Semester harus diisi",
            "tahun_pelajaran.required" => "Tahun pelajaran harus diisi"
        ]);

        if ($validation->fails()) return response()->json(['message' => $validation->errors(), 'status' => 403], 403);


        $semester = $this->semester->where('id', $semester)->first();
        $semester->update([
            'semester' => $request->semester,
            'tahun_pelajaran' => $request->tahun_pelajaran
        ]);

        return response()->json(['message' => 'Data semester berhasil diperbaharui!', 'status' => 200], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(String $semester)
    {
        $data = $this->semester->where('id', $semester)->first();
        $data->delete();
        return response()->json(['data' => $semester, 'message' => 'Data semester berhasil ditambahkan!', 'status' => 200], 200);
    }
}
