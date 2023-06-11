<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use App\Services\Admin\AdminService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }


    public function login(Request $request)
    {

        try {
            $rules = [
                "email" => "required",
                "password" => "required"
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $credentials = $request->only(['email', 'password']);

            $token = Auth::guard('api')->attempt($credentials);


            if (!$token)
                return response()->json(['error' => 'Unauthorized'], 401);

            $user = Auth::guard('api')->user();

            resolve(AdminService::careteAttendance());

            return response()->json(['token' => $token, 'user' => $user]);
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], $ex->getCode());
        }
    }


    // public function register(Request $request)
    // {

    //     $validator = Validator::make($request->all(), [
    //         'name' => 'required|string|between:2,100',
    //         'email' => 'required|string|email|max:100|unique:users',
    //         'password' => 'required|string|min:6',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json($validator->errors()->toJson(), 400);
    //     }

    //     $user = User::create(array_merge(
    //         $validator->validated(),
    //         ['password' => bcrypt($request->password)]
    //     ));

    //     return response()->json([
    //         'message' => 'User successfully registered',
    //         'user' => $user
    //     ], 201);
    // }


    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'User successfully signed out']);
    }

    protected function respondWithToken($token, $user)
    {
        return response()->json([
            'token' => $token,
            'user' => $user
        ]);
    }
}