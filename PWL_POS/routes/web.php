<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WelcomeController;
use App\Models\KategoriModel;
use Monolog\Level;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|

*/

Route::pattern('id', '[0-9]+'); // artinya ketika ada parameter {id}, maka harus berupa angka

Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('login', [AuthController::class, 'postlogin']);
Route::get('logout', [AuthController::class, 'logout'])->middleware('auth');
Route::get('register', [AuthController::class, 'register'])->name('register');
Route::post('register', [AuthController::class,'postRegister']);

    // artinya semua route didalam group ini harus punya role ADM (admin)
Route::middleware(['authorize:ADM'])->group(function () {
    Route::prefix('user')->group(function () {
        Route::get('/', [UserController::class, 'index']);                          // Menampilkan halaman awal user
        Route::post('/list', [UserController::class, 'list']);                      // Menampilkan data user dalam bentuk JSON untuk DataTables
        Route::get('/create', [UserController::class, 'create']);                   // Menampilkan halaman form tambah user
        Route::post('/', [UserController::class, 'store']);                         // Menyimpan data user baru
        Route::get('/create_ajax', [UserController::class, 'create_ajax']);         // Menampilkan halaman form tambah user Ajax
        Route::post('/ajax', [UserController::class, 'store_ajax']);                // Menyimpan data user baru Ajax
        Route::get('/{id}', [UserController::class, 'show']);                       // Menampilkan detail user
        Route::get('/{id}/edit', [UserController::class, 'edit']);                  // Menampilkan halaman form edit user
        Route::put('/{id}', [UserController::class, 'update']);                     // Menyimpan perubahan data user
        Route::get('/{id}/show_ajax', [UserController::class,'show_ajax']);         // Menampilkan halaman form show user ajax
        Route::get('/{id}/edit_ajax', [UserController::class, 'edit_ajax']);        // Menampilkan halaman form edit user ajax
        Route::put('/{id}/update_ajax', [UserController::class, 'update_ajax']);    // Menyimpan perubahan data user ajax
        Route::get('/{id}/delete_ajax', [UserController::class, 'confirm_ajax']);   // Untuk tampilkan form confirm delete user Ajax
        Route::delete('/{id}/delete_ajax', [UserController::class, 'delete_ajax']); // Untuk hapus data user ajax
        Route::get('/import', [UserController::class, 'import']);                   // ajax form upload excel
        Route::post('/import_ajax', [UserController::class, 'import_ajax']);        // ajax import excel
        Route::get('/export_excel', [UserController::class, 'export_excel']);       // export excel
        Route::get('/export_pdf', [UserController::class, 'export_pdf']);           // export pdf
        Route::delete('/{id}', [UserController::class, 'destroy']);                 // Menghapus data user
    });
    Route::prefix('level')->group(function () {
        Route::get('/', [LevelController::class, 'index']);                             // Menampilkan halaman awal level
        Route::post('/list', [LevelController::class, 'list']);                         // Menampilkan data level dalam bentuk JSON untuk DataTables
        Route::get('/create', [LevelController::class, 'create']);                      // Menampilkan halaman form tambah level
        Route::post('/', [LevelController::class, 'store']);                            // Menyimpan data level baru
        Route::get('/create_ajax', [LevelController::class, 'create_ajax']);            // Menampilkan halaman form tambah level Ajax
        Route::post('/ajax', [LevelController::class, 'store_ajax']);                   // Menyimpan data level baru Ajax
        Route::get('/{id}', [LevelController::class, 'show']);                          // Menampilkan detail level
        Route::get('/{id}/edit', [LevelController::class, 'edit']);                     // Menampilkan halaman form edit level
        Route::put('/{id}', [LevelController::class, 'update']);                        // Menyimpan perubahan data level
        Route::get('/{id}/show_ajax', [LevelController::class,'show_ajax']);            // Menampilkan halaman form show level ajax
        Route::get('/{id}/edit_ajax', [LevelController::class, 'edit_ajax']);           // Menampilkan halaman form edit level ajax
        Route::put('/{id}/update_ajax', [LevelController::class, 'update_ajax']);       // Menyimpan perubahan data level ajax
        Route::get('/{id}/delete_ajax', [LevelController::class, 'confirm_ajax']);      // Untuk tampilkan form confirm delete level Ajax
        Route::delete('/{id}/delete_ajax', [LevelController::class, 'delete_ajax']);    // Untuk hapus data level ajax
        Route::get('/import', [LevelController::class, 'import']);                      // ajax form upload excel
        Route::post('/import_ajax', [LevelController::class, 'import_ajax']);           // ajax import excel
        Route::get('/export_excel', [LevelController::class, 'export_excel']);          // export excel
        Route::get('/export_pdf', [LevelController::class, 'export_pdf']);              // export pdf
        Route::delete('/{id}', [LevelController::class, 'destroy']);                    // Menghapus data level
    });
});

