<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Validator;
use App\User;

class UserController extends Controller {

    // Class Constructor
    // Methods in 'except' are not authenticated by the auth middleware.
    public function __construct() {
        $this->middleware('auth:api', [
            'except' => ['login', 'register', 'index', 'destroy']
        ]);
        $this->middleware('auth:admins', [
            'only' => ['register', 'index', 'destroy']
        ]);
    }

    // REGISTER
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'phone' => 'required|unique:users',
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
            $user = User::create($input);
            $userData = [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'phone' => $input['phone']
            ];
            return response()->json([
                'code' => 200,
                'status' => true,
                'data' => $userData,
                'message' => 'User Registered'
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
            if(!$token = auth()->attempt($validator->validated())) {
                return response()->json([
                    'code' => 401,
                    'status' => false,
                    'message' => 'Invalid Credentials. User Unauthorized.'
                ], 401);
            } else {
                $user = auth()->user();
                $data = ['id' => $user->id, 'name' => $user->name, 'email' => $user->email];
                $data['session_token'] = $token;
                return response()->json([
                    'code' => 200,
                    'status' => true,
                    'data' => $data,
                    'message' => "User logged in successfully"
                ]);
            }
        }
    }

    // LOGOUT
    public function logout() {
        auth()->logout();
        return response()->json([
            'code' => 200,
            'status' => true,
            'message' => 'User logged out successfully'
        ]);
    }

    // Refresh Token
    public function refresh() {
        return response()->json([
            'code' => 200,
            'status' => true,
            'message' => 'Token Refreshed',
            'user' => auth()->user(),
            'session' => $this->createNewToken(auth()->refresh())
        ]);
    }

    protected function createNewToken($token) {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ];
    }

    // Get User Details
    public function profile() {
        return response()->json(auth()->user());
    }

    // REGISTRATIONS CLOSED
    public function registrationsClosed(Request $request) {
        return response()->json([
            'code' => 403,
            'status' => false,
            'message' => 'User Registrations are Closed'
        ]);
    }

    public function index() {
        return response()->json([
            'code' => 200,
            'status' => true,
            'data' => User::all()
        ]);
    }

    public function destroy(User $user) {
        $user->delete();
        return response()->json([
            'code' => 200,
            'status' => true,
            'message' => 'User Deleted successfully'
        ]);
    }

}
