<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Order;

use Illuminate\Http\Request;
use Validator;

use Illuminate\Support\Facades\DB;


class ProductController extends Controller
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
            select(
                '*',
                'product_name',
                'minimum_quantity',
                'retail_price',
                'quantity_on_hand',
                'created_at',
                DB::raw('(SELECT SUM(order_quantity) FROM orders WHERE product_id = products.id) AS total_orders'),
                DB::raw('(SELECT SUM(purchase_quantity) FROM purchases WHERE product_id = products.id) AS total_purchases')
            )
            ->where('product_name', 'like', '%' . $query . '%')
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

        // return response()->json($input);

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

        if($product){
            return response()->json([
                "status" => 200,
                "success" => true,
                "message" => "Product Found.",
                "request" => $input->attributes,
                "data" =>  $product
            ]);
        }else{
            return response()->json([
                "status" => 404,
                "success" => true,
                "message" => "Product Not Found.",
                "request" => $input->attributes,
                "data" =>  $product
            ]);
        }

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id )
    {
        //
        $input = $request;

        $product = Product::find($id);

        // product_name
        // retail_price
        // quantity_on_hand

        $product->update($request->all());

        return response()->json([
            "status" => 200,
            "success" => true,
            "message" => "Product updated successfully.",
            "data" => $product
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

    public function dashboard(Request $request )
    {
        //
        $input = $request;

        // $dashboard = DB::('(SELECT Count(product_name) FROM products) AS total_products');
                // DB::raw('(SELECT SUM(purchase_quantity) FROM purchases WHERE product_id = products.id) AS total_purchases'),
                // DB::raw('(SELECT SUM(purchase_quantity) FROM purchases WHERE product_id = products.id) AS total_purchases')

        $dashboard = [
            "total_products" => Product::count(),
            "inventory" => Product::select(
                DB::raw('SUM(quantity_on_hand) as total'),
                DB::raw('SUM(quantity_on_hand * retail_price) as inventory_value'),
            )->first(),
            "Purchase" => Purchase::select(
                DB::raw('SUM(purchase_quantity) as total'),
                DB::raw('SUM(purchase_quantity * purchase_price) as purchase_value'),
            )->first(),
            "Order" => Order::select(
                DB::raw('SUM(order_quantity) as total'),
                DB::raw('SUM(order_quantity * order_price) as order_value'),
            )->first(),
        ];

        return response()->json([
            "status" => 200,
            "success" => true,
            "message" => "Product List",
            "data" => $dashboard
        ]);
    }

}
