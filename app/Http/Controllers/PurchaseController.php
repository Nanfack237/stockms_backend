<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Store;
use App\Models\Stock;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class PurchaseController extends Controller
{
    public function list(Request $request){
        $storeData = json_decode($request->store, true);
        $store_id = $storeData['id'];

        $purchases = Purchase::where('store_id', $store_id)->get();

        if($purchases){

            $status = 200;
            $response = [
                'success' => 'Purchases',
                'purchases' => $purchases,
            ];

        } else {

            $status = 422;
            $response = [
                'error' => 'error, failed to find purchases',
            ];

        }

        return response()->json($response, $status);

    }

    public function store(Request $request){

        $validator = Validator::make($request->all(), [

            'product_id' => 'required|exists:products,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'quantity' => 'required|integer',
            'date' => 'required|date',
            'unit_price' => 'required|integer|min:0'

        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $storeData = json_decode($request->store, true);
        $store_id = $storeData['id'];

        $storePurchaseData = $validator->validated();
        $product_id = $storePurchaseData['product_id'];
        $supplier_id = $storePurchaseData['supplier_id'];
        $quantity = $storePurchaseData['quantity'];
        $date = $storePurchaseData['date'];
        $unit_price = $storePurchaseData['unit_price'];
        $total_price = $quantity * $unit_price;

        $product = Product::find($product_id);
        $store = Store::find($store_id);
        $supplier = Supplier::find($supplier_id);


        if(!$product || !$supplier || !$store){
            $status = 404;
            $response = [
                'error' => 'Error, this product or supplier does not exist!',
            ];

        } else {

            $purchase = Purchase::create([

                'product_id' => $product_id,
                'store_id' => $store_id,
                'stock_id' => 3,
                'supplier_id' => $supplier_id,
                'quantity' => $quantity,
                'date' => $date,
                'unit_price' => $unit_price,
                'total_price' => $total_price,
                'status' => 1,

            ]);

            if($purchase){

                $stock_check = Stock::where('product_id', $product_id)
                                        ->where('store_id', $store_id)
                                        ->where('cost_price', $unit_price)
                                        ->first();

                if(!$stock_check){

                    $stock = Stock::create([

                        'product_id' => $product_id,
                        'store_id' => $store_id,
                        'quantity' => $quantity,
                        'cost_price' => $unit_price,
                        'last_quantity' => 0

                    ]);
                    $status = 200;
                    $response = [
                        'success' => 'Purchase transaction saved and new stock created successfully  !',
                        'purchase' => $purchase,
                        'stock' => $stock,
                    ];

                } else {

                    $currentqtty = $stock_check->quantity;
                    $newqtty = $currentqtty + $quantity;
                    $stock_check->quantity = $newqtty;
                    $stock_check->last_quantity = $currentqtty;

                    if($stock_check->save()){

                        $status = 201;
                        $response = [
                            'success' => 'Purchase transaction saved and stock added successfully!',
                            'purchase' => $purchase,
                            'stock' => $stock_check,
                            'quantity_added' => $quantity
                        ];

                    } else {

                        $status = 201;
                        $response = [
                            'error' => 'Purchase transaction saved successfully but stock not added !',
                        ];

                    }
                }

            } else {

                $status = 422;
                $response = [
                    'error' => 'Error, failed to store purchase transaction !',
                ];

            }
        }

        return response()->json($response, $status);

    }

    public function edit(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer',
            'price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return $this->error('error', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
        }

        $purchase = Purchase::findOrFail($id);

        $editPurchaseData = $validator->validated();

        $purchase->quantity = $editPurchaseData['quantity'];
        $purchase->price = $editPurchaseData['price'];

        if ($purchase->save()) {
            $status = 201;
            $response = [
                'success' => 'Purchase edited successfully!',
                'purchase' => $purchase,
            ];
        } else {
            $status = 422;
            $response = [
                'error' => 'Error, failed to edit the purchase transaction!',
            ];
        }

        return response()->json($response, $status);
    }

    public function show(Request $request, $id)
    {

        $storeData = json_decode($request->store, true);
        $store_id = $storeData['id'];

        $purchase = Purchase::where('id', $id)
                             ->where('store_id', $store_id)
                             ->first();
    
        if ($purchase) {
            $status = 200;
            $response = [
                'success' => 'Purchase',
                'purchase' => $purchase,
            ];
        } else {
            $status = 422;
            $response = [
                'error' => 'error, failed to find the purchase transaction',
            ];
        }
    
        return response()->json($response, $status);
    }

    // public function delete(Request $request, $id)
    // {

    //     $storeData = json_decode($request->store, true);
    //     $store_id = $storeData['id'];

    //     $purchase = Purchase::where('id', $id)
    //                          ->where('store_id', $store_id)
    //                          ->first();

    //     if($purchase->delete()){
    //         $status = 200;
    //         $response = [
    //             'success' => 'Purchase transaction deleted successfully',
    //         ];
    //     } else {
    //         $status = 422;
    //         $response = [
    //             'error' => 'error, failed to delete purchase transaction',
    //         ];
    //     }

    //     return response()->json($response, $status);
    // }
}

