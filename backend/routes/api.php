<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/employees', [EmployeeController::class, 'index']); // Çalışanları listeleme
    Route::post('/employees', [EmployeeController::class, 'store']); // Yeni çalışan ekleme
    Route::put('/employees/{employee}', [EmployeeController::class, 'update']); // Çalışan güncelleme
    Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy']); // Çalışan silme
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/appointments', [AppointmentController::class, 'index']); // Randevuları listeleme
    Route::post('/appointments', [AppointmentController::class, 'store']); // Yeni randevu ekleme
    Route::put('/appointments/{appointment}', [AppointmentController::class, 'update']); // Randevu güncelleme
    Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroy']); // Randevu silme
    Route::get('appointments/{id}', [AppointmentController::class, 'show']);

});