Route::middleware(['authorize:ADM,MNG'])->group(function () {
    Route::prefix('kategori')->group(function () {
        Route::get('/', [KategoriController::class, 'index']);                              // Menampilkan halaman awal Kategori
        Route::post('/list', [KategoriController::class, 'list']);                          // Menampilkan data Kategori dalam bentuk JSON untuk DataTables
        Route::get('/create', [KategoriController::class, 'create']);                       // Menampilkan halaman form tambah Kategori
        Route::post('/', [KategoriController::class, 'store']);                             // Menyimpan data Kategori baru
        Route::get('/create_ajax', [KategoriController::class, 'create_ajax']);             // Menampilkan halaman form tambah kategori Ajax
        Route::post('/ajax', [KategoriController::class, 'store_ajax']);                    // Menyimpan data kategori baru Ajax
        Route::get('/{id}', [KategoriController::class, 'show']);                           // Menampilkan detail Kategori
        Route::get('/{id}/edit', [KategoriController::class, 'edit']);                      // Menampilkan halaman form edit Kategori
        Route::put('/{id}', [KategoriController::class, 'update']);                         // Menyimpan perubahan data Kategori
        Route::get('/{id}/show_ajax', [KategoriController::class,'show_ajax']);             // Menampilkan halaman form show kategori ajax
        Route::get('/{id}/edit_ajax', [KategoriController::class, 'edit_ajax']);            // Menampilkan halaman form edit kategori ajax
        Route::put('/{id}/update_ajax', [KategoriController::class, 'update_ajax']);        // Menyimpan perubahan data kategori ajax
        Route::get('/{id}/delete_ajax', [KategoriController::class, 'confirm_ajax']);       // Untuk tampilkan form confirm delete kategori Ajax
        Route::delete('/{id}/delete_ajax', [KategoriController::class, 'delete_ajax']);     // Untuk hapus data kategori ajax
        Route::get('/import', [KategoriController::class, 'import']);                       // ajax form upload excel
        Route::post('/import_ajax', [KategoriController::class, 'import_ajax']);            // ajax import excel
        Route::get('/export_excel', [KategoriController::class, 'export_excel']);           // export excel
        Route::get('/export_pdf', [KategoriController::class, 'export_pdf']);               // export pdf
        Route::delete('/{id}', [KategoriController::class, 'destroy']);                     // Menghapus data kategori
    });
    Route::prefix('supplier')->group(function () {
        Route::get('/', [SupplierController::class, 'index']);                              // Menampilkan halaman awal Supplier
        Route::post('/list', [SupplierController::class, 'list']);                          // Menampilkan data Supplier dalam bentuk JSON untuk DataTables
        Route::get('/create', [SupplierController::class, 'create']);                       // Menampilkan halaman form tambah Supplier
        Route::post('/', [SupplierController::class, 'store']);                             // Menyimpan data Supplier baru
        Route::get('/create_ajax', [SupplierController::class, 'create_ajax']);             // Menampilkan halaman form tambah supplier Ajax
        Route::post('/ajax', [SupplierController::class, 'store_ajax']);                    // Menyimpan data supplier baru Ajax
        Route::get('/{id}', [SupplierController::class, 'show']);                           // Menampilkan detail Supplier
        Route::get('/{id}/edit', [SupplierController::class, 'edit']);                      // Menampilkan halaman form edit Supplier
        Route::put('/{id}', [SupplierController::class, 'update']);                         // Menyimpan perubahan data Supplier
        Route::get('/{id}/show_ajax', [SupplierController::class,'show_ajax']);             // Menampilkan halaman form show supplier ajax
        Route::get('/{id}/edit_ajax', [SupplierController::class, 'edit_ajax']);            // Menampilkan halaman form edit supplier ajax
        Route::put('/{id}/update_ajax', [SupplierController::class, 'update_ajax']);        // Menyimpan perubahan data supplier ajax
        Route::get('/{id}/delete_ajax', [SupplierController::class, 'confirm_ajax']);       // Untuk tampilkan form confirm delete supplier Ajax
        Route::delete('/{id}/delete_ajax', [SupplierController::class, 'delete_ajax']);     // Untuk hapus data supplier ajax
        Route::get('/import', [SupplierController::class, 'import']);                       // ajax form upload excel
        Route::post('/import_ajax', [SupplierController::class, 'import_ajax']);            // ajax import excel
        Route::get('/export_excel', [SupplierController::class, 'export_excel']);           // export excel
        Route::get('/export_pdf', [SupplierController::class, 'export_pdf']);               // export pdf
        Route::delete('/{id}', [SupplierController::class, 'destroy']);                     // Menghapus data supplier
    });
    Route::prefix('barang')->group(function () {
        Route::get('/', [BarangController::class, 'index']);                            // Menampilkan halaman awal Barang
        Route::get('/list', [BarangController::class, 'list']);                        // Menampilkan data Barang dalam bentuk JSON untuk DataTables
        Route::get('/create', [BarangController::class, 'create']);                     // Menampilkan halaman form tambah Barang
        Route::post('/', [BarangController::class, 'store']);                           // Menyimpan data Barang baru
        Route::get('/create_ajax', [BarangController::class, 'create_ajax']);           // Menampilkan halaman form tambah barang Ajax
        Route::post('/ajax', [BarangController::class, 'store_ajax']);                  // Menyimpan data barang baru Ajax
        Route::get('/{id}', [BarangController::class, 'show']);                         // Menampilkan detail Barang
        Route::get('/{id}/edit', [BarangController::class, 'edit']);                    // Menampilkan halaman form edit Barang
        Route::put('/{id}', [BarangController::class, 'update']);                       // Menyimpan perubahan data Barang
        Route::get('/{id}/show_ajax', [BarangController::class,'show_ajax']);           // Menampilkan halaman form show barang ajax
        Route::get('/{id}/edit_ajax', [BarangController::class, 'edit_ajax']);          // Menampilkan halaman form edit barang ajax
        Route::put('/{id}/update_ajax', [BarangController::class, 'update_ajax']);      // Menyimpan perubahan data barang ajax
        Route::get('/{id}/delete_ajax', [BarangController::class, 'confirm_ajax']);     // Untuk tampilkan form confirm delete barang Ajax
        Route::delete('/{id}/delete_ajax', [BarangController::class, 'delete_ajax']);   // Untuk hapus data barang ajax
        Route::get('/import', [BarangController::class, 'import']);                     // ajax form upload excel
        Route::post('/import_ajax', [BarangController::class, 'import_ajax']);          // ajax import excel
        Route::get('/export_excel', [BarangController::class, 'export_excel']);         // export excel
        Route::get('/export_pdf', [BarangController::class, 'export_pdf']);             // export pdf
        Route::delete('/{id}', [BarangController::class, 'destroy']);                   // Menghapus data barang
    });
});

Route::middleware(['auth'])->group(function () {
    Route::get('/', [WelcomeController::class, 'index']);
});

Route::group(['prefix' => 'profile'], function () {
    Route::get('/', [ProfileController::class, 'index']);
    Route::post('/update_photo', [ProfileController::class, 'update_photo']);
});  
