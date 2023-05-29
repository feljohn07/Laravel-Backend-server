<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
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

        $purchase = Purchase::
            with('supplier')
            ->with('product')
            ->orderBy('created_at', 'desc')
            ->paginate($limit);

        return response()->json([
            "success" => true,
            "status" => 200,
            "request" => $request->attributes,
            "message" => "Purchase List",
            "data" => $purchase
        ]);
    }

    public function getSuppliers(Request $request)
    {
        $suppliers = Supplier::all();
        return response()->json([
            "success" => true,
            "status" => 200,
            "message" => "Supplier Lists",
            "data" => $suppliers
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $input = $request->all();

        // product_name
        // minimum_quantity
        // retail_price
        // quantity_on_hand
        $validator = Validator::make($input, [
            'product_id' => 'required',
            'supplier_id' => 'required',
            'purchase_date' => 'required',
            'purchase_price' => 'required',
            'purchase_quantity' => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                "status" => 400,
                "success" => false,
                "message" => "Validation Error.",
                "data" => $validator->errors()
            ]);
        }

        $purchase = Purchase::create($input);

        $product = Product::find($input['product_id']);
        $product->quantity_on_hand =  $product->quantity_on_hand + $input['purchase_quantity'];
        $product->save();

        return response()->json([
            "status" => 200,
            "success" => true,
            "message" => "Purchase created successfully.",
            "data" =>  $purchase
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request )
    {
        //
        $input = $request;

        $purchase = Purchase::find($input->id);

        if($purchase){
            return response()->json([
                "status" => 200,
                "success" => true,
                "message" => "Purchase Found.",
                "request" => $input->attributes,
                "data" =>  $purchase
            ]);
        }else{
            return response()->json([
                "status" => 404,
                "success" => false,
                "message" => "Purchase Not Found.",
                "request" => $input->attributes,
                "data" =>  $purchase
            ]);

        }


    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id )
    {
        $input = $request->all();

        $purchase = Purchase::find($id);

        $purchase->update($request->all());

        return response()->json([
            "status" => 200,
            "success" => true,
            "message" => "Product updated successfully.",
            "data" =>  $purchase
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id )
    {
        $purchase = Purchase::destroy($id);

        return response()->json([
            "status" => 200,
            "success" => true,
            "message" => "Purchase deleted successfully.",
            "data" =>  $purchase
        ]);
    }
}
