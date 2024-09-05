<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Giriş yapmış kullanıcı bilgilerini getir.
     */
    public function getUser(Request $request)
    {
        try {
            // Giriş yapmış kullanıcının bilgisini al
            $user = Auth::user();

            if ($user) {
                return response()->json([
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role, // Rol bilgisini döndür
                ], 200);
            } else {
                return response()->json(['message' => 'Kullanıcı bulunamadı.'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Kullanıcı bilgileri alınırken hata oluştu.'], 500);
        }
    }
}
