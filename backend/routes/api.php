<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AppointmentController;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Http\Controllers\UserController;

// Giriş ve Çıkış Rotası
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum')->name('logout');

// Sadece admin ve employee için kullanılabilir genel rota
Route::middleware(['auth:sanctum'])->group(function () {
    // Randevu rotaları (Hem Admin hem de Employee erişimi)
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::get('/appointments/{id}', [AppointmentController::class, 'show'])->name('appointments.show');
    Route::put('/appointments/{id}', [AppointmentController::class, 'update'])->name('appointments.update');
    Route::delete('/appointments/{id}', [AppointmentController::class, 'destroy'])->name('appointments.destroy');
});

Route::get('/user', [UserController::class, 'getUserInfo']);

   
    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
    Route::put('/employees/{id}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::delete('/employees/{id}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
// Posta koduna göre koordinat bilgilerini almak için rota
Route::get('/fetch-postcode-details/{postcode}', function ($postcode) {
    // postcodes.io API'si ile koordinatları al
    $response = Http::get("https://api.postcodes.io/postcodes/{$postcode}");

    if ($response->successful()) {
        return response()->json($response->json());
    } else {
        return response()->json(['error' => 'Postcode details could not be fetched.'], 400);
    }
});

Route::get('/fetch-nearby-addresses', function (Request $request) {
    $latitude = $request->query('lat');
    $longitude = $request->query('lon');

    if (!$latitude || !$longitude) {
        return response()->json(['error' => 'Latitude and longitude are required'], 400);
    }

    try {
        // Google Geocoding API'sine istek gönderiyoruz
        $apiKey = "AIzaSyBK1XtJlrLVOujmrk5tNW4IoFWyrycy6G0"; // Google API anahtarınızı .env dosyasından alın
        $googleApiUrl = "https://maps.googleapis.com/maps/api/geocode/json?latlng={$latitude},{$longitude}&key={$apiKey}";

        $response = Http::get($googleApiUrl);

        if ($response->successful()) {
            return response()->json($response->json());
        } else {
            return response()->json(['error' => 'Google API request failed'], 500);
        }
    } catch (Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});
