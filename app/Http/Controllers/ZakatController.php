<?php

namespace App\Http\Controllers;

use App\Models\Zakat;
use Illuminate\Http\Request;
use App\Models\ZakatProducts;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class ZakatController extends Controller implements HasMiddleware
{

    public static function middleware()
    {
        return [
            new Middleware(['auth:sanctum'], except: ['index', 'show'])
        ];
    }

    public function userZakats(): JsonResponse
    {
        try {
            $user = auth('sanctum')->user();
            $userZakats = $user->zakats;
            return response()->json([
                'status' => true,
                'data' => $userZakats,
                'message' => "Successful get all user's zakats"
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
                'data' => Zakat::all(),
                'message' => "Successful get all zakats"
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
            DB::beginTransaction();

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

            DB::commit();

            return response()->json([
                'status' => true,
                'data' => $zakat,
                'message' => "Successful insert new zakat"
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
    public function show(Zakat $zakat)
    {
        try {

            return response()->json([
                'status' => true,
                'data' => $zakat,
                'message' => "Successful show zakat's info"
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function showZakatProducts(ZakatProducts $zakatProducts, Zakat $zakat)
    {
        try {
            $zakatProducts = $zakat->zakatProducts()->get();
            return response()->json([
                'status' => true,
                'data' => $zakatProducts,
                'message' => "Successful show zakat's products info"
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
    public function update(Request $request, Zakat $zakat)
    {
        try {
            $fields = $request->validate([
                'membersCount' => 'required',
                'zakatValue' => 'required|max:10',
                'remainValue' => 'required|max:10'
            ]);

            // relation between user and products to take user_id for product
            $zakat->update($fields);

            return response()->json([
                'status' => true,
                'data' => $zakat,
                'message' => "Successful update zakat's info"
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
    public function destroy(Zakat $zakat)
    {
        try {
            // modify is the function in ProductPolicy file
            Gate::authorize('modify', $zakat);

            $zakat->delete();
            return response()->json([
                'status' => true,
                'message' => "Successful delete zakat's info"
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function deleteAllUserZakats()
    {
        try {
            $user = auth('sanctum')->user();
            $user->zakats()->delete();
            return response()->json([
                'status' => true,
                'message' => "Successful delete all of zakat's info"
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }


    public function getUserProductTotals()
    {
        try {
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

            return response()->json([
                'status' => true,
                'data' => $productTotals,
                'message' => "Successful get all of products' total"
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
