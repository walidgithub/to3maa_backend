<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller implements HasMiddleware
{

    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show', 'userProducts'])
        ];
    }

    public function userProducts(User $user)
    {
        $userProducts = $user->products()->get();
        return [
            'data' => $userProducts
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return [
            'data' => Product::all()
        ];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
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
        return [
            'data' => $product
        ];
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return [
            'data' => $product
        ];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
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
        return [
            'data' => $product
        ];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        // modify is the function in ProductPolicy file
        Gate::authorize('modify', $product);

        $product->delete();
        return ['message' => 'this product is deleted'];
    }
}
