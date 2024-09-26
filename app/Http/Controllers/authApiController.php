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
        /**
     * @OA\Info(title="AQS-TRACKING", version="1.0.0")
     *
     * @OA\Get(
     *     path="/api/allhods",
     *     security={{"Bearer": {}}},
     *     summary="fetch all hods",
     *     @OA\Response(
     *         response="200",
     *         description="Successful response"
     *     )
     * )
     */
    public function allhods() {
        $data=Hod::all();
        return response()->json(['hods users'=>$data], 200);
    }

    /**
     * @OA\Post(
     *      path="/api/register",
     *      security={{"Bearer": {}}},
     *      operationId="registerUser",
     *      tags={"Authentication"},
     *      summary="Register a new user",
     *      description="Register a new user and return the inserted data",
     *      @OA\Parameter(
     *          name="hod_name",
     *          description="enter hod name",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="email",
     *          description="User's email",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="contact_number",
     *          description="User's contact number",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="password",
     *          description="enter password",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="password_confirmation",
     *          description="enter repeat password",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="User successfully registered",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="hod_name", type="string", example="kirabo phionah"),
     *              @OA\Property(property="email", type="string", example="kirabo@gmail.com"),
     *              @OA\Property(property="contact_number", type="string", example="0785643266"),
     *              @OA\Property(property="password", type="string", example="123@we"),
     *              @OA\Property(property="password_confirmation", type="string", example="123@we")
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad user input"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource not found"
     *      )
     * )
     */

    public function register(Request $request)
    {
        // Validate the request data (Laravel will handle validation errors)
        $validatedData = $request->validate([
            'hod_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:hods',
            'password' => 'required|string|min:6|confirmed',
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
            'status' => 'success',
            'message' => 'User created successfully',
            'user' => $user,
            'authorization' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ], 201); // Set the status code to 201

        }
    }
    /**
     * @OA\Post(
     *      path="/api/login",
     *      security={{"Bearer": {}}},
     *      operationId="loginUser",
     *      tags={"Authentication"},
     *      summary="Login user",
     *      description="Authenticate a user and return a token",
     *      @OA\Parameter(
     *          name="email",
     *          description="User's email",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string",
     *              format="email"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="password",
     *          description="User's password",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      
     *      @OA\Response(
     *          response=200,
     *          description="User successfully logged in",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="status", type="string", example="success"),
     *              @OA\Property(property="user", type="object",
     *                  
     *               
     *                  @OA\Property(property="email", type="string", example="kirabo@gmail.com"),
     *                  @OA\Property(property="password", type="string", example="0785643266")
     *              ),
     *              @OA\Property(property="authorization", type="object",
     *                  @OA\Property(property="token", type="string", example="your-jwt-token"),
     *                  @OA\Property(property="type", type="string", example="bearer")
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized: Wrong credentials"
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     */

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
    /**
     * @OA\Post(
     *      path="/api/hods/employee/create",
     *      security={{"Bearer": {}}},
     *      operationId="addEmployee",
     *      tags={"Employee Management"},
     *      summary="Add a new employee",
     *      description="Create a new employee and send an email with a default password",
     *      @OA\Parameter(
     *          name="employee_name",
     *          description="Employee's name",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string",
     *              maxLength=255
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="email",
     *          description="Employee's company email (must be unique)",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string",
     *              format="email",
     *              maxLength=255
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="personalemail",
     *          description="Employee's personal email",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string",
     *              format="email",
     *              maxLength=255
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="contact_number",
     *          description="Employee's contact number",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string",
     *              minLength=10
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="position",
     *          description="Employee's position",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string",
     *              maxLength=255
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="hod_fk_id",
     *          description="ID of the HOD creating the employee",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Employee successfully created and email sent",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="Employee created successfully, email sent!"),
     *              @OA\Property(property="employee", type="object",
     *                  
     *                  @OA\Property(property="employee_name", type="string", example="kirabo"),
     *                  @OA\Property(property="email", type="string", example="pk@gmail.com"),
     *                  @OA\Property(property="personalemail", type="string", example="phionahk1@gmail.com"),
     *                  @OA\Property(property="contact_number", type="string", example="0785643266"),
     *                  @OA\Property(property="position", type="string", example="Software Developer"),
     *                  @OA\Property(property="hod_fk_id", type="integer", example=18)
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad user input"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource not found"
     *      )
     * )
     */

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
            'new_password' => 'required|string|min:6', // Confirm new_password with new_password_confirmation
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

