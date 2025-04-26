<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return Inertia::render('welcome');
});
Route::get('/login', function () {
    return Inertia::render('login');
});

Route::get('/registration', function () {
    return Inertia::render('registration');
});

// Just for testing
// Route::get('/test-db', function () {
//     try {
//         DB::connection()->getPdo();
//         return 'Database working!';
//     } catch (\Exception $e) {   
//         return 'Database failed: ' . $e->getMessage();
//     }
// });