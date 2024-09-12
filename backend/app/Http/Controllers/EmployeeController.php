<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class EmployeeController extends Controller
{
    /**
     * Tüm çalışanları listele.
     */
    public function index()
    {
        $employees = Employee::with('user')->get(); // Çalışanları ve ilgili kullanıcıları getir
        return response()->json($employees);
    }

    /**
     * Yeni bir çalışan ekle ve ona bir kullanıcı oluştur.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        // Kullanıcı oluştur
        $user = User::create([
            'name' => $validatedData['first_name'] . ' ' . $validatedData['last_name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role' => 'employee',  // Rol olarak "employee" atanır
        ]);

        // Çalışan oluştur
        $employee = Employee::create([
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'user_id' => $user->id,  // Kullanıcı ID'sini ilişkilendir
        ]);

        return response()->json([
            'message' => 'Çalışan başarıyla eklendi.',
            'employee' => $employee,
            'user' => $user,
        ], 201);
    }

    /**
     * Belirli bir çalışanın bilgilerini güncelle.
     */
    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);
        $user = User::findOrFail($employee->user_id);

        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id, // Mevcut kullanıcı hariç email benzersiz olmalı
            'password' => 'nullable|string|min:8', // Şifre opsiyonel
        ]);

        // Kullanıcıyı güncelle
        $user->name = $validatedData['first_name'] . ' ' . $validatedData['last_name'];
        $user->email = $validatedData['email'];
        if (!empty($validatedData['password'])) {
            $user->password = Hash::make($validatedData['password']);
        }
        $user->save();

        // Çalışanı güncelle
        $employee->first_name = $validatedData['first_name'];
        $employee->last_name = $validatedData['last_name'];
        $employee->save();

        return response()->json([
            'message' => 'Çalışan başarıyla güncellendi.',
            'employee' => $employee,
        ]);
    }

    /**
     * Belirli bir çalışanı sil ve ilgili kullanıcıyı da kaldır.
     */
    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);
        $user = User::findOrFail($employee->user_id);

        // Çalışan ve kullanıcıyı sil
        $employee->delete();
        $user->delete();

        return response()->json(['message' => 'Çalışan başarıyla silindi.']);
    }
    public function getAppointments($employeeId)
    {
        try {
            $appointments = Appointment::where('employee_id', $employeeId)->get();
            return response()->json($appointments, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Çalışanın randevuları yüklenemedi.'], 500);
        }
    }
}
