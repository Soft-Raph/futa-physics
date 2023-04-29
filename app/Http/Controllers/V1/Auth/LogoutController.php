<?php

namespace App\Http\Controllers\V1\Auth;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $request->user()->token()->revoke();

            return ResponseHelper::success(null, 'Log out successful', 200);
        } catch (\Exception $e) {
            return ResponseHelper::error(500, $e->getMessage());
        }
    }
}
