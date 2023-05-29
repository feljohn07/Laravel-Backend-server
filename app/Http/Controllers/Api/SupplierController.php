<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Validator;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $param = $request->all();

        $limit = $param['limit'] ?? 10;
        $query = $param['query'] ?? "";

        $supplier = Supplier::
            where('name', 'like', '%' . $query . '%')
            ->orWhere('address', 'like', '%' . $query . '%')
            ->orderBy('created_at', 'desc')
            ->paginate($limit);

        return response()->json([
            "success" => true,
            "status" => 200,
            "request" => $request->attributes,
            "message" => "Supplier List",
            "data" => $supplier
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required',
            'address' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                "status" => 400,
                "success" => false,
                "message" => "Validation Error.",
                "data" => $validator->errors()
            ]);
        }

        $supplier = Supplier::create($input);

        return response()->json([
            "status" => 200,
            "success" => true,
            "message" => "Supplier created successfully.",
            "data" =>  $supplier
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request )
    {
        //
        $input = $request;

        $supplier = Supplier::find($input->id);

        return response()->json([
            "status" => 200,
            "success" => true,
            "message" => "Supplier Found.",
            "request" => $input->attributes,
            "data" =>  $supplier
        ]);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request )
    {
        //
        $input = $request;

        $supplier = Supplier::find($input->id);

        $supplier->name = $input->name;
        $supplier->address = $input->address;
        $supplier->save();

        return response()->json([
            "status" => 200,
            "success" => true,
            "message" => "Supplier updated successfully.",
            "data" =>  $supplier
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request )
    {
        //
        $input = $request;
        $supplier = Supplier::destroy($input->id);

        return response()->json([
            "status" => 200,
            "success" => true,
            "message" => "Supplier deleted successfully.",
            "data" =>  $supplier
        ]);
    }

    public function search(Request $request)
    {
        $input = $request;

        $supplier = Supplier::where('name', 'like', '%' . $input->name . '%')
            ->get();

        return response()->json([
            "status" => 200,
            "success" => true,
            "message" => "Supplier Found.",
            "request" => $input->attributes,
            "data" =>  $supplier
        ]);
    }
}

