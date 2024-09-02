<?php


namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    /**
     * Randevuları listele.
     */
    public function index(Request $request)
    {
        $employeeId = $request->query('employee_id');
        
        // Çalışana göre filtreleme
        if ($employeeId) {
            $appointments = Appointment::where('employee_id', $employeeId)->get();
        } else {
            $appointments = Appointment::all();
        }

        return response()->json($appointments);
    }

    /**
     * Yeni randevu oluştur.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'postcode' => 'required|string',
            'appointment_date' => 'required|date',
            'client_name' => 'required|string|max:255',
            'client_email' => 'required|email|max:255',
            'client_phone' => 'required|string|max:15',
            'employee_id' => 'required|exists:employees,id',
            'location_lat' => 'required|numeric',
            'location_lng' => 'required|numeric',
            'distance' => 'required|string',
            'duration' => 'required|string',
            'departure_time' => 'required', // Ofisten çıkış zamanı zorunlu
            'available_time' => 'required', // Müsait olacağı zaman zorunlu
        ]);

        $appointment = Appointment::create($validatedData);

        return response()->json($appointment, 201);
    }

    /**
     * Randevuyu güncelle.
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'postcode' => 'required|string',
            'appointment_date' => 'required|date',
            'client_name' => 'required|string|max:255',
            'client_email' => 'required|email|max:255',
            'client_phone' => 'required|string|max:15',
            'employee_id' => 'required|exists:employees,id',
            'location_lat' => 'required|numeric',
            'location_lng' => 'required|numeric',
            'distance' => 'required|string',
            'duration' => 'required|string',
            'departure_time' => 'required', // Ofisten çıkış zamanı zorunlu
            'available_time' => 'required', // Müsait olacağı zaman zorunlu
        ]);

        $appointment = Appointment::findOrFail($id);
        $appointment->update($validatedData);

        return response()->json($appointment, 200);
    }

    /**
     * Randevuyu sil.
     */
    public function destroy($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->delete();

        return response()->json(null, 204);
    }
    public function show($id)
{
    $appointment = Appointment::findOrFail($id);
    return response()->json($appointment);
}
}
