<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    // Çalışanları listeleme
    public function index()
    {
        $employees = Employee::all();
        return response()->json($employees);
    }

    // Çalışan ekleme
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
        ]);

        $employee = Employee::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
        ]);

        return response()->json($employee, 201);
    }

    // Çalışan güncelleme
    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $employee->id,
        ]);

        $employee->update($request->all());

        return response()->json($employee);
    }

    // Çalışan silme
    public function destroy(Employee $employee)
    {
        $employee->delete();
        return response()->json(['message' => 'Çalışan başarıyla silindi']);
    }
}
