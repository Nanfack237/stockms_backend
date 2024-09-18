<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Product;
use App\Models\Store;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class EmployeeController extends Controller
{
    public function list(Request $request){

        $storeData = json_decode($request->store, true);
        $store_id = $storeData['id'];

        $employees = Employee::where('store_id', $store_id)->get();

        if($employees){

            $status = 200;
            $response = [
                'success' => 'employees',
                'employees' => $employees,
            ];

        } else {

            $status = 422;
            $response = [
                'error' => 'error, failed to list Suppliers',
            ];

        }

        return response()->json($response, $status);
    }

    public function store(Request $request){

        $storeData = json_decode($request->store, true);
        $store_id = $storeData['id'];

        $validator = Validator::make($request->all(), [
            'name'=>'required|string|min:4',
            'email'=>'required|string',
            'address'=>'required|string',
            'contact'=>'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $storeEmployeeData = $validator->validated();
        $employee = Employee::create([
            'name' => $storeEmployeeData['name'],
            'email' => $storeEmployeeData['email'],
            'address' => $storeEmployeeData['address'],
            'contact' => $storeEmployeeData['contact'],
            'store_id'=> $store_id,
            'status' => 1
        ]);

        if ($employee) {

            $status = 201;
            $response = [
                'success' => 'Employee stored successfully !',
                'employee' => $employee,
            ];

        } else {
            $status = 422;
            $response = [
                'error' => 'error, failed to store the Employee!',
            ];
        }

        return response()->json($response, $status);

    }

    public function edit(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:4',
            'email' => 'required|string|email|min:4',
            'address' => 'required|string',
            'contact' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->error('error', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
        }

        $storeData = json_decode($request->store, true);
        $store_id = $storeData['id'];

        $employee = Employee::where('store_id', $store_id)->find($id);

        $editEmployeeData = $validator->validated(); // Get validated data as array

        $employee->name = $editEmployeeData['name'];
        $employee->email = $editEmployeeData['email'];
        $employee->address = $editEmployeeData['address'];
        $employee->contact = $editEmployeeData['contact'];

        if ($employee->save()) {
            $status = 201;
            $response = [
                'error' => 'Employee edited successfully!',
                'employee' => $employee,
            ];
        } else {
            $status = 422;
            $response = [
                'error' => 'Error, failed to edit the Employee!',
            ];
        }

        return response()->json($response, $status);
    }

    public function show(Request $request, $id)
    {
        $storeData = json_decode($request->store, true);
        $store_id = $storeData['id'];

        $employee = Employee::where('store_id', $store_id)->find($id);

        if($employee){
            $status = 200;
            $response = [
                'success' => 'Employee',
                'employee' => $employee,
            ];
        } else {
            $status = 422;
            $response = [
                'error' => 'error, failed to find Employee',
            ];
        }


        return response()->json($response, $status);

    }
}
