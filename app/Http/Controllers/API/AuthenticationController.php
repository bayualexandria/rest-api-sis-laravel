<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\{Guru, User, Siswa};
use App\Notifications\EmailVerification;
use App\Notifications\VerificationEmailSuccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthenticationController extends Controller
{
    function register(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required',
            'username' => 'required|unique:users|numeric',
            'email' => 'email:tls|required|unique:users',
            'password' => 'required|same:cpassword|min:8',
            'cpassword' => 'required|same:password|min:8',
        ], [
            'name.required' => 'Nama harus diisi',
            'username.required' => 'NIS harus diisi',
            'username.unique' => 'NIS harus didaftarkan',
            'username.numeric' =>
            'Yang anda masukan bukan NIS',
            'email.email' =>
            'Yang anda masukan bukan email',
            'email.required' =>
            'Email harus diisi',
            'email.unique' => 'Email sudah didaftarkan',
            'password.required'
            => 'Password harus diisi',
            'cpassword.required' => 'Konfirmasi password harus diisi',
            'password.same' => 'Password tidak sama dengan konfirmasi password',
            'cpassword.same' => 'Konfirmasi password tidak sama dengan password',
            'password.min' =>
            'Password minimal 8 karakter',
            'cpassword.min' => 'Konfirmasi password minimal 8 karakter',
        ]);

        if ($validation->fails()) {
            return response()->json(['message' => $validation->errors(), 'status' => 403], 403);
        }
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'status_id' => 2

        ]);
        // Siswa::create(['nis' => $request->username]);
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->plainTextToken;
        $user->notify(new EmailVerification($user));

        return response()->json([
            'data' => $user,
            'message' => "User dengan nama {$request->name} berhasil ditambahkan! Silahkan cek email untuk verifikasi",
            'accessToken' => $token,
        ], 201);
    }

    // Logic Login Siswa
    public function login(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'username' => 'required|numeric',
            'password' => 'required'
        ], [
            'username.required' => 'NIS harus diisi',
            'username.numeric' => 'Yang anda masukan bukan NIS',
            'password.required' => 'Password harus diisi'
        ]);
        if ($validation->fails()) {
            return response()->json(['message' => $validation->errors(), 'status' => 401], 401);
        }
        $user = User::where('username', $request->username)->first();
        if ($user) {
            if ($user->status_id == 3) {
                if ($user->email_verified_at) {

                    if (password_verify($request->password, $user->password)) {

                        $tokenResult = $user->createToken('Personal Access Token');
                        $token = $tokenResult->plainTextToken;
                        $siswa = Siswa::where('nis', $user->username)->first();
                        if (!$siswa) {
                            Siswa::create(['nis' => $user->username, 'nama' => $user->name]);
                            return response()->json([
                                'user' => $user,
                                'accessToken' => $token,
                                'token_type' => 'Bearer',
                                'status' => 200
                            ], 200);
                        }
                        return response()->json([
                            'user' => $user,
                            'accessToken' => $token,
                            'token_type' => 'Bearer',
                            'status' => 200
                        ], 200);
                    }
                    return response()->json(['message' => 'Password yang anda masukan salah!', 'status' => 403], 403);
                }
                $user->notify(new EmailVerification($user));
                return response()->json(['message' => 'Akun belum terverifikasi! Silahkan cek email untuk verifikasi.', 'status' => 403], 403);
            }
            return response()->json(['message' => 'Akun ini tidak mempunyai akses diportal ini', 'status' => 403], 403);
        }
        return response()->json(['message' => 'NIS belum terdaftar', 'status' => 403], 403);
    }

    public function admin(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'username' => 'required|numeric',
            'password' => 'required'
        ], [
            'username.required' => 'Username atau NIP harus diisi',
            'username.numeric' => 'Yang anda masukan bukan username atau NIP',
            'password.required' => 'Password harus diisi'
        ]);
        if ($validation->fails()) {
            return response()->json(['message' => $validation->errors(), 'status' => 401], 401);
        }
        $user = User::where('username', $request->username)->first();
        if ($user) {
            if ($user->status_id != 3) {
                if ($user->email_verified_at) {

                    if (password_verify($request->password, $user->password)) {

                        $tokenResult = $user->createToken('Personal Access Token');
                        $token = $tokenResult->plainTextToken;
                        $guru = Guru::where('nip', $user->username)->first();
                        if (!$guru && $user->status_id == 2) {
                            Guru::create(['nip' => $user->username, 'nama' => $user->name, 'image_profile' => 'assets/images/users.png']);
                            return response()->json([
                                'user' => $user,
                                'accessToken' => $token,
                                'token_type' => 'Bearer',
                                'status' => 200
                            ], 200);
                        }
                        return response()->json([
                            'user' => $user,
                            'accessToken' => $token,
                            'token_type' => 'Bearer',
                            'status' => 200
                        ], 200);
                    }
                    return response()->json(['message' => 'Password yang anda masukan salah!', 'status' => 403], 403);
                }
                $user->notify(new EmailVerification($user));
                return response()->json(['message' => 'Akun belum terverifikasi! Silahkan cek email untuk verifikasi.', 'status' => 403], 403);
            }
            return response()->json(['message' => 'Akun ini tidak mempunyai akses diportal ini', 'status' => 403], 403);
        }
        return response()->json(['message' => 'User atau NIP belum terdaftar', 'status' => 403], 403);
    }



    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json([
            'message' => "Akun anda berhasil logout!",
            'status' => 200
        ], 200);
    }
    public function emailVerify($email)
    {
        $user = User::where('email', $email)->first();
        $verify = $user->update(['email_verified_at' => date('Y-m-d h:m:t')]);
        $user->notify(new VerificationEmailSuccess($user));

        return response()->json(['data' => $verify, 'message' => 'Akun berhasil terverifikasi!', 'status' => 200, 'date' => date('Y-m-d h:m:t')], 200);
    }
}
