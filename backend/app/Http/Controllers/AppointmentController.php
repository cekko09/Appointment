<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use Carbon\Carbon;
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
        $request->validate([
            'appointment_date' => 'required|date',
            'client_name' => 'required|string|max:255',
            'client_email' => 'required|email|max:255',
            'client_phone' => 'required|string|max:15',
            'employee_id' => 'required|exists:employees,id',
            'location_lat' => 'required|numeric',
            'location_lng' => 'required|numeric',
            'distance' => 'required|numeric',
            'duration' => 'required|string',
            'departure_time' => 'required|string',
            'available_time' => 'required|string',
            'address' => 'required|string',
        ]);

        

        try {
            $appointment = Appointment::create([
                'postcode' => $request->postcode,
                'appointment_date' => $request->appointment_date,
                'client_name' => $request->client_name,
                'client_email' => $request->client_email,
                'client_phone' => $request->client_phone,
                'employee_id' => $request->employee_id,
                'location_lat' => $request->location_lat,
                'location_lng' => $request->location_lng,
                'distance' => $request->distance,
                'duration' => $request->duration,
                'departure_time' => $request->departure_time,
                'available_time' => $request->available_time,
                'address' => $request->address,
            ]);

            return response()->json($appointment, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Randevu oluşturulamadı. Lütfen tekrar deneyin.'], 500);
        }
    }
    /**
     * Randevuyu güncelle.
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'postcode' => 'nullable|string',
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
            'address' => 'required|string', // Adres alanını doğrula
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

    /**
     * Randevuyu göster.
     */
    public function show($id)
    {
        $appointment = Appointment::findOrFail($id);
        return response()->json($appointment);
    }
}
