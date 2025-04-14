<?php

namespace App\Http\Controllers;

use App\Models\KategoriModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;

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
    
        if ($request->kategori_id) {
            $kategori->where('kategori_id', $request->kategori_id);
        }
    
        return DataTables::of($kategori)
            ->addIndexColumn()
            ->addColumn('aksi', function ($kategori) {
                $btn = '<a href="'.url('kategori/'.$kategori->kategori_id).'" class="btn btn-info btn-sm">Detail</a>'; 
                $btn .= '<button onclick="modalAction(\''.url('/kategori/' . $kategori->kategori_id . '/edit_ajax').'\')" class="btn btn-warning btn-sm">Edit</button> '; 
                $btn .= '<button onclick="modalAction(\''.url('/kategori/' . $kategori->kategori_id . '/delete_ajax').'\')"  class="btn btn-danger btn-sm">Hapus</button> '; 
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
    public function import()
    {
        return view('kategori.import');
    }

    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'file_kategori' => ['required', 'mimes:xlsx', 'max:1024']
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            $file = $request->file('file_kategori');
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
                            'kategori_kode' => $value['A'],
                            'kategori_nama' => $value['B'],
                            'created_at' => now(),
                        ];                        
                    }
                }
                if (count($insert) > 0) {
                    KategoriModel::insertOrIgnore($insert);
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

    public function export_excel()
    {
        $kategori = KategoriModel::select('kategori_kode', 'kategori_nama')->orderBy('kategori_nama')->get();
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kode Kategori');
        $sheet->setCellValue('C1', 'Nama Kategori');
        $sheet->getStyle('A1:C1')->getFont()->setBold(true);

        $no = 1;
        $baris = 2;
        foreach ($kategori as $item) {
            $sheet->setCellValue('A' . $baris, $no);
            $sheet->setCellValue('B' . $baris, $item->kategori_kode);
            $sheet->setCellValue('C' . $baris, $item->kategori_nama);
            $baris++;
            $no++;
        }

        foreach (range('A', 'C') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->setTitle('Data Kategori');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data Kategori - ' . date('Y-m-d H:i:s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');
        header('Pragma: public');

        $writer->save('php://output');
        exit;
    }

    public function export_pdf()
    {
        ini_set('max_execution_time', 300);
        set_time_limit(300);

        $kategori = KategoriModel::select('kategori_kode', 'kategori_nama')
            ->orderBy('kategori_kode')
            ->get();

        $pdf = Pdf::loadView('kategori.export_pdf', ['kategori' => $kategori]);
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption(['isRemoteEnabled' => true]);
        return $pdf->stream('Laporan Data Kategori - ' . date('Y-m-d H:i:s') . '.pdf');
    }

    
}

