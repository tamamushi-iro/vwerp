<?php

namespace App\Http\Controllers;

use App\Admin;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller {

    // Class Constructor
    // Methods in 'except' are not authenticated by the auth middleware.
    public function __construct() {
        $this->middleware('auth:admins', [
            'except' => ['login', 'register']
        ]);
    }

    // REGISTER
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:admins',
            'password' => 'required|string|confirmed|min:6'
        ]);

        if($validator->fails()) {
            return response()->json([
                'code' => 400,
                'status' => false,
                'message' => count($validator->errors()) > 4 ? 'Bad Request' : $validator->errors()
            ], 400);
        } else {
            $input = array_merge($validator->validated(), ['password' => bcrypt($request['password'])]);
            $admin = Admin::create($input);
            return response()->json([
                'code' => 200,
                'status' => true,
                'data' => $admin,
                'message' => 'Admin Registered'
            ]);
        }
    }

    // LOGIN
    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        if($validator->fails()) {
            return response()->json([
                'code' => 400,
                'status' => false,
                'message' => $validator->errors()
            ], 400);
        } else {
            // If security problems, use: attempt(['email' => request('email'), 'password' => request('password')])
            if(!$token = Auth::guard('admins')->attempt($validator->validated())) {
                return response()->json([
                    'code' => 401,
                    'status' => false,
                    'message' => 'Invalid Credentials. Admin Unauthorized.'
                ], 401);
            } else {
                $admin = Auth::guard('admins')->user();
                $data = $admin;
                $data['session_token'] = $token;
                return response()->json([
                    'code' => 200,
                    'status' => true,
                    'data' => $admin,
                    'message' => "Admin logged in successfully"
                ]);
            }
        }
    }

    // LOGOUT
    public function logout() {
        Auth::guard('admins')->logout();
        return response()->json([
            'code' => 200,
            'status' => true,
            'message' => 'Admin logged out successfully'
        ]);
    }

    // Refresh Token
    public function refresh() {
        return response()->json([
            'code' => 200,
            'status' => true,
            'message' => 'Token Refreshed',
            'admin' => Auth::guard('admins')->user(),
            'session' => $this->createNewToken(Auth::guard('admins')->refresh())
        ]);
    }

    protected function createNewToken($token) {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('admins')->factory()->getTTL() * 60
        ];
    }

    // Get User Details
    public function profile() {
        return response()->json(Auth::guard('admins')->user());
    }

    // REGISTRATIONS CLOSED
    public function registrationsClosed(Request $request) {
        return response()->json([
            'code' => 403,
            'status' => false,
            'message' => 'Registrations are Closed for Admins'
        ]);
    }
    
}
