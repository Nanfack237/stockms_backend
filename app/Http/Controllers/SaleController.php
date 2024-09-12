<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Customer;
use App\Models\Store;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class SaleController extends Controller
{
    public function list(Request $request){

        $storeData = json_decode($request->store, true);
        $store_id = $storeData['id'];

        $sales = Sale::where('store_id', $store_id)->get();

        if($sales){

            $status = 200;
            $response = [
                'success' => 'Sales',
                'sales' => $sales,
            ];

        } else {

            $status = 422;
            $response = [
                'error' => 'Error, failed to find sales',
            ];

        }

        return response()->json($response, $status);

    }

    public function store(Request $request){

        $validator = Validator::make($request->all(), [

            'product_id' => 'required|exists:products,id',
            'stock_id' => 'required|exists:stocks,id',
            'quantity' => 'required|integer',
            'date' => 'required|date',
            'unit_price' => 'required|integer|min:0',
            'customer_name' => 'required|string',
            'customer_contact' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $storeSaleData = $validator->validated();
        $product_id = $storeSaleData['product_id'];
        $stock_id = $storeSaleData['stock_id'];
        $quantity = $storeSaleData['quantity'];
        $date = $storeSaleData['date'];
        $unit_price = $storeSaleData['unit_price'];
        $total_price = $quantity * $unit_price;
        $customer_name = $storeSaleData['customer_name'];
        $customer_contact = $storeSaleData['customer_contact'];

        $storeData = json_decode($request->store, true);
        $store_id = $storeData['id'];

        $product = Product::find($product_id);
        $stock_check = Stock::where('product_id', $product_id)->where('store_id', $store_id)->where('id', $stock_id)->first();
        
        $currentqtty = $stock_check->quantity;

        if($quantity > $currentqtty)
        {

            $status = 422;
            $response = [
                'error' => 'The quantity demanded is greater than the quantity in stock!',
                'quantity_remaining' => $currentqtty
            ];

        } else if($quantity == $currentqtty){

            $status = 422;
            $response = [
                'error' => 'The quantity demanded is the exact quantity in stock',
            ];
        } else if($currentqtty <= 2){

            $status = 422;
            $response = [
                'error' => 'The stock is in shortage',
                'stock_quantity' => $currentqtty
            ];
        
        } else {

            $sale = Sale::create([

                'product_id' => $storeSaleData['product_id'],
                'stock_id' => $storeSaleData['stock_id'],
                'store_id' => $store_id,
                'quantity' => $storeSaleData['quantity'],
                'date' => $storeSaleData['date'],
                'unit_price' => $storeSaleData['unit_price'],
                'total_price' => $total_price,
                'customer_name' => $storeSaleData['customer_name'],
                'customer_contact' => $storeSaleData['customer_contact'],
                'status' => 1

            ]);

            if($sale){

                $customer = Customer::create([

                    'name' => $customer_name,
                    'contact' => $customer_contact,
                    'store_id' => $store_id,
                    'status' => 1
    
                ]);

                $currentqtty = $stock_check->quantity;
                $stock_check->last_quantity = $currentqtty;
                $newqtty = $currentqtty - $quantity;
                $stock_check->quantity = $newqtty;

                if($stock_check->save()){

                    $status = 201;
                    $response = [
                        'success' => 'Sale transaction stored and stock deduced successfully!',
                        'sale' => $sale,
                        'stock' => $stock_check,
                        'quantity_deduced' => $quantity
                    ];

                } else {

                    $status = 422;
                    $response = [
                        'error' => 'Error, Sale transaction saved but stock nor deduce!',
                    ];

                }

            } else {

                $status = 422;
                $response = [
                    'error' => 'Error, failed to store sale transaction  !',
                ];

            }

        }

        return response()->json($response, $status);

    }

    public function edit(Request $request, $id)
    {

        $storeData = json_decode($request->store, true);
        $store_id = $storeData['id'];

        $validator = Validator::make($request->all(), [

            'product_id' => 'required|exists:products,id',
            'stock_id' => 'required|exists:stocks,id',
            'quantity' => 'required|integer',
            'date' => 'required|date',
            'unit_price' => 'required|integer|min:0',
            'total_price' => 'required|integer|min:0',
            'customer_name' => 'required|string',
            'customer_contact' => 'required|integer',

        ]);

        if ($validator->fails()) {
            return $this->error('error', Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
        }

        $sale = Sale::findOrFail($id);

        $editSaleData = $validator->validated();
        $product_id = $editSaleData['product_id'];
        $store_id = $store_id;
        $stock_id = $editSaleData['stock_id'];
        $quantity = $editSaleData['quantity'];
        $date = $editSaleData['date'];
        $unit_price = $editSaleData['unit_price'];
        $total_price = $quantity * $unit_price;
        $customer_name = $editSaleData['customer_name'];
        $customer_contact = $editSaleData['customer_contact'];

        if ($sale->save()) {

            $status = 201;
            $response = [
                'success' => 'Sale edited successfully!',
                'sale' => $sale,
            ];

        } else {

            $status = 422;
            $response = [
                'error' => 'Error, failed to edit the sale!',
            ];

        }

        return response()->json($response, $status);
    }

    public function show(Request $request, $id)
    {

        
        $storeData = json_decode($request->store, true);
        $store_id = $storeData['id'];

        $sale = Sale::where('id', $id)
                             ->where('store_id', $store_id)
                             ->first();

        if($sale){

            $status = 200;
            $response = [
                'success' => 'Sale',
                'sale' => $sale,
            ];

        } else {

            $status = 422;
            $response = [
                'error' => 'error, failed to find the sale transaction',
            ];

        }

        return response()->json($response, $status);

    }

    // public function delete(Request $request, $id)
    // {

    //     $storeData = json_decode($request->store, true);
    //     $store_id = $storeData['id'];

    //     $sale = Sale::where('id', $id)
    //                          ->where('store_id', $store_id)
    //                          ->first();

    //     if($sale->delete()){

    //         $status = 200;
    //         $response = [
    //             'success' => 'Sale transaction deleted successfully',
    //         ];

    //     } else {

    //         $status = 422;
    //         $response = [
    //             'error' => 'error, failed to delete sale transaction',
    //         ];

    //     }

    //     return response()->json($response, $status);
    // }
}
