<?php
namespace App\Http\Controllers;

use App\Http\Requests\ApiTokenRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ApiTokenController extends Controller
{
    public function createToken(ApiTokenRequest $request): JsonResponse
    {

        // Check in user already have some token
        if ($request->bearerToken()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unnecessary token attached',
            ], 400);
        };

        try {
            //Find user and check use data
            $user = User::query()->where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => 'error',
                    'data' => 'The provided credentials are incorrect'],
                    401);
            }
            // Token issuing
            $token = $user->createToken($request->email);
            return response()->json([
                'status' => 'ok',
                'token' => $token->plainTextToken,
            ], 200);

        } catch (\Exception $e) {
            // DB error response
            return response()->json([
                'status' => 'error',
                'data' => 'Token issuing error'],
                500);
        }
    }

    public function clearToken(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $user->tokens()->delete();
            return response()->json([
                'status' => 'ok',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}
