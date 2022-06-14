<?php

namespace App\Http\Controllers\API;

use App\Actions\Fortify\PasswordValidationRules;
use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    use PasswordValidationRules; //app - actions - fortify

    //fungsi login
    public function login(Request $request)
    {
        //membuat validasi dengan try cath
        try{
            //validasi input
            $request->validate([
                'email' => 'email|required', //apakah email(juga untuk ngecek) masuk atau tidak
                'password' => 'required' //cek apakah ada data atau tidak
            ]);

            //mengecek credentials login
            $credentials = $request(['email','password']);

            //jika error login
            if(!Auth::attempt($credentials)){
                return ResponseFormatter::error([
                    'message' => 'Unauthorized'
                ], 'Authentication Failed', 500);
            }

            //jika hash tidak sesusai maka error
            $user = User::where('email', $request->email)->first();
            if(!Hash::chech($request->password, $user->password, [])) //untuk mengecek apakah password yang dimasukan user sama, apakah sesuai dengan memasukan password
            {
                throw new \Exception('Invalid Credentials');
            }

            //jika berhasil login
            $tokenResult = $user->createToken('authToken')->plainTextToken; //mobile -> plainTextToken
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'Authenticated');

        } catch(Exception $error){
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error
            ], 'Authentication Failed', 500);
        }
    }

    //fungsi register
    public function register (Request $request)
    {
        try {
            //validasi
            $request -> validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => $this->passwordRules()
            ]);

            //membuat data user
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'address' => $request->address,
                'postalCode' => $request->postalCode,
                'phoneNumber' => $request->phoneNumber,
                'city' => $request->city,
                'password' => Hash::make($request->password),
            ]);

            //mengabil data email
            $user = User::where('email', $request->email)->first();

            //mengambil token
            $tokenResult = $user->createToken('authToken')->plainTextToken;

            //validasi token
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ]);

        } 

        //jika gagal
        catch (Exception $error){
            //throw $th
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error
            ], 'Authentication Failed', 500);
        }
    }

    //fungsi logut
    public function logout(Request $request)
    {
        //Mengambil token yang sedang diakses, delete untuk kegiatan logout
        $token = $request->user()->currentAccessToken()->delete();

        return ResponseFormatter::success($token, 'Token Revoked');
    }

    //Ambil data user
    public function fetch(Request $request)
    {
        return ResponseFormatter::success($request->user(),'Data profile user berhasil diambil');
    }

    //fungsi update profile
    public function updateProfile(Request $request)
    {
        $data = $request->all(); // mengambil semua request yang masuk di updateprofile, dan disesuaikan di db users

        $user = Auth::user();
        $user->update($data);
        

        return ResponseFormatter::success($user, 'Profile Updated');

    }

    //fungsi update foto
    public function updatePhoto(Request $request)
    {
        //validasi gambar yang dibutuhkan upload
        $validator = Validator::make($request->all(), [
            'file' => 'required|image|max:2048',
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error(
                ['error'=>$validator->errors()], 
                'Update Photo Fails', 401);
        }

        if ($request->file('file')) {

            $file = $request->file->store('assets/user', 'public');

            //simpan foto ke db urlnya
            $user = Auth::user();
            $user->profile_photo_path = $file;
            $user->update();

            return ResponseFormatter::success([$file],'File successfully uploaded');
        }
    }
    
}
