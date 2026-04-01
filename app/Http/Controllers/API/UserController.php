<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getData()
    {
        $users = User::select("id", "name")->get();
        return response()->json($users, 200);
    }
}
