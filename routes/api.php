<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\CheckRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});
Route::post('signin/', [AuthController::class, 'loginUser'])->name('loginUser');
Route::post('register/', [AuthController::class, 'register'])->name('register');

Route::group(['middleware' => ['auth:api', 'roles:admin']], function () {
    Route::post('product/{id}/comment', [CommentController::class, 'storeComment'])->name('storeComment');
    Route::get('product/{id}', [ProductController::class, 'show'])->name('show');
    Route::put('product/{id}', [ProductController::class, 'update'])->name('update');
    Route::delete('product/{id}', [ProductController::class, 'destroy'])->name('destroy');
    Route::post('product/create', [ProductController::class, 'createProduct'])->name('createProduct');
    Route::get('user/{id}', [UserController::class, 'show'])->name('show');
    Route::get('products/category', [ProductController::class, 'search'])->name('search');
    Route::get('product',[ProductController::class,'getAllProduct'])->name('getAllProduct');
    Route::put('user/{id}',[UserController::class,'updateUser'])->name('updateUser');

});
Route::post('forgot-password',[AuthController::class,'forgotPassword'])->name('forgotPassword');
Route::post('reset-password',[AuthController::class,'resetPassword'])->name('resetPassword');


