<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Device;
use App\Models\Access;
use Illuminate\Database\QueryException as QueryException;
use App\Http\Requests\UserRequest;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {   
        
    }

    /**
     * Retreive a resource by its id.
     */
    public function show(Int $userId)
    {
        $query = User::where('id', $userId);

        if ($query->count() < 1) {
            return response()->json(null, 404);
        }

        return response()->json($query->first());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $device)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $device)
    {
        //
    }

    public function getDevices(Request $request, $userId)
    {
        // Check wheter an user with this id exists
        $userQuery = User::where('id', $userId);

        if ($userQuery->count() < 1) {
            return response()->json(null, 404);
        }

        $accesses = Access::where('user_id', $userId)->get();

        $devicesIds = array_map(function ($access) {
            return $access['device_id'];
        }, $accesses->toArray());

        return response()->json(Device::whereIn('id', $devicesIds)->get());
    }
}
