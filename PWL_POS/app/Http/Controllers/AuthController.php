<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserModel; 
use App\Models\LevelModel; 
use Illuminate\Support\Facades\Hash; 

class AuthController extends Controller
{
    public function login (){
        if (Auth::check()) {
            return redirect('/');
        }
        return view('auth.login');
    }
    public function postLogin(Request $request){
        // Validate the request
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Check if the request is AJAX
        if ($request->ajax() || $request->wantsJson()) {
            $credentials = $request->only('username', 'password');
            if (Auth::attempt($credentials)) {
                return response()->json([
                    'status' => true,
                   'message' => 'Login success',
                   'redirect' => url('/'),
                ]);
            }
            return response()->json([
                'status' => false,
               'message' => 'Login gagal',
            ]);
        }

        // If not an AJAX request, redirect back to login
        return redirect('login')->withErrors(['login' => 'Invalid credentials']);
    }
    public function logout(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('login');
    }

    // Show the registration form
    public function register()
    {
        return view('auth.register');
    }

    public function postRegister(Request $request)
    {
        $request->validate([
            'username' => 'required|string|unique:m_user,username|min:4|max:20',
            'password' => 'required|string|min:6|max:20|confirmed',
            'nama' => 'required|string|max:100',
        ]);
    
        // Cek apakah level "Customer" tersedia di database
        $customerLevel = LevelModel::where('level_nama', 'Customer')->first();
        if (!$customerLevel) {
            return response()->json([
                'status' => false,
                'message' => 'Level Customer tidak ditemukan!',
            ]);
        }
    
        // Buat user baru
        $user = UserModel::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'nama' => $request->nama,
            'level_id' => $customerLevel->level_id,
        ]);
    
        // Coba login user setelah registrasi
        if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            return response()->json([
                'status' => true,
                'message' => 'Registrasi berhasil!',
                'redirect' => url('/'),
            ]);
        }
    
        return response()->json([
            'status' => false,
            'message' => 'Registrasi berhasil, tetapi gagal login otomatis. Silakan login secara manual.',
        ]);
    }
    
}