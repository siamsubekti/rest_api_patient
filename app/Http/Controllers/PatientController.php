<?php

namespace App\Http\Controllers;

use App\Models\EnumStatus;
use App\Models\Patient;
use App\Models\Person;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PatientController extends Controller
{
    /**
     * get array person by name
     * @param Request $request
     * @return Builder[]|Collection
     */
    private function getPerson(Request $request)
    {
        $person = new Person();
        $find_person = $person->getPersonName($request->name);
        $person->name = $request->name;
        $person->phone = $request->phone;
        $person->address = $request->address;
        if (count($find_person) !== 0) {
            $person->update();
        } else {
            $person->save();
        }

        return $person->getPersonName($request->name);
    }

    /**
     * get id for array person
     * @param $persons
     * @return mixed
     */
    private function personId($persons)
    {
        foreach ($persons as $person)
        {
            return $person->id;
        }
    }

    /**
     * get all patients
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $patients = Patient::all();

        $data_patients = [];
        foreach ($patients as $patient) {
            $new_patient = new Patient();
            $new_patient->id = $patient->id;
            $new_patient->person = new Person();
            $person = Person::find($patient->person_id);
            if ($person) {
                $new_patient->person->name = $person->name;
                $new_patient->person->phone = $person->phone;
                $new_patient->person->address = $person->address;
            } else {
                $new_patient->person = null;
            }
            $new_patient->statuses = $patient->statuses;
            $new_patient->date_in = $patient->date_in;
            $new_patient->date_out = $patient->date_out;
            $new_patient->created_at = $patient->created_at;
            $new_patient->updated_at = $patient->updated_at;
            $new_patient->deleted_at = $patient->deleted_at;

            array_push($data_patients, $new_patient);
        }

        return response()->json([
            'status' => [
                'code' => '200',
                'description' => 'Get All Patient Ok'
            ],
            'data' => $data_patients
        ]);
    }

    /**
     * get id for array person
     * @param $id
     * @return JsonResponse
     */
    public function get($id): JsonResponse
    {
        $patient = Patient::find($id);
        $person = Person::find($patient->person_id);
        $new_patient = new Patient();
        $new_patient->id = $patient->id;
        $new_patient->person = new Person();
        $new_patient->person = $person;
        $new_patient->statuses = $patient->statuses;
        $new_patient->date_in = $patient->date_in;
        $new_patient->date_out = $patient->date_out;

        if ($patient) {
            return response()->json([
                'status' => [
                    'code' => '200',
                    'description' => 'Get Single Patient Ok'
                ],
                'data' => $new_patient,
            ], 200);
        } else {
            return response()->json([
                'status' => [
                    'code' => '404',
                    'description' => 'Data Patient Not Found'
                ],
                'data' => [],
            ], 404);
        }
    }

    /**
     * get id for array person
     * @param $name
     * @return JsonResponse
     */
    public function search($name): JsonResponse
    {
        $patients = Patient::search($name);

        foreach ($patients as $patient)
        {
            $patient->person = Person::find($patient->person_id);
        }

        return response()->json([
            'status' => [
                'code' => '200',
                'description' => "Get Patient with name $name"
            ],
            'data' => $patients,
        ], 200);
    }

    /**
     * get positive patient
     * @return JsonResponse
     */
    public function searchPositive(): JsonResponse
    {
        $patients = Patient::searchStatuses(EnumStatus::POSITIVE);

        foreach ($patients as $patient)
        {
            $patients->person = Person::find($patient->person_id);
        }

        return response()->json([
            'status' => [
                'code' => '200',
                'description' => "Get Patient with status positive"
            ],
            'data' => $patients,
        ], 200);
    }

    /**
     * get recovered patient
     * @return JsonResponse
     */
    public function searchRecovered(): JsonResponse
    {
        $patients = Patient::searchStatuses(EnumStatus::RECOVERY);

        foreach ($patients as $patient)
        {
            $patients->person = Person::find($patient->person_id);
        }

        return response()->json([
            'status' => [
                'code' => '200',
                'description' => "Get Patient with status recovered"
            ],
            'data' => $patients,
        ], 200);
    }

    /**
     * get dead patient
     * @return JsonResponse
     */
    public function searchDead(): JsonResponse
    {
        $patients = Patient::searchStatuses(EnumStatus::DEAD);

        foreach ($patients as $patient)
        {
            $patients->person = Person::find($patient->person_id);
        }

        return response()->json([
            'status' => [
                'code' => '200',
                'description' => "Get Patient with status dead"
            ],
            'data' => $patients,
        ], 200);
    }

    /**
     * create patient
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required|min:10|max:20',
            'address' => 'required',
            'statuses' => ['required', Rule::in(EnumStatus::$status)],
            'date_in' => 'required|date|before:date_out',
            'date_out' => 'required',
        ]);


        $persons = $this->getPerson($request);
        $person_id = $this->personId($persons);
        $new_person = Person::find($person_id);

        $patient = new Patient();
        $patient->person_id = $person_id;
        $patient->statuses = $request->statuses;
        $patient->date_in = $request->date_in;
        $patient->date_out = $request->date_out;
        $patient->save();
        $patient->person = new Person();
        $patient->person = $new_person;

        return response()->json([
            'status' => [
                'code' => '201',
                'description' => 'Patient has been created',
            ],
            'data' => $patient,
        ], 201);
    }

    /**
     * update patient
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        $patient = Patient::find($id);

        if ($patient) {
            $person = $this->updatePerson($request, $patient->person_id);

            $patient->update([
                'person_id' => $person->id,
                'statuses' => ($request->statuses != null) ? $request->statuses : $patient->statuses,
                'date_in' => ($request->date_in != null) ? $request->date_in : $patient->date_in,
                'date_out' => ($request->date_out != null) ? $request->date_out : $patient->date_out,
            ]);

            $patient->person = new Person();
            $patient->person = $person;

            return response()->json([
                'status' => [
                    'code' => '200',
                    'description' => 'Patient has been updated',
                ],
                'data' => $patient,
            ], 200);
        } else {
            return response()->json([
                'status' => [
                    'code' => '404',
                    'description' => 'Data Patient Not Found'
                ],
                'data' => [],
            ], 404);
        }
    }

    /**
     * get id for array person
     * @param Request $request
     * @param $person_id
     * @return Person
     */
    private function updatePerson(Request $request, $person_id): Person
    {
        $person = Person::find($person_id);
        $person->update([
            'name' => ($request->name != null) ? $request->name : $person->name,
            'phone' => ($request->phone != null) ? $request->name : $person->phone,
            'address' => ($request->address !== null) ? $request->address : $person->address,
        ]);

        return $person;
    }
}
