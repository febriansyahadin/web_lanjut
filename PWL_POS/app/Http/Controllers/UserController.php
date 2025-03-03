<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\UserModel;

class UserController extends Controller
{
    public function index()
    {
       /* $data = [
          'username' => 'customer-1',
          'nama' => 'Pelanggan',
          'password' => Hash::make('12345'),
          'level_id' => 4
        ];

      $data = [
        'nama' => 'Pelanggan Pertama'
      ];
        $data = [
           'level_id' => 2,
           'username' => 'manager_tiga',
         'nama' => 'Manager 3',
          'password' => Hash::make('12345')
      ];
        UserModel::create($data);
        UserModel::where('username', 'customer-1')->update($data);*/

        
        $users = UserModel::all();
        return view('user', ['data' => $users]);
    }
        public function tambah()
        {
          return view('user_tambah');
        }

        public function tambah_simpan(Request $request)
        {
          UserModel::create([
            'username' => $request->username,
            'nama' => $request->nama,
            'password' => Hash::make('$request->password'),
            'level_id' =>$request->level_id
          ]);
          return redirect('/user');
        }

        public function ubah($id){
          $user = UserModel::find($id);
          return view('user_ubah', ['data' => $user]);
        }

        public function ubah_simpan($id, Request $request){
          $users = UserModel::find($id);

          $users->username = $request->username;
          $users->nama = $request->nama;
          $users->password = Hash::make('$request->password');
          $users->level_id = $request->level_id;

          $users->save();
          return redirect('/user');
        }

        public function hapus($id){
          $users = UserModel::find($id);
          $users->delete();
          return redirect('/user');
        }
      }