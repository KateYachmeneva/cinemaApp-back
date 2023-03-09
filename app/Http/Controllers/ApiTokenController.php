<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ApiTokenController extends Controller
{

    public function createToken(Request $request): \Illuminate\Http\JsonResponse|array
    {
        $validate = Validator::make($request->all(), [
            'email' => ['required', 'string', 'max 255'],
            'password' => ['required', 'string', 'min:8'],
            'device_name' => ['required', 'string'],
        ]);
        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors(),401]);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'The provided credentials are incorrect'], 401);
        }

        $token=$user->createToken($request->email);
        return['token'=>$token->plainTextToken];
    }

}
