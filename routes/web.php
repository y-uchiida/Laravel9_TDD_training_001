<?php

use App\Http\Controllers\Mypage\UserLoginController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PostManageController;
use App\Http\Controllers\SignupController;
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

Route::get('/', [PostController::class, 'index']);

Route::get('/post/{post}', [PostController::class, 'show'])
  ->name('post.show')
  ->whereNumber('post');

Route::get('/signup', [SignupController::class, 'index']);
Route::post('/signup', [SignupController::class, 'store']);

Route::get('/mypage/login', [UserLoginController::class, 'index'])->name('login');
Route::post('/mypage/login', [UserLoginController::class, 'login']);
Route::post('/mypage/logout', [UserLoginController::class, 'logout'])->name('logout');

Route::get('/mypage/posts/create', [PostManageController::class, 'create'])->middleware('auth')->name('mypage:create');
Route::post('/mypage/posts/create', [PostManageController::class, 'store'])->middleware('auth')->name('mypage:store');

Route::get('/mypage/posts/edit/{id}', [PostManageController::class, 'edit'])->middleware('auth')->name('mypage:edit')->whereNumber('id');
Route::get('/mypage/posts', [PostManageController::class, 'index'])->middleware('auth')->name('mypage:posts');