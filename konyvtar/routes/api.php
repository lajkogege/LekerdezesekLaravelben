<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\LendingController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\Admin;
use App\Http\Middleware\Librarian;
use App\Http\Middleware\Warehouseman;
use App\Models\Lending;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//bárki által elérhető
Route::post('/register',[RegisteredUserController::class, 'store']);
Route::post('/login',[AuthenticatedSessionController::class, 'store']);

//összes kérés

Route::patch('update-password/{id}', [UserController::class, "updatePassword"]);

//autentikált útvonal, simple user is
Route::middleware(['auth:sanctum'])
    ->group(function () {
        //profil elérés, modositása
        Route::get('/auth-users', [UserController::class,'show']);
        Route::patch('/auth-users', [UserController::class,'update']);
        //hány kölcsönzése volt idáig a bejelentkezett felhazsnálonak
        Route::get('/lendings-count',[LendingController::class, 'lendingCount']);
        //hányaktiv kölcsönzése van
        Route::get('/active-lending-count', [LendingController::class,'activeLendingCount']);

        //hány mönyvet kölcsönzött ideéig
        Route::get('/lendings-books-count', [LendingController::class,'lendingsBooksCount']);
        Route::get('lendings-copies', [LendingController::class, "lendingsWithCopies"]);
        Route::get('userlend', [UserController::class, "userLendings"]);
        //kikölcsönzött könyvek adatai
        Route::get('lendings-books-data',[LendingController::class,'lendingsBooksData']);
        // Kijelentkezés útvonal
        Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);
        //könyvenként csoportosítsd, csak azokat, amik max 1 példányban van nálam!
        Route::get('lendings')

    });

//admin
Route::middleware(['auth:sanctum',Admin::class])
->group(function () {
    //összes kérés
    Route::apiResource('/admin/users', UserController::class);
    Route::get('/admin/specific-date', [LendingController::class, "dateSpecific"]);
    Route::get('/admin/specific-copy/{copy_id}', [LendingController::class, "copySpecific"]);
});


//librarian
Route::middleware(['auth:sanctum',Librarian::class])
->group(function () {
   //útvonalak
   Route::get('book-coopies', [BookController::class, "booksWithCopies"]);
});

//warehouseman
Route::middleware(['auth:sanctum', Warehouseman::class])
->group(function () {
   //útvonalak
});



Route::get('books-copies', [BookController::class, "booksWithCopies"]);