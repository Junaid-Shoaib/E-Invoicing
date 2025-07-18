<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ItemController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('customers.index');
});

Route::get('/test', function() {
 dd(Carbon\Carbon::now()->format('Y-m-d h:i:s'));
});

Auth::routes();

Route::get('admin/home', [HomeController::class, 'adminHome'])->name('admin.home')->middleware('is_admin');
Route::get('invoices/export/excel', [InvoiceController::class, 'exportExcel'])->name('invoices.export.excel');
Route::get('/invoices/export-pdf', [InvoiceController::class, 'exportPdf'])->name('invoices.exportPdf');
Route::resource('customers', CustomerController::class)->middleware('auth');
Route::resource('items', ItemController::class)->middleware('auth');;
Route::resource('invoices', InvoiceController::class)->middleware('auth');;
Route::get('/invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');
Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
