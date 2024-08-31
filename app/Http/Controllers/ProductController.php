<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class ProductController extends Controller implements HasMiddleware
{

    public static function middleware()
    {
        return [
            new Middleware(['auth:sanctum'], except: ['index', 'show'])
        ];
    }

    public function userProducts(): JsonResponse
    {
        try {
            $user = auth('sanctum')->user();
    
            $userProducts = $user->products;

            return response()->json([
                'status' => true,
                'data' => $userProducts,
                'message' => "Successful get user's products"
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            return response()->json([
                'status' => true,
                'data' => Product::all(),
                'message' => "Successful get all products"
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $fields = $request->validate([
                'productName' => 'required|max:255',
                'productPrice' => 'required|max:10',
                'productDesc' => 'max:255',
                'productImage' => 'required|max:255',
                'sa3Weight' => 'required',
                'productQuantity' => 'required',
            ]);
    
            // relation between user and products to take user_id for product
            $product = $request->user()->products()->create($fields);

            return response()->json([
                'status' => true,
                'data' => $product,
                'message' => "Successful insert new product"
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        try {

            return response()->json([
                'status' => true,
                'data' => $product,
                'message' => "Successful show product's info"
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        try {
            // modify is the function in ProductPolicy file
            Gate::authorize('modify', $product);
    
            $fields = $request->validate([
                'productName' => 'required|max:255',
                'productPrice' => 'required|max:10',
                'productDesc' => 'required|max:255',
                'productImage' => 'required|max:255',
                'sa3Weight' => 'required',
                'productQuantity' => 'required',
            ]);
    
            $product->update($fields);

            return response()->json([
                'status' => true,
                'data' => $product,
                'message' => "Successful update product's info"
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        try {
            // modify is the function in ProductPolicy file
            Gate::authorize('modify', $product);
    
            $product->delete();

            return response()->json([
                'status' => true,
                'message' => "Successful delete product's info"
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
