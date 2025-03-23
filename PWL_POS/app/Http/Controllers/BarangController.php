<?php

namespace App\Http\Controllers;

use App\Models\BarangModel;
use App\Models\KategoriModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class BarangController extends Controller
{
    public function index()
    {

      //menampilkan halaman awal Barang
      $breadcrumb = (object) [
          'title' => 'Daftar Barang',
          'list' => ['Home', 'Barang']
      ];

      $page = (object) [
        'title' => 'Daftar barang yang terdaftar dalam sistem',
      ];

      $activeMenu = 'barang'; //set menu yang sedang aktif

      $kategori = KategoriModel::all(); //ambil data level untuk filter level
      return view('barang.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'kategori' => $kategori, 'activeMenu' => $activeMenu]);
    }

    // Ambil data user dalam bentuk json untuk datatables
    public function list(Request $request)
    {
        $barang = BarangModel::select(
                'barang_id',
                'barang_kode',
                'barang_nama',
                'harga_beli',
                'harga_jual',
                'kategori_id'
            )
            ->with('kategori');
    
        // Filter data user berdasarkan kategori_id
        if ($request->kategori_id) {
            $barang->where('kategori_id', $request->kategori_id);
        }
    
        // Menambahkan kolom index / no urut (default nama kolom: DT_RowIndex)
        return DataTables::of($barang)
            ->addIndexColumn()
            ->addColumn('aksi', function ($barang) {
                // Menambahkan kolom aksi
                 $btn = '<a href="'.url('barang/'.$barang->barang_id).'" class="btn btn-info btn-sm">Detail</a> ';
                $btn .= '<button onclick="modalAction(\''.url('/barang/' . $barang->barang_id .
                '/edit_ajax').'\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\''.url('/barang/' . $barang->barang_id .
                '/delete_ajax').'\')" class="btn btn-danger btn-sm">Hapus</button> ';
    
                return $btn;
            })
            ->rawColumns(['aksi']) // Memberitahu bahwa kolom aksi adalah HTML
            ->make(true);
    }
    

  public function create()
  {
    $breadcrumb = (object) [
        'title' => 'Tambah barang',
        'list' => ['Home', 'Barang', 'Tambah']
    ];

    $page = (object) [
        'title' => 'Tambah Barang baru',
    ];

    $kategori = KategoriModel::all();
    $activeMenu = 'barang'; //set menu yang sedang aktif

    return view('barang.create', ['breadcrumb' => $breadcrumb, 'page' => $page, 'kategori'=>$kategori, 'activeMenu' => $activeMenu]);
  }
  // Menyimpan data user baru
    public function store(Request $request)
  {
    $request->validate([
        // username harus diisi, berupa string, minimal 3 karakter, dan bernilai unik di tabel m_user kolom username
        'barang_kode' => 'required|string|max:50',
        'barang_nama' => 'required|string|max:100',
        'harga_beli' => 'required|integer',
        'harga_jual' => 'required|integer',
        'kategori_id' => 'required|integer' // level_id harus diisi dan berupa angka
    ]);

    BarangModel::create([
        'barang_kode' => $request->barang_kode,
        'barang_nama' => $request->barang_nama,
        'harga_beli' => $request->harga_beli,
        'harga_jual' => $request->harga_jual,
        'kategori_id' => $request->kategori_id
    ]);

    return redirect('/barang')->with('success', 'Data Barang berhasil disimpan');
  }

  public function show($id)
  {
    $barang = BarangModel::with('kategori')->find($id);
    
    $breadcrumb = (object) [
        'title' => 'Detail Barang',
        'list' => ['Home', 'Barang', 'Detail']
    ];

    $page = (object) [
        'title' => 'Detail Barang',
    ];

    $activeMenu = 'Barang'; //set menu yang sedang aktif

    return view('barang.show', ['breadcrumb' => $breadcrumb, 'page' => $page, 'barang' => $barang, 'activeMenu' => $activeMenu]);
  }

  // Menampilkan halaman form edit user
    public function edit(string $id)
    {
        $barang = BarangModel::find($id);
        $kategori = KategoriModel::all();

        $breadcrumb = (object) [
            'title' => 'Edit Barang',
            'list' => ['Home', 'Barang', 'Edit']
        ];

        $page = (object) [
            'title' => 'Edit Barang'
        ];

        $activeMenu = 'barang'; // set menu yang sedang aktif

        return view('barang.edit', ['breadcrumb' => $breadcrumb, 'page' => $page, 'barang' => $barang, 'kategori' => $kategori, 'activeMenu' => $activeMenu]);
    }

    // Menyimpan perubahan data user
    public function update(Request $request, string $id)
    {
        $request->validate([
            // username harus diisi, berupa string, minimal 3 karakter,
            // dan bernilai unik di tabel m_user kolom username kecuali untuk user dengan id yang sedang diedit
            'barang_kode' => 'required|string|min:3|unique:m_barang,barang_kode,'.$id.',barang_id',
            'barang_nama' => 'required|string|max:100', // nama harus diisi, berupa string, dan maksimal 100 karakter
            'harga_beli' => 'required|integer',
            'harga_jual' => 'required|integer',
            'kategori_id' => 'required|integer' // level_id harus diisi dan berupa angka
        ]);

        BarangModel::find($id)->update([
            'barang_kode' => $request->barang_kode,
            'barang_nama' => $request->barang_nama,
            'harga_beli' => $request->harga_beli,
            'harga_jual' => $request->harga_jual,
            'kategori_id' => $request->kategori_id
        ]);

        return redirect('/barang')->with('success', 'Data barang berhasil diubah');
    }

        // Menghapus data user
    public function destroy(string $id)
    {
        $check = BarangModel::find($id);

        if (!$check) { // untuk mengecek apakah data user dengan id yang dimaksud ada atau tidak
            return redirect('/barang')->with('error', 'Data barang tidak ditemukan');
        }

        try {
            BarangModel::destroy($id); // Hapus data user
            return redirect('/barang')->with('success', 'Data user berhasil dihapus');
        } catch (\Illuminate\Database\QueryException $e) {
            // Jika terjadi error ketika menghapus data, redirect kembali ke halaman dengan membawa pesan error
            return redirect('/barang')->with('error', 'Data user gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
        }
    }

    public function create_ajax()
    {
        $barang = BarangModel::select('barang_id', 'barang_nama')->get();
        $kategori = KategoriModel::select('kategori_id', 'kategori_nama')->get(); // Tambahkan ini
    
        return view('barang.create_ajax', compact('barang', 'kategori'));
    }
    
    

    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'barang_kode' => 'required|string|max:10|unique:m_barang,barang_kode',
                'barang_nama' => 'required|string|max:100',
                'harga_jual' => 'required|numeric',
                'harga_beli' => 'required|numeric',
                'kategori_id' => 'required|integer'
            ];
    
            $validator = Validator::make($request->all(), $rules);
    
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }
    
            // Create the barang
            BarangModel::create($request->all());
    
            return response()->json([
                'status' => true,
                'message' => 'Data barang berhasil disimpan',
            ]);
        }
    
        return redirect('/');
    }
    public function edit_ajax(string $id)
    {
        $barang = BarangModel::find($id);
        $kategori = KategoriModel::all(); // Ambil semua kategori
    
        if (!$barang) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }
    
        return view('barang.edit_ajax', compact('barang', 'kategori')); // Kirim kategori ke view
    }
    
    public function update_ajax(Request $request, $id){
        // Check if the request is from ajax
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'barang_kode' => 'required|string|max:10|unique:m_barang,barang_kode,'.$id.',barang_id',
                'barang_nama' => 'required|string|max:100',
                'harga_jual' => 'required|numeric',
                'harga_beli' => 'required|numeric',
                'kategori_id' => 'required|integer'
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed.',
                    'msgField' => $validator->errors()
                ]);
            }
            $check = BarangModel::find($id);
            if ($check) {
                $check->update($request->all());
                return response()->json([
                    'status' => true,
                    'message' => 'Data successfully updated'
                ]);
            } else{
                return response()->json([
                    'status' => false,
                    'message' => 'Data not found'
                ]);
            }
        }
        return redirect('/');
    }

    public function confirm_ajax(string $id)
    {
        $barang =BarangModel::find($id);

        return view('barang.confirm_ajax', ['barang' => $barang]);
    }
    
    public function delete_ajax(Request $request, string $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $barang = BarangModel::find($id);
            if ($barang) {
                $barang->delete();
                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil dihapus'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }
        }
        return redirect('/');
    }
}