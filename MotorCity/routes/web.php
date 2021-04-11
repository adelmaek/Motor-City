<?php

use Illuminate\Support\Facades\Route;
use App\Models\Account;
use App\Models\Bank;
use App\Models\Brand;
use Carbon\Carbon;
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
view()->composer(['layouts/app'], function ($view) {
    $bankAccounts = Account::getBankAccounts();
    $posAccounts = Account::getPosAccounts();
    $view->with(['bankAccounts'=>$bankAccounts,'posAccounts'=>$posAccounts]);
});

// Route::get('/', function () {
//     $banks = Bank::all();
//     $bankAccounts = Account::getBankAccounts();
//     $brands = Brand::all();
//     return view('home',["banks"=>$banks,"bankAccounts"=>$bankAccounts,"brands"=>$brands]);
// })->middleware('auth');

Auth::routes();
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home')->middleware(['auth','userAccess']);
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home')->middleware(['auth','userAccess']);
Route::get('/addAccount', [App\Http\Controllers\AccountController::class, 'getAddAccount'])->name('addAccount')->middleware(['auth','userAccess']);
Route::post('/addAccount', [App\Http\Controllers\AccountController::class, 'postInsertAccount'])->name('addAccount')->middleware(['auth','userAccess']);
Route::post('/addBrand', [App\Http\Controllers\AccountController::class, 'postAddBrand'])->name('addBrand')->middleware(['auth','userAccess']);
Route::post('/addTransaction', [App\Http\Controllers\TransactionController::class, 'postAddTransaction'])->name('addTransaction')->middleware(['auth','userAccess']);
Route::get('/queryBrandAllTransactions', [App\Http\Controllers\TransactionController::class, 'getQueryBrandAllTransactions'])->name('queryBrandAllTransactions')->middleware(['auth','userAccess']);
Route::post('/queryBrandAllTransactions', [App\Http\Controllers\TransactionController::class, 'getBrandAllTransactions'])->name('queryBrandAllTransactions')->middleware(['auth','userAccess']);
Route::get('/queryBrandAccountTransaction/{accountType}',[App\Http\Controllers\TransactionController::class, 'getQueryAccountTransaction'])->name('queryBrandAccountTransaction')->middleware(['auth','userAccess']);
Route::post('/queryBrandAccountTransaction/{accountType}', [App\Http\Controllers\TransactionController::class, 'getBrandAccountTransaction'])->name('queryBrandAccountTransaction')->middleware(['auth','userAccess']);    
Route::get('/queryBankAccountTransaction/{accountId}',[App\Http\Controllers\TransactionController::class, 'getQueryBankAccountTransaction'])->name('queryBankAccountTransaction')->middleware(['auth','userAccess']);
Route::post('/queryBankAccountTransaction/{accountId}',[App\Http\Controllers\TransactionController::class, 'getBankAccountTransaction'])->name('queryBankAccountTransaction')->middleware(['auth','userAccess']);
Route::get('/deleteTransaction/{transactionId}',[App\Http\Controllers\TransactionController::class, 'getDeleteTransaction'])->name('deleteTransaction')->middleware(['auth','userAccess']);
Route::post('/settleCheck/{transactionId}',[App\Http\Controllers\TransactionController::class, 'postSettleCheck'])->name('settleCheck')->middleware(['auth','userAccess']);
Route::get('/confirmSettling/{transactionId}',[App\Http\Controllers\TransactionController::class, 'getConfirmCheckSettling'])->name('confirmSettling')->middleware(['auth','userAccess']);
Route::post('/editDescription/{transactionId}',[App\Http\Controllers\TransactionController::class, 'postEditDescription'])->name('editDescription')->middleware(['auth','userAccess']);
Route::post('/editClientName/{transactionId}',[App\Http\Controllers\TransactionController::class, 'postEditClientName'])->name('editClientName')->middleware(['auth','userAccess']);
Route::get('/queryPosAccountTransaction/{accountId}',[App\Http\Controllers\TransactionController::class, 'getQueryPosAccountTransaction'])->name('queryPosAccountTransaction')->middleware(['auth','userAccess']);
Route::post('/queryPosAccountTransaction/{accountId}',[App\Http\Controllers\TransactionController::class, 'postQueryPosAccountTransaction'])->name('queryPosAccountTransaction')->middleware(['auth','userAccess']);
Route::post('/settlePosTransactions',[App\Http\Controllers\TransactionController::class, 'postSettlePosTransactions'])->name('settlePosTransactions')->middleware(['auth','userAccess']);
Route::post('/confirmSettlingPos/{transactionId}',[App\Http\Controllers\TransactionController::class, 'postConfirmSettlingPos'])->name('confirmSettlingPos')->middleware(['auth','userAccess']);
Route::get('/search',[App\Http\Controllers\TransactionController::class, 'getSearchTransactions'])->name('search')->middleware(['auth','userAccess']);
Route::get('/tempTransactions',[App\Http\Controllers\TempTransactionController::class, 'getTempTransactions'])->name('tempTransactions')->middleware(['auth']);
Route::post('/tempTransactions',[App\Http\Controllers\TempTransactionController::class, 'postTempTransaction'])->name('tempTransactions')->middleware(['auth','userAccess']);
Route::get('/deleteTempTransaction/{transactionId}',[App\Http\Controllers\TempTransactionController::class, 'getDeleteTempTransaction'])->name('deleteTempTransaction')->middleware(['auth','userAccess']);
Route::post('/confirmTempTransaction/{transactionId}',[App\Http\Controllers\TempTransactionController::class, 'postConfirmTempTransaction'])->name('confirmTempTransaction')->middleware(['auth','userAccess']);

