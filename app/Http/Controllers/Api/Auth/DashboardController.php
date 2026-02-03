<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function datos(Request $request)
    {
        return response()->json([
            'number' => 1,
            'message' => 'Usuario autenticado.',
            'user' => $request->user()
        ]);
    }
}
