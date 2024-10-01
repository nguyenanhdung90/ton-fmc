<?php

use App\Http\Controllers\TonController;
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

//Route::get('/', function () {
//    return view('welcome');
//});

Route::get('/', [TonController::class, 'transfer']);
Route::get('/withdraw', [TonController::class, 'withdraw']);
Route::get('/deposit', [TonController::class, 'deposit']);
