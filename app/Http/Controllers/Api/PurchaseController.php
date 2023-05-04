<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Validator;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $param = $request->all();

        $limit = $param['limit'] ?? 10;
        $query = $param['query'] ?? "";

        $product = Product::
            where('product_name', 'like', '%' . $query . '%')
            ->orderBy('created_at', 'desc')
            ->paginate($limit);

        return response()->json([
            "success" => true,
            "status" => 200,
            "request" => $request->attributes,
            "message" => "Product List",
            "data" => $product
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $input = $request->all();

        // product_name
        // minimum_quantity
        // retail_price
        // quantity_on_hand
        $validator = Validator::make($input, [
            'product_name' => 'required',
            'minimum_quantity' => 'required',
            'retail_price' => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                "status" => 400,
                "success" => false,
                "message" => "Validation Error.",
                "data" => $validator->errors()
            ]);
        }

        $product = Product::create($input);

        return response()->json([
            "status" => 200,
            "success" => true,
            "message" => "Product created successfully.",
            "data" =>  $product
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request )
    {
        //
        $input = $request;

        $product = Product::find($input->id);

        return response()->json([
            "status" => 200,
            "success" => true,
            "message" => "Product Found.",
            "request" => $input->attributes,
            "data" =>  $product
        ]);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request )
    {
        //
        $input = $request;

        return response()->json([
            $input->product_name
        ]);

        $product = Product::find($input->id);

        // product_name
        // retail_price
        // quantity_on_hand

        $product->product_name = $input->product_name;
        $product->minimum_quantity = $input->minimum_quantity;
        $product->retail_price = $input->retail_price;
        $product->save();

        return response()->json([
            "status" => 200,
            "success" => true,
            "message" => "Product updated successfully.",
            "data" =>  $product
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request )
    {
        //
        $input = $request;
        $product = Product::destroy($input->id);

        return response()->json([
            "status" => 200,
            "success" => true,
            "message" => "Product deleted successfully.",
            "data" =>  $product
        ]);
    }

    // public function search(Request $request)
    // {
    //     $input = $request;

    //     $customer = Customer::where('name', 'like', '%' . $input->name . '%')
    //         ->get();

    //     return response()->json([
    //         "status" => 200,
    //         "success" => true,
    //         "message" => "Customer Found.",
    //         "request" => $input->attributes,
    //         "data" =>  $customer
    //     ]);
    // }
}
