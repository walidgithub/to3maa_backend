<?php

namespace App\Http\Controllers;

use App\Models\Zakat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class ZakatController extends Controller implements HasMiddleware
{

    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show', 'userZakats'])
        ];
    }

    public function userZakats(User $user)
    {
        $userZakats = $user->zakats()->get();
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
        $fields = $request->validate([
            'membersCount' => 'required',
            'zakatValue' => 'required|max:10',
            'remainValue' => 'required|max:10'
        ]);

        // relation between user and products to take user_id for product
        $zakat = $request->user()->zakats()->create($fields);
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
}
