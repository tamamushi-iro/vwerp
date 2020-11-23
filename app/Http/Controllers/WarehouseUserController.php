<?php

namespace App\Http\Controllers;

use Validator;
use App\WarehouseUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WarehouseUserController extends Controller
{

    // Class Constructor
    // Methods in 'except' are not authenticated by the auth middleware.
    public function __construct() {
        $this->middleware('auth:whusers', [
            'except' => ['login', 'register', 'index', 'destroy']
        ]);
        $this->middleware('auth:api,admins', [
            'only' => ['register', 'index', 'destroy']
        ]);
    }

    // REGISTER
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:warehouse_users',
            'phone' => 'required|unique:warehouse_users',
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
            $whuser = WarehouseUser::create($input);
            $whuserData = [
                'id' => $whuser['id'],
                'name' => $whuser['name'],
                'email' => $whuser['email'],
                'phone' => $whuser['phone']
            ];
            return response()->json([
                'code' => 200,
                'status' => true,
                'data' => $whuserData,
                'message' => 'Warehouse User Registered'
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
            if(!$token = Auth::guard('whusers')->attempt($validator->validated())) {
                return response()->json([
                    'code' => 401,
                    'status' => false,
                    'message' => 'Invalid Credentials. Whuser Unauthorized.'
                ], 401);
            } else {
                $data = Auth::guard('whusers')->user();
                unset($data['created_at']);
                unset($data['updated_at']);
                $data['session_token'] = $token;
                return response()->json([
                    'code' => 200,
                    'status' => true,
                    'data' => $data,
                    'message' => "Warehouse User logged in successfully"
                ]);
            }
        }
    }

    // LOGOUT
    public function logout() {
        Auth::guard('whusers')->logout();
        return response()->json([
            'code' => 200,
            'status' => true,
            'message' => 'Warehouse User logged out successfully'
        ]);
    }

    // Refresh Token
    public function refresh() {
        return response()->json([
            'code' => 200,
            'status' => true,
            'message' => 'Token Refreshed',
            'admin' => Auth::guard('whusers')->user(),
            'session' => $this->createNewToken(Auth::guard('whusers')->refresh())
        ]);
    }

    protected function createNewToken($token) {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('whusers')->factory()->getTTL() * 60
        ];
    }

    // Get User Details
    public function profile() {
        return response()->json(Auth::guard('whusers')->user());
    }

    // REGISTRATIONS CLOSED - NOT USED.
    public function registrationsClosed(Request $request) {
        return response()->json([
            'code' => 403,
            'status' => false,
            'message' => ' Warehouse User registrations are Closed'
        ]);
    }

    public function index() {
        return response()->json([
            'code' => 200,
            'status' => true,
            'data' => WarehouseUser::all()
        ]);
    }

    public function destroy(WarehouseUser $whuser) {
        $whuser->delete();
        return response()->json([
            'code' => 200,
            'status' => true,
            'message' => 'Warehouse User Deleted successfully'
        ]);
    }
    
}
