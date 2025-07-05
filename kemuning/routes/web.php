<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Finance\CustomerProgressController;
use App\Http\Controllers\Finance\FinanceDashboardController;
use App\Http\Controllers\Finance\RecapFrameController;
use App\Http\Controllers\Finance\RecapNonPhotoController;
use App\Http\Controllers\Finance\RecapPhotoController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\TransactionController;
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

// DASHBOARD HOME
Route::get('/', [DashboardController::class, 'index']);
Route::post('/', [DashboardController::class, 'createNewCustomer']);
Route::post('/editorder', [DashboardController::class, 'editCustomer']);


// TRANSACTION
Route::get('/customer/item/{id}', [TransactionController::class, 'getCustomerItem']);
Route::delete('/customer/itemdel/{id}/{item_id}', [TransactionController::class, 'deleteCustomerItem']);
Route::post('/transaction/edit/{id}', [TransactionController::class, 'updateDataCustomer']);
Route::post('/transaction/cancel/{id}', [TransactionController::class, 'cancelCustomer']);
Route::post('/transaction/additem', [TransactionController::class, 'addNewCustomerItem']);
Route::get('/transaction/{id}', [TransactionController::class, 'new_transaction']);
Route::get('getEmployees/{id}', [TransactionController::class, 'getEmployees']);

// ADMIN
Route::get('/admin', [AdminDashboardController::class, 'index']);
Route::get('/admin/customerData', [CustomerController::class, 'customerManagement']);
Route::get('/admin/customerData/table', [CustomerController::class, 'customerTable']);
Route::get('/admin/orderDetail/{id}', [CustomerController::class, 'customerDetail']);
Route::get('/admin/itemData', [ItemController::class, 'itemData']);

Route::get('/admin/updateItemData/{id}', [ItemController::class, 'updateItemData']);
Route::post('/admin/saveUpdateItemData', [ItemController::class, 'saveUpdateItemData']);

// FINANCE
Route::get('/finance', [FinanceDashboardController::class, 'index']);
Route::get('/finance/recap/photo', [RecapPhotoController::class, 'index']);
Route::post('/finance/recap/photo', [RecapPhotoController::class, 'index']);
Route::get('/finance/recap/nonPhoto', [RecapNonPhotoController::class, 'index']);
Route::post('/finance/recap/nonPhoto', [RecapNonPhotoController::class, 'index']);
Route::get('/finance/recap/frame', [RecapFrameController::class, 'index']);
Route::post('/finance/recap/frame', [RecapFrameController::class, 'index']);
Route::get('/finance/progress', [CustomerProgressController::class, 'index']);

Route::get('/invoice/customer/{id}', [InvoiceController::class, 'customerInvoice']);

// TABLE
Route::get('/recent_customer', [DashboardController::class, 'getRecentCustomer']);
Route::get('/finance/progress/table', [CustomerProgressController::class, 'getCustomerProgress']);
Route::get('/admin/itemData/table', [ItemController::class, 'itemTable']);
