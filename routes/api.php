<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DogsController;
use Illuminate\Support\Facades\Route;

Route::get('/dog_list', [DogsController::class, 'dog_list'])->name('dog_list');
Route::get('/dog_list/show/{id}', [DogsController::class, 'dog_list_show'])->name('dog_list_show');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/dog_list/store', [DogsController::class, 'dog_list_store'])->name('dog_list_store');

Route::put('/dog_list/update/{id}', [DogsController::class, 'dog_list_update'])->name('dog_list_update');
Route::delete('/dog_list/delete/{id}', [DogsController::class, 'dog_list_destroy'])->name('dog_list_destroy');

Route::group(['middleware' => ['auth:sanctum']], function(){
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
