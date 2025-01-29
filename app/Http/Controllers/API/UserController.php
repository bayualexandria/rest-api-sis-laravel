<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\ChangedPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    private $user;

    function __construct()
    {
        $this->user = new User();
    }

    function changePassword($username, Request $request)
    {
        $validation = Validator::make($request->all(), [
            'oldPassword' => 'required',
            'newPassword' => 'required|min:8|same:confNewPassword',
            'confNewPassword' => 'required|min:8'
        ], [
            'oldPassword.required' => 'Password lama harus diisi',
            'newPassword.required' => 'Password baru harus diisi',
            'newPassword.min' => 'Password minimal 8 karakter',
            'newPassword.same' => 'Password baru tidak sama dengan konfirmasi password baru',
            'confNewPassword.required' => 'Konfirmasi password baru harus diisi',
            'confNewPassword.min' => 'Konfirmasi password minimal 8 karakter',
        ]);

        if ($validation->fails()) return response()->json(['message' => $validation->errors(), 'status' => 401], 401);

        $user = $this->user->where('username', $username)->first();

        if (!password_verify($request->oldPassword, $user->password)) return response()->json(['message' => "Password lama yang anda masukan tidak sesuai.", 'status' => 403], 403);

        if (password_verify($request->newPassword, $user->password)) return response()->json(['message' => "Password baru tidak boleh sama dengan password lama.", 'status' => 403], 403);

        $user->update(['password' => bcrypt($request->newPassword)]);
        $user->notify(new ChangedPassword($user, $request->newPassword));
        return response()->json(['message' => "Password telah diperbaharui", 'status' => 200], 200);
    }
}
