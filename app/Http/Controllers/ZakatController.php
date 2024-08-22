<?php

namespace App\Http\Controllers;

use App\Models\Zakat;
use App\Models\ZakatProducts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class ZakatController extends Controller implements HasMiddleware
{

    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show'])
        ];
    }

    public function userZakats()
    {
        $user = auth('sanctum')->user();
        $userZakats = $user->zakats;
        return [
            'data' => $userZakats
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return [
            'data' => Zakat::all()
        ];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $zakatFields = $request->validate([
            'membersCount' => 'required',
            'zakatValue' => 'required|max:10',
            'remainValue' => 'required|max:10',
            'zakatProducts.*.productName' => 'required|max:255',
            'zakatProducts.*.productPrice' => 'required|max:10',
            'zakatProducts.*.productDesc' => 'max:255',
            'zakatProducts.*.productImage' => 'required|max:255',
            'zakatProducts.*.sa3Weight' => 'required',
            'zakatProducts.*.productQuantity' => 'required'
        ]);

        $zakat = $request->user()->zakats()->create($zakatFields);

        $zakatProductsResult = [];

        foreach ($zakatFields['zakatProducts'] as $product) {
            $zakatProductsResult[] = $zakat->zakatProducts()->create([
                'productName' => $product['productName'],
                'productPrice' => $product['productPrice'],
                'productDesc' => $product['productDesc'],
                'productImage' => $product['productImage'],
                'sa3Weight' => $product['sa3Weight'],
                'productQuantity' => $product['productQuantity'],
            ]);
        }

        $zakat['zakatProducts'] = $zakatProductsResult;
        return [
            'data' => $zakat
        ];
    }

    /**
     * Display the specified resource.
     */
    public function show(Zakat $zakat)
    {
        return [
            'data' => $zakat
        ];
    }

    public function showZakatProducts(ZakatProducts $zakatProducts, Zakat $zakat)
    {
        $zakatProducts = $zakat->zakatProducts()->get();
        return [
            'data' => $zakatProducts
        ];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Zakat $zakat)
    {
        $fields = $request->validate([
            'membersCount' => 'required',
            'zakatValue' => 'required|max:10',
            'remainValue' => 'required|max:10'
        ]);

        // relation between user and products to take user_id for product
        $zakat->update($fields);
        return [
            'data' => $zakat
        ];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Zakat $zakat)
    {
        // modify is the function in ProductPolicy file
        Gate::authorize('modify', $zakat);

        $zakat->delete();
        return ['message' => 'this zakat is deleted'];
    }

    public function deleteAllUserZakats()
    {
        $user = auth('sanctum')->user();
        $user->zakats()->delete();
        return ['message' => 'all zakats are deleted'];
    }


    public function getUserProductTotals()
    {
        $user = auth('sanctum')->user();

        $productTotals = collect($user->zakatProducts)
            ->groupBy('productName')
            ->map(function ($items) {
                return [
                    'productName' => $items->first()->productName,
                    'productPrice' => $items->first()->productPrice,
                    'productDesc' => $items->first()->productDesc,
                    'productImage' => $items->first()->productImage,
                    'sa3Weight' => $items->first()->sa3Weight,
                    'productTotals' => array_sum($items->pluck('productQuantity')->toArray())
                ];
            })
            ->values();

        return [
            'data' => $productTotals
        ];
    }
}
