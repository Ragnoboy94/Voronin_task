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
        $birthdate = Carbon::parse($data['birthdate']);
        $now = Carbon::now();

        $data['age'] = $birthdate->diffInDays($now);
        $data['age_type'] = 'день';

        if ($data['age'] > 30) {
            $data['age'] = $birthdate->diffInMonths($now);
            $data['age_type'] = 'месяц';
        }

        if ($data['age'] > 12) {
            $data['age'] = $birthdate->diffInYears($now);
            $data['age_type'] = 'год';
        }

        $patient = new Patient($data);

        $patient_id = rand(1, 10000); // Выбираем случайный ID для пациента. На практике вам потребуется более надежный метод генерации уникальных ID.

        Cache::put('patient_' . $patient_id, $patient, 300);

        // Получаем текущий список ID пациентов из кэша (или пустой массив, если список еще не существует)
        $patient_ids = Cache::get('patient_ids', []);

        // Добавляем ID нового пациента в список
        $patient_ids[] = $patient_id;

        // Сохраняем обновленный список ID пациентов в кэше
        Cache::put('patient_ids', $patient_ids, 300);

        return response()->json($patient, 201);
    }

    public function index()
    {
        // Получаем список ID пациентов из кэша
        $patient_ids = Cache::get('patient_ids', []);

        $patients = [];

        // Загружаем каждого пациента по ID из кэша
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
