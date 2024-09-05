<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // Kullanıcı modeli

class UserController extends Controller
{
    /**
     * Kullanıcı bilgilerini döndürür.
     */
    public function getUserInfo(Request $request)
    {
        // Örnek olarak kimlik doğrulamayı kaldırıyoruz ve tüm kullanıcıyı gösteriyoruz.
        // Eğer sadece giriş yapan kullanıcıyı istiyorsanız, aşağıdaki gibi yapabilirsiniz:
        // $user = $request->user(); 
        
        // Bu örnekte tüm kullanıcıları alıyoruz, siz dilediğiniz gibi düzenleyebilirsiniz.
        $user = User::find(1); // Örneğin, 1. kullanıcıyı al
        
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role, // Rol, örneğin 'admin' veya 'employee'
        ]);
    }
}
