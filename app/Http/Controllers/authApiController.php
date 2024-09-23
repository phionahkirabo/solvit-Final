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
use Illuminate\Support\Facades\DB;
use App\Mail\EmployeeCreated;
use Illuminate\Support\Str;
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

    public function login(Request $request)
        {
            // Validate the request
            $validatedData = $request->validate([
                'email' => 'string|email',
                'password' => 'string|min:8',
                'default_password' => 'string|min:8',
            ]);

            // HOD login credentials
            $credentialsHod = $request->only('email', 'password');

            // Employee login credentials (uses default_password)
            $credentialsEmployee = $request->only('email', 'default_password');

            // Attempt to login as HOD
            if ($token = auth('hod')->attempt($credentialsHod)) {
                return response()->json([
                    'status' => 'success',
                    'user' => auth('hod')->user(),
                    'authorization' => [
                        'token' => $token,
                        'type' => 'bearer',
                    ]
                ]);
            }

            // Attempt to login as Employee
            if ($token = auth('employee')->attempt($credentialsEmployee)) {
                return response()->json([
                    'status' => 'success',
                    'user' => auth('employee')->user(),
                    'authorization' => [
                        'token' => $token,
                        'type' => 'bearer',
                    ]
                ]);
            }

            // If both login attempts fail, return unauthorized
            return response()->json(['error' => 'Wrong credentials'], 401);
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
        'employee_name' => 'required|string|max:255',
        'email' => 'required|email|unique:employees,email|max:255',
        'personalemail' => 'required|email|max:255',
        'contact_number' => 'required|numeric|min:10',
        'position' => 'required|string|max:255',
        'hod_fk_id' => 'required|exists:hods,id', 
    ]);

    // Generate default password
    $defaultPassword = Str::random(8);

    // Create employee
    $employee = Employee::create([
        'employee_name' => $request->employee_name,
        'email' => $request->email,
        'personalemail' => $request->personalemail,
        'contact_number' => $request->contact_number,
        'position' => $request->position,
        'hod_fk_id' => auth('hod')->id(), // Use the 'hod' guard to get the authenticated HOD ID
        'default_password' => Hash::make($defaultPassword), // Hash the default password
    ]);

    // Send email to employee with default password and link to set a new password
    $verificationLink = route('employee.verify.default.password', ['email' => $employee->personalemail]);

    Mail::to($employee->personalemail)->send(new EmployeeCreated($employee, $defaultPassword, $verificationLink));

    return response()->json([
        'message' => 'Employee created successfully, email sent!',
        'employee' => $employee
    ], 201);
}

 public function verifyDefaultPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'default_password' => 'required|string',
        ]);

        $employee = Employee::where('email', $request->email)->first();

        if (!$employee || !Hash::check($request->default_password, $employee->default_password)) {
            return response()->json(['message' => 'Invalid default password'], 401);
        }

        return response()->json(['message' => 'Default password verified successfully'], 200);
    }

 public function employeeResetpassword(Request $request)
    {
        \Log::info($request->all());
        // Validate the input, including the email, default password, and confirmed new password
        $request->validate([
            'email' => 'required|string|email', // Validate email format
            'default_password' => 'required|string', // Ensure default_password is provided
            'new_password' => 'required|string|confirmed|min:6', // Confirm new_password with new_password_confirmation
        ]);

        // Retrieve the employee by their email
        $employee = Employee::where('email', $request->email)->first();

        // Check if the employee exists and the default_password matches
        if ($employee && Hash::check($request->default_password, $employee->default_password)) {
            // Update the employee's password with the new hashed password
            $employee->update([
                'default_password' => Hash::make($request->new_password),
            ]);

            return response()->json(['success' => 'Password updated successfully']);
        } else {
            // Return error if the email or password is incorrect
            return response()->json(['error' => 'Incorrect email or default password'], 404); 
        }
    }


}

