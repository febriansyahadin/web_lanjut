<?php

namespace App\Http\Controllers;

use App\Models\BarangModel;
use App\Models\KategoriModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

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
public function list (Request $request)
{
    $barang = BarangModel::select('barang_id', 'barang_kode', 'barang_nama','harga_beli', 'harga_jual','kategori_id')
        ->with('kategori');

        //Filter sata user berdasarkan kategori_id
        if ($request->kategori_id) {
            $barang->where('kategori_id', $request->kategori_id);
        }

    // menambahkan kolom index / no urut (default nama kolom: DT_RowIndex)
    return DataTables::of($barang)
        ->addIndexColumn()
        ->addColumn('aksi', function ($barang) { // menambahkan kolom aksi
            $btn = '<a href="'.url('/barang/'.$barang->barang_id).'" class="btn btn-info btn-sm">Detail</a>';
            $btn .= '<a href="'.url('/barang/'.$barang->barang_id.'/edit').'" class="btn btn-warning btn-sm">Edit</a>';
            $btn .= '<form class="d-inline-block" method="POST" action="'.url('/barang/'.$barang->barang_id).'">'.
                        csrf_field().
                        method_field('DELETE').
                        '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakin menghapus data ini?\');">Hapus</button>'.
                    '</form>';
            return $btn;
        })
        ->rawColumns(['aksi']) // memberitahu bahwa kolom aksi adalah html
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
}     