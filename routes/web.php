<?php

use App\Models\invoice;
use App\Models\invoices_details;
use App\Models\InvoiceAttachments;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\Customers_Report;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\SectionsController;
use App\Http\Controllers\InvoicesDetailsController;
use App\Http\Controllers\InvoiceAttachmentsController;
use App\Http\Controllers\ReportsController;

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
    return view('auth.login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

//                                             invoice
Route::get('edit_invoice/{id?}', [InvoiceController::class, 'edit']); //edit invoice
Route::get('status_show/{id?}', [InvoiceController::class, 'show'])->name('status_show');
Route::post('Status_Update/{id?}', [InvoiceController::class, 'Status_Update'])->name('Status_Update');
Route::resource('invoices', InvoiceController::class);
Route::get('invoices_paid', [InvoiceController::class, 'paid']); //الفواتير المدفوعة
Route::get('invoices_unpaid', [InvoiceController::class, 'unpaid']); //الفواتير الغير المدفوعة
Route::get('invoices_partial', [InvoiceController::class, 'partial']); //الفواتير المدفوعة جزئيا
//                                            Print_invoices
Route::get('Print_invoice/{id?}', [InvoiceController::class, 'Print']);
//                                            Export_invoice as Excel
Route::get('export_invoices', [InvoiceController::class, 'export']);
//                                             Archive
Route::resource('Archive', ArchiveController::class);
//                                          invoice Details
Route::get('InvoicesDetails/{id?}', [InvoicesDetailsController::class, 'edit'])->name('InvoicesDetails');
Route::get('notification', [InvoicesDetailsController::class, 'notification']);
Route::get('View_file/{invoice_number}/{file_name}', [InvoicesDetailsController::class, 'openFile']); //show file
Route::get('download/{invoice_number}/{file_name}', [InvoicesDetailsController::class, 'getFile']); //download file
Route::get('delete_file/{id?}', [InvoicesDetailsController::class, 'destroy'])->name('destroy.file'); //delete file
//                                          Section Invoice
Route::resource('sections', SectionsController::class);
//                                          Attachments invoice
Route::resource('InvoiceAttachments', InvoiceAttachmentsController::class);
//                                          product Invoice
Route::get('/editProduct/{id?}', [ProductsController::class, 'update'])->name('product.update');
Route::get('/deleteProduct/{id?}', [ProductsController::class, 'destroy'])->name('product.destory');
Route::resource('products', ProductsController::class);
Route::get('section/{id?}', [InvoiceController::class, 'getproducts']);
//                                         permisission and users
Route::group(['middleware' => ['auth']], function () {
    Route::resource('users', 'App\Http\Controllers\UserManagement\UserController');
    Route::resource('roles', 'App\Http\Controllers\UserManagement\RoleController');
});

//                                         reports
Route::get('reports_invoice',[ReportsController::class,'index']);
Route::post('Search_invoices',[ReportsController::class,'Search_invoices']);
Route::get('costomer_reports',[Customers_Report::class,'index']);
Route::post('Search_customers',[Customers_Report::class,'Search_customers']);
//                                        NOtification
Route::get('markAsRead',[InvoiceController::class,'mark_All_As_Read'])->name('mark_All_As_Read');
Route::get('markAsRead/{id}',[InvoiceController::class,'markAsRead'])->name('markAsRead');



Route::get('/{page}', 'App\Http\Controllers\AdminController@index');
