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
use App\Models\Employee;


use Illuminate\Support\Facades\Mail;
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

    return response()->json([ 
        'user'=>Auth::user(),
        'token' => $token]);

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
    public function addEmployee(Request $request)
    {
        $request->validate([
            'employee_name' => 'required|string',
            'email' => 'required|email|unique:employees,email',
            'personalemail' => 'required|email',
            'contact_number' => 'required',
            'position' => 'required',
        ]);

        // Generate default password
        $defaultPassword = str_random(8);

        // Create employee
        $employee = Employee::create([
            'employee_name' => $request->employee_name,
            'email' => $request->email,
            'personalemail' => $request->personalemail,
            'contact_number' => $request->contact_number,
            'position' => $request->position,
            'hod_fk_id' => auth()->id(), // Ensure the HOD ID is stored
            'default_password' => Hash::make($defaultPassword), // Hash the default password
        ]);

        // Send email to employee with default password and link to set new password
        Mail::to($request->personalemail)->send(new \App\Mail\EmployeeCreated($employee, $defaultPassword));

        return response()->json([
            'message' => 'Employee created successfully, email sent!',
            'employee' => $employee
        ], 201);
    }
     public function employeeLogin(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('employee')->attempt($credentials)) {
            $token = Auth::guard('employee')->user()->createToken('EmployeeToken')->plainTextToken;
            return response()->json([
                'token' => $token,
                'message' => 'Login successful',
            ], 200);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }
}
