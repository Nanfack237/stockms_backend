<?php

namespace App\Http\Controllers;
use App\Models\Product;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    public function list(Request $request){

        $storeData = json_decode($request->store, true);
        $store_id = $storeData['id'];

        $products = Product::where('store_id', $store_id)->get();

        if($products){

            $status = 200;
            $response = [
                'success' => 'Product',
                'products' => $products,
            ];
            
        } else {

            $status = 422;
            $response = [
                'error' => 'error, failed to list products',
            ];

        }

        return response()->json($response, $status);
    }

    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'name'=>'required|string|min:4',
            'category'=>'required|string',
            'description'=>'required|string',
            'image'=>'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $storeData = json_decode($request->store, true);
        $store_id = $storeData['id'];

        $storeProductData = $validator->validated();
        $product = Product::create([
            'name' => $storeProductData['name'],
            'category' => $storeProductData['category'],
            'description' => $storeProductData['description'],
            'image'=> $storeProductData['image'],
            'store_id'=> $store_id,
            'status' => 1
        ]);
        if ($product) {

            $status = 201;
            $response = [
                'message' => 'Product stored successfully !',
                'Product' => $product,
            ];

        } else {
            $status = 422;
            $response = [
                'message' => 'error, failed to store the product!',
            ];
        }

        return response()->json($response, $status);

    }//

    public function edit(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:4',
            'category' => 'required|string|max:30',
            'description' => 'required|string|max:255',
            'image' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->error('error', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
        }

        $storeData = json_decode($request->store, true);
        $store_id = $storeData['id'];

        $product = Product::where('store_id', $store_id)->find($id);


        $editProductData = $validator->validated(); // Get validated data as array

        $product->name = $editProductData['name'];
        $product->category = $editProductData['category'];
        $product->description = $editProductData['description'];
        $product->image = $editProductData['image'];

        if ($product->save()) {
            $status = 201;
            $response = [
                'message' => 'Product edited successfully!',
                'Product' => $product,
            ];
        } else {
            $status = 422;
            $response = [
                'message' => 'Error, failed to edit the product!',
            ];
        }

        return response()->json($response, $status);
    }

    public function show(Request $request, $id)
    {
        $storeData = json_decode($request->store, true);
        $store_id = $storeData['id'];

        $product = Product::where('store_id', $store_id)->find($id);

        if($product){
            $status = 200;
            $response = [
                'success' => 'Product',
                'product' => $product,
            ];
        } else {
            $status = 422;
            $response = [
                'error' => 'error, failed to find product',
            ];
        }


        return response()->json($response, $status);

    }

    public function delete(Request $request, $id)
    {
        $storeData = json_decode($request->store, true);
        $store_id = $storeData['id'];

        $product = Product::where('store_id', $store_id)->find($id);
  
        if($product->delete()){
            $status = 200;
            $response = [
                'success' => 'Product deleted successfully',
            ];
        } else {
            $status = 422;
            $response = [
                'error' => 'error, failed to delete product',
            ];
        }

        return response()->json($response, $status);
    }
}
