<?php

namespace App\Http\Controllers;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;
// use Illuminate\Http\Request;
use App\Models\Hod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\QueryException;
use Exception;

class authApiController extends Controller
{
    public function register(Request $request)
    {
        // Validate the request data (Laravel will handle validation errors)
        $validatedData = $request->validate([
            'hod_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:hods',
            'password' => 'required|string|min:6',
            'contact_number' => 'required|string|min:10',
        ]);

        try {
            // Create a new user with the validated data
            $user = Hod::create([
                'hod_name' => $request->hod_name,
                'email' => $request->email,
                'contact_number' => $request->contact_number,
                'password' => Hash::make($request->password),
            ]);

            // Generate a JWT token for the authenticated user
            $token = Auth::guard('api')->login($user);

            // Return a JSON response with success status and token
            return response()->json([
                'status' => 'success',
                'message' => 'User created successfully',
                'user' => $user,
                'authorization' => [
                    'token' => $token,
                    'type' => 'bearer',
                ]
            ]);

        } catch (QueryException $e) {
            // Handle database query errors (e.g., unique constraint failure)
            return response()->json([
                'status' => 'error',
                'message' => 'Error creating user: ' . $e->getMessage(),
            ], 500);
        } catch (Exception $e) {
            // Handle other general exceptions
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function login(Request $request) {
    $credentials = $request->only('email', 'password');

    if (!$token = JWTAuth::attempt($credentials)) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    return response()->json(['token' => $token]);
}
    public function logout()
    {
        try {
            Auth::guard('api')->logout();
            return response()->json(['message' => 'You have logged out successfully']);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Logout failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
