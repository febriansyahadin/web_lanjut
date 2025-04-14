<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\SupplierModel;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;

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

        $activeMenu = 'supplier';
        $supplier = SupplierModel::all(); // Ambil data dengan model

        return view('supplier.index', compact('breadcrumb', 'page', 'activeMenu', 'supplier'));
    }

    public function list(Request $request)
    {
        $supplier = SupplierModel::all(); // Mengambil semua data supplier tanpa filter
    
        return DataTables::of($supplier)
            ->addIndexColumn()
            ->addColumn('aksi', function ($supplier) {
                /*return '<a href="' . url('supplier/' . $supplier->supplier_id) . '" class="btn btn-info btn-sm">Detail</a> '
                    . '<a href="' . url('supplier/' . $supplier->supplier_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a> '
                    . '<form class="d-inline-block" method="POST" action="' . url('supplier/' . $supplier->supplier_id) . '">'
                    . csrf_field() . method_field('DELETE') . '
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakin menghapus data ini?\');">Hapus</button>
                    </form>';*/
                    $btn = '<a href="'.url('supplier/'.$supplier->supplier_id).'" class="btn btn-info btn-sm">Detail</a>'; 
                                $btn .= '<button onclick="modalAction(\''.url('/supplier/' . $supplier->supplier_id . 
                    '/edit_ajax').'\')" class="btn btn-warning btn-sm">Edit</button> '; 
                                $btn .= '<button onclick="modalAction(\''.url('/supplier/' . $supplier->supplier_id . 
                    '/delete_ajax').'\')"  class="btn btn-danger btn-sm">Hapus</button> '; 
                                return $btn;
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
    
    public function create_ajax()
    {
        $supplier = SupplierModel::select('supplier_id', 'supplier_nama')->get();

        return view('supplier.create_ajax')->with('supplier', $supplier);
    }

    public function store_ajax(Request $request)
{
    if ($request->ajax() || $request->wantsJson()) {
        $rules = [
            'supplier_nama' => 'required|string|max:100',
            'supplier_kontak' => 'nullable|string|max:50',
            'supplier_alamat' => 'nullable|string|max:255',
            'supplier_email'  => 'required|email|unique:m_supplier,supplier_email'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi Gagal',
                'msgField' => $validator->errors(),
            ]);
        }

        // Create the supplier
        SupplierModel::create($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Data supplier berhasil disimpan',
        ]);
    }

    return redirect('/');
    }
    
    public function edit_ajax(string $id)
    {
        $supplier = SupplierModel::find($id);

        return view('supplier.edit_ajax')->with('supplier', $supplier);
    }

    public function update_ajax(Request $request, $id){
        // Check if the request is from ajax
        if ($request->ajax() || $request->wantsJson()) {
            $check = SupplierModel::find($id);
            if (!$check) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data not found'
                ]);
            }
    
            $rules = [
                'supplier_nama' => 'required|string|max:100',
                'supplier_alamat' => 'nullable|string|max:255', 
                'supplier_kontak' => 'nullable|string|max:50',
            ];
    
            // Validasi email hanya jika diubah
            if ($request->supplier_email != $check->supplier_email) {
                $rules['supplier_email'] = 'required|email|unique:m_supplier,supplier_email';
            }
    
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed.',
                    'msgField' => $validator->errors()
                ]);
            }
    
            $check->update($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Data successfully updated'
            ]);
        }
        return redirect('/');
    }

    public function confirm_ajax(string $id){
        $supplier = SupplierModel::find($id);

        return view('supplier.confirm_ajax', ['supplier' => $supplier]);
    }
    
    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $supplier = SupplierModel::find($id);
            if ($supplier) {
                $supplier->delete();
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
        return view('supplier.import');
    }

    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'file_supplier' => ['required', 'mimes:xlsx', 'max:1024']
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }
            $file = $request->file('file_supplier');
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
                            'supplier_id' => $value['A'],
                            'supplier_nama' => $value['B'],
                            'supplier_alamat' => $value['C'],
                            'supplier_kontak' => $value['D'],
                            'supplier_email' => $value['E'],
                            'created_at' => now()
                        ];
                    }
                }
                if (count($insert) > 0) {
                    SupplierModel::insertOrIgnore($insert);
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
        $supplier = SupplierModel::select('supplier_id', 'supplier_nama', 'supplier_alamat', 'supplier_kontak', 'supplier_email')->get();
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'nama Supplier');
        $sheet->setCellValue('C1', 'Alamat Supplier');
        $sheet->setCellValue('D1', 'Kontak Supplier');
        $sheet->setCellValue('E1', 'Email Supplier');

        $sheet->getStyle('A1:F1')->getFont()->setBold(true);

        $no = 1;
        $baris = 2;
        foreach ($supplier as $value) {
            $sheet->setCellValue('A' . $baris, $no);
            $sheet->setCellValue('B' . $baris, $value->supplier_nama);
            $sheet->setCellValue('C' . $baris, $value->supplier_alamat);
            $sheet->setCellValue('D' . $baris, $value->supplier_kontak);
            $sheet->setCellValue('E' . $baris, $value->supplier_email);
            $baris++;
            $no++;
        }

        foreach (range('A', 'F') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $sheet->setTitle('Data Supplier');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data Supplier - ' . date('Y-m-d H:i:s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        $writer->save('php://output');
        exit;
    }

    public function export_pdf()
    {
        ini_set('max_execution_time', 300);
        set_time_limit(300);
        
        $supplier = SupplierModel::select('supplier_id', 'supplier_nama', 'supplier_alamat', 'supplier_kontak', 'supplier_email')
            ->orderBy('supplier_id')
            ->get();

        $pdf = Pdf::loadView('supplier.export_pdf', ['supplier' => $supplier]);
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption(['isRemoteEnabled' => true]);
        return $pdf->stream('Laporan Data Supplier - ' . date('Y-m-d H:i:s') . '.pdf');
    }

}
