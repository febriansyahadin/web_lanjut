<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\SupplierModel;

class SupplierController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Supplier',
            'list' => ['Home', 'Supplier']
        ];

        $page = (object) [
            'title' => 'Daftar Supplier yang terdaftar di sistem'
        ];

        $activeMenu = 'Supplier';
        $supplier = SupplierModel::all(); // Ambil data dengan model

        return view('supplier.index', compact('breadcrumb', 'page', 'activeMenu', 'supplier'));
    }

    public function list(Request $request)
    {
        $supplier = SupplierModel::all(); // Mengambil semua data supplier tanpa filter
    
        return DataTables::of($supplier)
            ->addIndexColumn()
            ->addColumn('aksi', function ($supplier) {
                return '<a href="' . url('supplier/' . $supplier->supplier_id) . '" class="btn btn-info btn-sm">Detail</a> '
                    . '<a href="' . url('supplier/' . $supplier->supplier_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a> '
                    . '<form class="d-inline-block" method="POST" action="' . url('supplier/' . $supplier->supplier_id) . '">'
                    . csrf_field() . method_field('DELETE') . '
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakin menghapus data ini?\');">Hapus</button>
                    </form>';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }
    

    public function create()
    {
        $breadcrumb = (object) [
            'title' => 'Tambah supplier',
            'list' => ['Home', 'supplier', 'Tambah']
        ];

        $page = (object) [
            'title' => 'Tambah supplier Baru'
        ];

        $activeMenu = 'supplier';
        $supplier = SupplierModel::all();

        return view('supplier.create', compact('breadcrumb', 'page', 'activeMenu', 'supplier'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'supplier_nama'   => 'required|string|max:100',
            'supplier_alamat' => 'required|string',
            'supplier_kontak' => 'required|string|max:15|unique:m_supplier,supplier_kontak',
            'supplier_email'  => 'required|email|unique:m_supplier,supplier_email'
        ]);

        // Simpan data ke database
        SupplierModel::create([
            'supplier_nama'   => $request->supplier_nama,
            'supplier_alamat' => $request->supplier_alamat,
            'supplier_kontak' => $request->supplier_kontak,
            'supplier_email'  => $request->supplier_email
        ]);

        // Redirect dengan pesan sukses
        return redirect('/supplier')->with('success', 'Data supplier berhasil disimpan');
    }

    public function show($id)
    {
        $supplier = SupplierModel::find($id);

        $breadcrumb = (object) [
            'title' => 'Detail supplier',
            'list' => ['Home', 'supplier', 'Detail']
        ];

        $page = (object) [
            'title' => 'Detail supplier',
        ];

        $activeMenu = 'supplier'; //set menu yang sedang aktif

        return view('supplier.show', compact('breadcrumb', 'page', 'supplier', 'activeMenu'));
    }

    public function edit($id)
    {
        $breadcrumb = (object) [
            'title' => 'Edit supplier',
            'list' => ['Home', 'supplier', 'Edit']
        ];

        $page = (object) [
            'title' => 'Edit supplier'
        ];

        $activeMenu = 'supplier';
        $supplier = SupplierModel::findOrFail($id);

        return view('supplier.edit', compact('breadcrumb', 'page', 'activeMenu' ,'supplier'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'supplier_nama'   => 'required|string|max:100',
            'supplier_alamat' => 'required|string',
            'supplier_kontak' => 'required|string|max:15|unique:m_supplier,supplier_kontak,' . $id . ',supplier_id',
            'supplier_email'  => 'required|email|unique:m_supplier,supplier_email,' . $id . ',supplier_id'
        ]);

        SupplierModel::findOrFail($id)->update($request->all());

        return redirect('/supplier')->with('success', 'Data supplier berhasil diubah!');
    }

    public function destroy($id)
    {
        $check = SupplierModel::find($id);
        if (!$check) {
            return redirect('/supplier')->with('error', 'Data supplier tidak ditemukan');
        }

        try {
            SupplierModel::destroy($id);
            return redirect('/supplier')->with('success', 'Data supplier berhasil dihapus');
        } catch (\Exception $e) {
            return redirect('/supplier')->with('error', 'Data supplier gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
        }
    }
}
