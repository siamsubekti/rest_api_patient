<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PersonController extends Controller
{
    public function index(): JsonResponse
    {
        $people = Person::all();

        return response()->json([
            'status' => [
                'code' => '200',
                'description' => 'Get All Person Ok'
            ],
            'data' => $people,
        ]);
    }

    public function get($id): JsonResponse
    {
        $person = Person::find($id);

        if ($person) {
            return response()->json([
                'status' => [
                    'code' => '200',
                    'description' => 'Get Single Person Ok'
                ],
                'data' => $person,
            ], 200);
        } else {
            return response()->json([
                'status' => [
                    'code' => '404',
                    'description' => 'Data Person Not Found'
                ],
                'data' => [],
            ], 404);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'name' => 'required|max:191',
            'phone' => 'required|min:10|max:20',
            'address' => 'required'
        ]);

        $person = Person::find($id);

        if ($person) {
            $person->name = $request->name;
            $person->phone = $request->phone;
            $person->address = $request->address;
            $person->update();
            return response()->json([
                'status' => [
                    'code' => '200',
                    'description' => 'Person has been updated',
                ],
                'data' => $person,
            ], 200);
        } else {
            return response()->json([
                'status' => [
                    'code' => '404',
                    'description' => 'Data Person Not Found'
                ],
                'data' => [],
            ], 404);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|max:191',
            'phone' => 'required|min:10|max:20',
            'address' => 'required'
        ]);

        $person = new Person();
        $person->name = $request->name;
        $person->phone = $request->phone;
        $person->address = $request->address;
        $person->save();

        return response()->json([
            'status' => [
                'code' => '201',
                'description' => 'Person has been created',
            ],
            'data' => $person,
        ], 201);
    }

    public function destroy($id): JsonResponse
    {
        $person = Person::find($id);

        if ($person) {
            $person->delete();

            return response()->json([
                'status' => [
                    'code' => '200',
                    'description' => "Person with ${person['name']} has been deleted"
                ]
            ], 200);
        } else {
            return response()->json([
                'status' => [
                    'code' => '404',
                    'description' => 'Data Person Not Found'
                ],
            ], 404);
        }
    }
}
