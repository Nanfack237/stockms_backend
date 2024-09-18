<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class StoreController extends Controller
{
    public function list(Request $request){

        $userData = json_decode($request->user, true);
        $user_id = $userData['id'];

        $stores = Store::where('user_id', $user_id)->get();

        if($stores){

            $status = 200;
            $response = [
                'success' => 'Stores',
                'stores' => $stores,
            ];
            
        } else {

            $status = 422;
            $response = [
                'error' => 'error, failed to list stores',
            ];

        }

        return response()->json($response, $status);
    }

    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'name'=>'required|string|min:4',
            'category'=>'required|string|max:30',
            'description'=>'required|string|min:10',
            'location'=>'required|string',
            'contact'=>'required|integer',
            

        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $userData = json_decode($request->user, true);

        $user_id = $userData['id'];

        $saveStoreData = $validator->validated();
        $store = Store::create([
            'name' => $saveStoreData['name'],
            'category' => $saveStoreData['category'],
            'description' => $saveStoreData['description'],
            'location'=> $saveStoreData['location'],
            'contact'=> $saveStoreData['contact'],
            'image'=>'images/login.jpg',
            'user_id'=> $user_id,
            'status' => 1
        ]);
        if ($store) {

            $status = 201;
            $response = [
                'success' => 'Store created successfully !',
                'store' => $store,
            ];

        } else {
            $status = 422;
            $response = [
                'error' => 'Error, failed to create a store!',
            ];
        }

        return response()->json($response, $status);

    }

    public function edit(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name'=>'required|string|name|unique:stores',
            'category'=>'required|string|max:30',
            'description'=>'required|string|max:255',
            'location'=>'required|string',
            'contact'=>'required|integer',
            'image'=>'required|string',
        ]);

        if ($validator->fails()) {
            return $this->error('error', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
        }

        $store = Store::where('user_id', $userId)->find($id);

        $editStoreData = $validator->validated(); 

        $store->name = $editStoreData['name'];
        $store->category = $editStoreData['category'];
        $store->description = $editStoreData['description'];
        $store->location = $editStoreData['location'];
        $store->contact = $editStoreData['contact'];
        $store->image = $editStoreData['image'];

        if ($store->save()) {
            $status = 201;
            $response = [
                'success' => 'Store edited successfully!',
                'store' => $store,
            ];
        } else {
            $status = 422;
            $response = [
                'error' => 'Error, failed to edit the store!',
            ];
        }

        return response()->json($response, $status);
    }

    public function show(Request $request)
    {
        $userData = json_decode($request->user, true);
        $user_id = $userData['id'];

        $storeData = json_decode($request->store, true);
        $store_id = $storeData['id'];

        $store = Store::where('user_id', $user_id)->find($store_id);

        if($store){
            $status = 200;
            $response = [
                'success' => 'store',
                'stores' => $store,
            ];
        } else {
            $status = 422;
            $response = [
                'error' => 'Error, failed to find the store',
            ];
        }

        return response()->json($response, $status);

    }

    public function delete(Request $request, $id)
    {
        $userData = json_decode($request->user, true);
        $user_id = $userData['id'];

        $store = Store::where('user_id', $user_id)->find($id);


        if($store->delete()){
            $status = 200;
            $response = [
                'success' => 'Store deleted successfully',
            ];
        } else {
            $status = 422;
            $response = [
                'error' => 'error, failed to delete store',
            ];
        }

        return response()->json($response, $status);
    }
}
