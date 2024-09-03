<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Kullanıcı girişi.
     */
    public function login(Request $request)
    {
        // Giriş verilerini doğrulayın
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Kullanıcı rolüne göre token oluşturma
            if ($user->role === 'employee') {
                $token = $user->createToken('EmployeeToken', ['view-appointments', 'create-appointments'])->plainTextToken;
            } else {
                $token = $user->createToken('AdminToken', ['*'])->plainTextToken;
            }

            return response()->json([
                'user' => $user,
                'token' => $token,
            ]);
        }

        // Giriş bilgileri hatalı ise hata döndür
        throw ValidationException::withMessages([
            'email' => ['Giriş bilgileri hatalı.'],
        ]);
    }
    public function logout(Request $request)
    {
        try {
            // Kullanıcının tüm tokenlarını sil
            $request->user()->tokens()->delete();

            return response()->json(['message' => 'Başarıyla çıkış yapıldı.'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Çıkış işlemi başarısız.', 'error' => $e->getMessage()], 500);
        }
    }
}
