<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class PatientController extends Controller
{
    public function store(Request $request)
    {
        $requestData = $request->json()->all();
        $keys = ['first_name', 'last_name', 'birthdate'];
        $data = array_intersect_key($requestData, array_flip($keys));

        $patient = new Patient($data);

        $patient_id = rand(1, 10000);

        Cache::put('patient_' . $patient_id, $patient, 300);

        $patient_ids = Cache::get('patient_ids', []);
        $patient_ids[] = $patient_id;
        Cache::put('patient_ids', $patient_ids, 300);

        return response()->json($patient, 201);
    }

    public function index()
    {
        $patient_ids = Cache::get('patient_ids', []);

        $patients = [];

        foreach ($patient_ids as $id) {
            $patient = Cache::get('patient_' . $id);
            if ($patient) {
                $patients[] = $patient;
            }
        }

        $formattedPatients = array_map(function ($patient) {
            return [
                'name' => $patient->name,
                'birthdate' => $patient->birthdate,
                'age' => $patient->age . ' ' . $patient->age_type,
            ];
        }, $patients);

        return response()->json($formattedPatients);
    }

}
