<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\Customer;
use Illuminate\Http\Request;
use Validator;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $param = $request->all();

        $limit = $param['limit'] ?? 10;
        $query = $param['query'] ?? "";

        $order = Order::
            with('customer')
            ->with('product')
            ->orderBy('created_at', 'desc')
            ->paginate($limit);

        return response()->json([
            "success" => true,
            "status" => 200,
            "request" => $request->attributes,
            "message" => "Order List",
            "data" => $order
        ]);
    }

    public function getCustomers(Request $request)
    {
        $customer = Customer::all();
        return response()->json([
            "success" => true,
            "status" => 200,
            "message" => "Customer Lists",
            "data" => $customer
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'product_id' => 'required',
            'customer_id' => 'required',
            'order_date' => 'required',
            'order_price' => 'required',
            'order_quantity' => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                "status" => 400,
                "success" => false,
                "message" => "Validation Error.",
                "data" => $validator->errors()
            ]);
        }

        $order = Order::create($input);

        $product = Product::find($input['product_id']);
        $product->quantity_on_hand =  $product->quantity_on_hand - $input['order_quantity'];
        $product->save();

        return response()->json([
            "status" => 200,
            "success" => true,
            "message" => "Order created successfully.",
            "data" =>  $order
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request )
    {
        //
        $input = $request;

        $order = Order::find($input->id);

        return response()->json([
            "status" => 200,
            "success" => true,
            "message" => "Order Found.",
            "request" => $input->attributes,
            "data" =>  $order
        ]);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id )
    {

        $order = Order::find($id);

        $order->update($request->all());

        return response()->json([
            "status" => 200,
            "success" => true,
            "message" => "Order updated successfully.",
            "data" =>  $order
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id )
    {
        $order = Order::destroy($id);

        return response()->json([
            "status" => 200,
            "success" => true,
            "message" => "Order deleted successfully.",
            "data" =>  $order
        ]);
    }

}
