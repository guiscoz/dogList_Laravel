<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DogsController;
use Illuminate\Support\Facades\Route;

Route::get('/dog_list', [DogsController::class, 'dog_list'])->name('dog_list');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::group(['middleware' => ['auth:sanctum']], function(){
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/get_user', [AuthController::class, 'get_user'])->name('get_user');

    Route::post('/dog_list/store', [DogsController::class, 'dog_list_store'])->name('dog_list_store');
    Route::put('/dog_list/update/{id}', [DogsController::class, 'dog_list_update'])->name('dog_list_update');
    Route::get('/dog_list/current_dog/{id}', [DogsController::class, 'current_dog'])->name('current_dog');
    Route::delete('/dog_list/delete/{id}', [DogsController::class, 'dog_list_destroy'])->name('dog_list_destroy');
    Route::put('/dog_list/delete_image/{id}', [DogsController::class, 'delete_image'])->name('delete_image');
});
