<?php

namespace App\Http\Controllers\api\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
class RolesController extends Controller
{
    public function index(): JsonResponse
    {
        $roles = Role::all();
        return response()->json($roles, 200);
    }
}
