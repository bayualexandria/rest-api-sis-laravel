<?php

use App\Http\Controllers\API\{
    AuthenticationController,
    GuruController,
    HomeSchoolController,
    KelasController,
    MapelController,
    SemesterController,
    SiswaController,
    UserController
};
use App\Models\User;
use App\Notifications\ForgetPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

// Authentication verification email and reset password
Route::controller(AuthenticationController::class)->prefix('auth')->group(function () {
    // Login Portal Siswa
    Route::post('login', 'login');
    // Login Portal Admin
    Route::post('login-admin', 'admin');
    Route::post('register', 'register');
    Route::get('verify/{email}', 'emailVerify');
    Route::post('/forgot-password', function (Request $request) {
        $validation = Validator::make($request->all(), [
            'email' => 'email:tls|required',
        ], [

            'email.email' =>
            'Yang anda masukan bukan email',
            'email.required' =>
            'Email harus diisi',

        ]);
        if ($validation->fails()) {
            return response()->json(['message' => $validation->errors(), 'status' => 403], 403);
        }

        $user = User::where('email', $request->email)->first();
        $forgetPass = DB::table('password_reset_tokens')->where('email', $request->email)->first();
        $token = hash('sha256', Str::random(60));
        $password = Str::random(8);
        if (!$user) {
            return response()->json(['message' => "Email yang anda masukan belum terdaftar!", 'status' => 403], 403);
        }

        if (!$forgetPass) {
            DB::table('password_reset_tokens')->insert(['email' => $request->email, 'token' => $token]);
            $user->update(['password' => bcrypt($password)]);
            $user->notify(new ForgetPassword($user, $token, $password));
            return response()->json(['message' => "Anda berhasil melakukan reset password. Silahkan cek email anda untuk melihat pesan yang terkirim!", 'status' => 200], 200);
        }

        $user->update(['password' => bcrypt($password)]);
        DB::table('password_reset_tokens')->where('email', $request->email)->update(['token' => $token]);
        $user->notify(new ForgetPassword($user, $token, $password));

        return response()->json(['message' => "Anda berhasil melakukan reset password. Silahkan cek email anda untuk melihat pesan yang terkirim!", 'status' => 200], 200);
    });

    Route::get('reset/{email}/{token}', function ($email, $token) {
        $dataToken = DB::table('password_reset_tokens')->where('email', $email)->first();
        if ($dataToken->email != $email) {
            return response()->json(['message' => 'Email anda tidak valid!', 'status' => 403], 403);
        }
        if ($token != $dataToken->token) {
            return response()->json(['message' => 'Token anda tidak valid!', 'status' => 403], 403);
        }
        return response()->json(['email' => $email])->isRedirect("/reset-password");
    });

    // Route::post('reset-password/{email}', function ($mail) {});
});



// Profile Sekolah
Route::controller(HomeSchoolController::class)->prefix('sekolah')->group(function () {
    Route::get('/',  'getDataSekolah');
    Route::put('/',  'updateDataSekolah');
});



Route::middleware(['auth:sanctum'])->group(function () {
    Route::middleware(['isAdmin'])->group(function () {

        // Get All Users
        Route::get('user', function () {
            $users = User::all();
            return response()->json(['data' => $users, 'message' => 'Get data success', 'status' => 200], 200);
        });

        // Get By Id User
        Route::get('user/{username}', function ($username) {
            $user = User::where('username', $username)->first();
            if ($user) {
                return response()->json(['data' => $user, 'message' => 'Data berhasil ditampilkan', 'status' => 200], 200);
            }
            return response()->json(['message' => 'Data tidak ditemukan', 'status' => 404], 404);
        });

        // Start Guru
        Route::controller(GuruController::class)->prefix('guru')->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store');
            Route::get('restore', 'restoreDataGuru');
            Route::get('data-trash', 'showDeleteDataGuru');
            Route::delete('delete-permanent', 'deletePermanenDataGuru');
            Route::get('{nip}', 'show');
            Route::post('{nip}', 'update');
            Route::delete('{nip}', 'destroy');
            Route::delete('delete/{nip}', 'deletePermanenDataGuruById');
            Route::get('restore/{nip}', 'restoreDataGuruById');
        });
        // End Guru

        // Start Mapel
        Route::controller(MapelController::class)->prefix('mapel')->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store');
            Route::get('{id}', 'show');
            Route::put('{id}', 'update');
            Route::delete('{id}', 'destroy');
        });
        // End Mapel

        // Start Kelas
        Route::controller(KelasController::class)->prefix('kelas')->group(function () {
            Route::get('/', 'index');
            Route::get('data-kelas', 'getKelas');
            Route::post('/', 'store');
            Route::get('{id}', 'show');
            Route::get('guru/{nip}', 'getDataKelasByIdGuru');
            Route::put('{id}', 'update');
            Route::delete('{id}', 'destroy');
        });
        // End Kelas

        // Start User guru by id
        Route::get('user/{username}/guru', function ($username) {
            $user = User::where('username', $username)->first();
            return response()->json([
                'data' => [
                    'name' => $user->name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'status_id' => $user->status_id,
                    'guru' => [
                        'nip' => $user->guru->nip,
                        'nama' => $user->guru->nama,
                        'jenis_kelamin' => $user->guru->jenis_kelamin,
                        'no_hp' => $user->guru->no_hp,
                        'alamat' => $user->guru->alamat,
                        'image_profile' => $user->guru->image_profile
                    ]
                ],
                'message' => 'Get data success',
                'status' => 200
            ], 200);
        });
        // End User guru by id

        // Start Semester
        Route::controller(SemesterController::class)->prefix('semester')->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store');
            Route::put('{id}', 'update');
            Route::delete('{id}', 'destroy');
        });
        // End Semester
    });

    // Start Siswa
    Route::controller(SiswaController::class)->prefix('siswa')->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('restore', 'restoreDataSiswa');
        Route::get('data-trash', 'showDeleteDataSiswa');
        Route::get('kelas', 'getAllSiswaByKelas');
        Route::post('kelas', 'insertSiswaByKelas');
        Route::delete('delete-permanent', 'deletePermanenDataSiswa');
        Route::get('{nis}', 'show');
        Route::post('{nis}', 'update');
        Route::delete('{nis}', 'destroy');
        Route::delete('delete/{nis}', 'deletePermanenDataSiswaById');
        Route::get('restore/{nis}', 'restoreDataSiswaById');
        Route::post('changeEmail/{nis}', 'changeEmailSiswa');
        Route::post('changePassword/{nis}', 'changePasswordSiswa');
    });
    // End Siswa

    // Start User siswa by id
    Route::get('user/{username}/siswa', function ($username) {
        $user = User::where('username', $username)->first();
        return response()->json([
            'data' => [
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'siswa' => [
                    'nis' => $user->siswa->nis,
                    'nama' => $user->siswa->nama,
                    'jenis_kelamin' => $user->siswa->jenis_kelamin,
                    'no_hp' => $user->siswa->no_hp,
                    'alamat' => $user->siswa->alamat,
                    'image_profile' => $user->siswa->image_profile,
                ]
            ],
            'message' => 'Get data success',
            'status' => 200
        ], 200);
    });
    // End User siswa by id

    Route::post('change-password/{username}', [UserController::class, 'changePassword']);
    Route::get('logout', [AuthenticationController::class, 'logout']);
});
