<?php

namespace App\Http\Controllers;

use App\Models\LevelModel;
use App\Models\BarangModel;
use App\Models\KategoriModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yajra\DataTables\Facades\DataTables;

class BarangController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Barang',
            'list' => ['Home', 'Barang']
        ];

        $page = (object) [
            'title' => 'Data Barang'
        ];

        $activeMenu = 'barang';
        $kategori = KategoriModel::select('kategori_id', 'kategori_nama')->get();
        return view('barang.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu, 'kategori' => $kategori]);
    }

    public function create()
    {
        $breadcrumb = (object) [
            'title' => 'Tambah Barang',
            'list' => ['Home', 'Barang', 'Tambah']
        ];

        $page = (object) [
            'title' => 'Tambah Barang Baru'
        ];

        $activeMenu = 'barang';
        $kategoris = KategoriModel::all();
        return view('barang.create', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu, 'kategoris' => $kategoris]);
    }

    public function store(Request $request)
    {

        $request->validate([
            'barang_kode' => 'required|string|max:10|unique:m_barang,barang_kode',
            'barang_nama' => 'required|string|max:100',
            'harga_jual' => 'required|numeric',
            'harga_beli' => 'required|numeric',
            'kategori_id' => 'required|integer'
        ]);

        BarangModel::create([
            'barang_kode' => $request->barang_kode,
            'barang_nama' => $request->barang_nama,
            'harga_jual' => $request->harga_jual,
            'harga_beli' => $request->harga_beli,
            'kategori_id' => $request->kategori_id
        ]);

        return redirect('barang')->with('success', 'Data barang berhasil disimpan');
    }

    public function show(string $id)
    {
        $barang = BarangModel::find($id);
        
        $breadcrumb = (object) [
            'title' => 'Detail Barang',
            'list' => ['Home', 'Barang', 'Detail']
        ];

        $page = (object) [
            'title' => 'Detail Barang'
        ];

        $activeMenu = 'barang';

        return view('barang.show', [
            'breadcrumb' => $breadcrumb, 
            'page' => $page, 
            'barang' => $barang, 
            'activeMenu' => $activeMenu
        ]);
    }

    public function edit(string $id)
    {
        $barang = BarangModel::find($id);
        $kategoris  = KategoriModel::all();
        
        $breadcrumb = (object) [
            'title' => 'Edit Barang',
            'list' => ['Home', 'Barang', 'Edit']
        ];

        $page = (object) [
            'title' => 'Edit Barang'
        ];

        $activeMenu = 'barang';

        return view('barang.edit', [
            'breadcrumb' => $breadcrumb, 
            'page' => $page, 
            'barang' => $barang, 
            'activeMenu' => $activeMenu,
            'kategoris' => $kategoris
        ]);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'barang_kode' => 'required|string|max:10|unique:m_barang,barang_kode,'.$id.',barang_id',
            'barang_nama' => 'required|string|max:100',
            'harga_jual' => 'required|numeric',
            'harga_beli' => 'required|numeric',
            'kategori_id' => 'required|integer'
        ]);

        $barang = BarangModel::find($id);
        if (!$barang) {
            return redirect('barang')->with('error', 'Data barang tidak ditemukan');
        }

        $barang->update([
            'barang_kode' => $request->barang_kode,
            'barang_nama' => $request->barang_nama,
            'harga_jual' => $request->harga_jual,
            'harga_beli' => $request->harga_beli,
            'kategori_id' => $request->kategori_id,
        ]);

        return redirect('barang')->with('success', 'Data barang berhasil diubah');
    }

    public function destroy(string $id)
    {
        try {
            BarangModel::destroy($id);
            return redirect('barang')->with('success', 'Data barang berhasil dihapus');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect('barang')->with('error', 'Data barang gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
        }
    }

    public function list(Request $request)
    {
        $barangQuery = BarangModel::with('kategori')->select('barang_id', 'kategori_id', 'barang_kode', 'barang_nama', 'harga_jual', 'harga_beli');

      
        if ($request->kategori_id) {
            $barangQuery->where('kategori_id', $request->kategori_id);
        }

        return DataTables::of($barangQuery)
            ->addIndexColumn()
            ->addColumn('aksi', function($row) {
                $btn = '<a href="'.url('barang/'.$row->barang_id).'" class="btn btn-info btn-sm">Detail</a> ';
                $btn .= '<button onclick="modalAction(\''.url('/barang/' . $row->barang_id .
                '/edit_ajax').'\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\''.url('/barang/' . $row->barang_id .
                '/delete_ajax').'\')" class="btn btn-danger btn-sm">Hapus</button> ';
                return $btn; 
            })
            ->rawColumns(['aksi'])
            ->make(true);
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
        $kategoris = KategoriModel::all();
        return view('barang.edit_ajax', ['barang' => $barang, 'kategoris' => $kategoris]);
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
        $barang = BarangModel::find($id);
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
public function export_excel()
{
    $barangs = BarangModel::select('kategori_id','barang_kode', 'barang_nama','harga_jual','harga_beli')
    ->orderBy('kategori_id')
    ->with('kategori')->get();
    
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Set headers
    $sheet->setCellValue('A1', 'No');
    $sheet->setCellValue('B1', 'Kode Barang');
    $sheet->setCellValue('C1', 'Nama Barang');
    $sheet->setCellValue('D1', 'Harga Beli');
    $sheet->setCellValue('E1', 'Harga Jual');
    $sheet->setCellValue('F1', 'Kategori');

    $sheet->getStyle('A1:F1')->getFont()->setBold(true);
    // Populate data
    $no = 1;
    $row = 2;
    foreach ($barangs as $key => $value) {
        $sheet->setCellValue('A'.$row, $no);
        $sheet->setCellValue('B'.$row,$value->barang_kode);
        $sheet->setCellValue('C'.$row,$value->barang_nama);
        $sheet->setCellValue('D'.$row,$value->harga_beli);
        $sheet->setCellValue('E'.$row,$value->harga_jual);
        $sheet->setCellValue('F'.$row,$value->kategori->kategori_nama);
        $row++;
        $no++;
    }
    foreach(range('A','F') as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
    }
    $sheet->setTitle('Data Barang');
    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
    $filename = 'data_barang_'.date('Ymd_His').'.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="'.$filename.'"');
    header('Cache-Control: max-age=0');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
    header('Cache-Control: cache, must-revalidate');
    header('Pragma: public');

    $writer->save('php://output');
    exit;
}

public function import()
{
    return view('barang.import');
}

public function import_ajax(Request $request)
{
    if ($request->ajax() || $request->wantsJson()) {
        $rules = [
            'file_barang' => ['required', 'mimes:xlsx', 'max:1024']
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi Gagal',
                'msgField' => $validator->errors()
            ]);
        }
        $file = $request->file('file_barang');
        $reader = IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray(null, false, true, true);
        $insert = [];
        if (count($data) > 1) {
            foreach ($data as $baris => $value) {
                if ($baris > 1) {
                    $insert[] = [
                        'kategori_id' => $value['A'],
                        'barang_kode' => $value['B'],
                        'barang_nama' => $value['C'],
                        'harga_beli' => $value['D'],
                        'harga_jual' => $value['E'],
                        'created_at' => now(),
                    ];
                }
            }
            if (count($insert) > 0) {
                BarangModel::insertOrIgnore($insert);
            }
            return response()->json([
                'status' => true,
                'message' => 'Data berhasil diimport'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Tidak ada data yang diimport'
            ]);
        }
    }
    return redirect('/');
}
public function export_pdf(){
    $barangs = BarangModel::select('kategori_id','barang_kode', 'barang_nama','harga_jual','harga_beli')
    ->orderBy('kategori_id')
    ->with('kategori')->get();

    $pdf = Pdf::loadView('barang.export_pdf',['barang' => $barangs]);
    $pdf->setPaper('a4','potrait');
    $pdf->setOption('isRemoteEnabled',true);
    $pdf->render();
    return $pdf->stream('Data Barang'.date('y-m-d H:i:s').'.pdf');
}

}