<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\UserModel;

class UserController extends Controller
{
    public function index()
    {
       // $data = [
         //   'username' => 'customer-1',
         //   'nama' => 'Pelanggan',
          //  'password' => Hash::make('12345'),
           // 'level_id' => 4
      //  ];

      $data = [
        'nama' => 'Pelanggan Pertama'
      ];
      UserModel::where('username', 'customer-1')->update($data);

        $users = UserModel::all();
        return view('user', ['data' => $users]);
    }
}
