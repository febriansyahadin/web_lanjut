<?php

namespace App\Http\Controllers;

use App\Models\KategoriModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class KategoriController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Kategori',
            'list' => ['Home', 'Kategori']
        ];
    
        $page = (object) [
            'title' => 'Daftar Kategori yang terdaftar di sistem'
        ];
    
        $activeMenu = 'kategori';
        $kategori = KategoriModel::all(); // Ambil data dengan model
    
        return view('kategori.index', compact('breadcrumb', 'page', 'activeMenu', 'kategori'));
    }
    
    public function list(Request $request)
    {
        $kategori = KategoriModel::select('kategori_id', 'kategori_kode', 'kategori_nama');

        if ($request->kategori_id) { // Sesuaikan dengan nama filter dari view
            $kategori->where('kategori_id', $request->kategori_id);
        }

        return DataTables::of($kategori)
            ->addIndexColumn()
            ->addColumn('aksi', function ($kategori) {
                /*return '<a href="' . url('kategori/' . $kategori->kategori_id) . '" class="btn btn-info btn-sm">Detail</a> '
                    . '<a href="' . url('kategori/' . $kategori->kategori_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a> '
                    . '<form class="d-inline-block" method="POST" action="' . url('kategori/' . $kategori->kategori_id) . '">'
                    . csrf_field() . method_field('DELETE') . '
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakin menghapus data ini?\');">Hapus</button>
                    </form>';*/
                    $btn  = '<button onclick="modalAction(\''.url('/kategori/' . $kategori->kategori_id . 
                    '/show_ajax').'\')" class="btn btn-info btn-sm">Detail</button> '; 
                                $btn .= '<button onclick="modalAction(\''.url('/kategori/' . $kategori->kategori_id . 
                    '/edit_ajax').'\')" class="btn btn-warning btn-sm">Edit</button> '; 
                                $btn .= '<button onclick="modalAction(\''.url('/kategori/' . $kategori->kategori_id . 
                    '/delete_ajax').'\')"  class="btn btn-danger btn-sm">Hapus</button> '; 
                     
                    return $btn;  
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create()
    {
        $breadcrumb = (object) [
            'title' => 'Tambah Kategori',
            'list' => ['Home', 'kategori', 'Tambah']
        ];

        $page = (object) [
            'title' => 'Tambah kategori Baru'
        ];

        $activeMenu = 'kategori';
        $kategori = KategoriModel::all();

        return view('kategori.create', compact('breadcrumb', 'page', 'activeMenu', 'kategori'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kategori_kode' => 'required|string|unique:m_kategori,kategori_kode',
            'kategori_nama' => 'required|string|max:100'
        ]);
    
        KategoriModel::create([
            'kategori_kode' => $request->kategori_kode,
            'kategori_nama' => $request->kategori_nama
        ]);
    
        return redirect('/kategori')->with('success', 'Data user berhasil disimpan');
    }
    

    public function show($id)
    {
      $kategori = KategoriModel::find($id);
      
      $breadcrumb = (object) [
          'title' => 'Detail kategori',
          'list' => ['Home', 'kategori', 'Detail']
      ];
  
      $page = (object) [
          'title' => 'Detail kategori',
      ];
  
      $activeMenu = 'kategori'; //set menu yang sedang aktif
  
      return view('kategori.show', compact('breadcrumb', 'page', 'kategori', 'activeMenu'));
    }

    public function edit($id)
    {
        $breadcrumb = (object) [
            'title' => 'Edit kategori',
            'list' => ['Home', 'kategori', 'Edit']
        ];

        $page = (object) [
            'title' => 'Edit kategori'
        ];

        $activeMenu = 'kategori';
        $kategori = KategoriModel::findOrFail($id);

        return view('kategori.edit', compact('breadcrumb', 'page', 'activeMenu', 'kategori'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kategori_kode' => 'required|unique:m_kategori,kategori_kode,' . $id . ',kategori_id',
            'kategori_nama' => 'required'
        ]);

        KategoriModel::findOrFail($id)->update($request->all());

        return redirect('/kategori')->with('success', 'Data kategori berhasil diubah!');
    }

    public function destroy($id)
    {
        $check = KategoriModel::find($id);
        if (!$check) {
            return redirect('/kategori')->with('error', 'Data kategori tidak ditemukan');
        }

        try {
           KategoriModel::destroy($id);
            return redirect('/kategori')->with('success', 'Data kategori berhasil dihapus');
        } catch (\Exception $e) {
            return redirect('/kategori')->with('error', 'Data kategori gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
        }
    }

    public function create_ajax()
    {
        $kategori = KategoriModel::select('kategori_id', 'kategori_kode', 'kategori_nama')->get();  

        return view('kategori.create_ajax')->with('kategori', $kategori);
    }

    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
        $rules = [
            'kategori_kode' => 'required|string|max:10|unique:m_kategori,kategori_kode',
            'kategori_nama' => 'required|string|max:100',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi Gagal',
                'msgField' => $validator->errors(),
            ]);
        }

        // Create the category
        KategoriModel::create($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Data kategori berhasil disimpan',
        ]);
    }

        return redirect('/');
    }

    public function edit_ajax(string $id)
{
    $kategori = KategoriModel::find($id);

    return view('kategori.edit_ajax', ['kategori' => $kategori]);
}

public function update_ajax(Request $request, $id){
    // Check if the request is from ajax
    if ($request->ajax() || $request->wantsJson()) {
        $rules = [
            'kategori_kode' => 'required|string|max:10|unique:m_kategori,kategori_kode,'.$id.',kategori_id',
            'kategori_nama' => 'required|string|max:100',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'msgField' => $validator->errors()
            ]);
        }
        $check = KategoriModel::find($id);
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
        $kategori = KategoriModel::find($id);
        return view('kategori.confirm_ajax', ['kategori' => $kategori]);
    }

    public function delete_ajax(Request $request, string $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $kategori = KategoriModel::find($id);
            if ($kategori) {
                $kategori->delete();
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