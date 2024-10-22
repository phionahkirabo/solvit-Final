<?php

namespace App\Http\Controllers;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

use Illuminate\Http\Request;
// use Illuminate\Http\Request;
use App\Models\Hod;
use App\Notifications\ResetPassword;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use App\Models\Employee;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Mail\EmployeeCreated;
use App\Mail\ForgotPasswordMail;

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
     *      operationId="register hod",
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
     *          description="hod's email",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="contact_number",
     *          description="hod's contact number",
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
        'email' => 'required|string|email|max:255|unique:hods,email|unique:employees,email',
        'password' => 'required|string|min:8|confirmed',
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
        ], 201); // Set the status code to 201 (Created)

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
            'message' => 'An error occurred: ' . $e->getMessage(),
        ], 500); // Properly return the error message
    }
}

    /**
     * @OA\Post(
     *      path="/api/login",
     *      security={{"Bearer": {}}},
     *      operationId="login hod",
     *      tags={"Authentication"},
     *      summary="Login user",
     *      description="Authenticate a user and return a token",
     *      @OA\Parameter(
     *          name="email",
     *          description="hod's email/employee's email",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string",
     *              format="email"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="password",
     *          description="hod's password/employee's password",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      
     *      @OA\Response(
     *          response=200,
     *          description="hod successfully logged in",
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
        'email' => 'string|email|required',
        'password' => 'string',
         // Only required for employee login
    ]);

    // HOD login credentials
    $credentialsHod = $request->only('email', 'password');

    // Employee login credentials (uses password)
    $credentialsEmployee = $request->only('email', 'password');

    // Attempt to login as HOD first
    if ($token = auth('hod')->attempt($credentialsHod)) {
        return response()->json([
            'status' => 'HOD logged in successfully',
            'user' => auth('hod')->user(),
            'authorization' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ], 200); // Successful HOD login, return success response
    }
   // If HOD login fails, attempt to login as Employee
    if ($token = auth('employee')->attempt($credentialsEmployee)) {
        return response()->json([
            'status' => 'Employee logged in successfully',
            'user' => auth('employee')->user(),
            'authorization' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ], 200); // Successful Employee login, return success response
    }

    // If both HOD and Employee login attempts fail, return unauthorized
    return response()->json(['error' => 'Login failed, invalid credentials'], 401);
}

    
    /**
     * @OA\Post(
     *      path="/api/forgot-password",
     *      security={{"Bearer": {}}},
     *      operationId="forgot-password hod",
     *      tags={"Authentication"},
     *      summary="forgot-password user",
     *      description="Authenticate a hod and return sent email",
     *      @OA\Parameter(
     *          name="email",
     *          description="hod's email",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string",
     *              format="email"
     *          )
     *      ),
     *      
     *      
     *      @OA\Response(
     *          response=200,
     *          description="Hod successfully logged in",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="status", type="string", example="success"),
     *              @OA\Property(property="user", type="object",
     *                  
     *               
     *                  @OA\Property(property="email", type="string", example="kirabo@gmail.com"),
     *                
     *              ),
     *              @OA\Property(property="authorization", type="object",
     *                  @OA\Property(property="token", type="string", example="your-jwt-token"),
     *                  @OA\Property(property="type", type="string", example="bearer")
     *              )
     *          )
     *      ),
     *       @OA\Response(
     *          response=400,
     *          description="Bad hod input"
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

    public function forgotPassword(Request $request)
    {
     
        // Validate email field
        $request->validate(['email' => 'required|email']);

        // Find the HOD by email
        $hod = Hod::where('email', $request->email)->first();

        if (!$hod) {
            return response()->json(['message' => 'HOD not found'], 404);
        }

        // Generate and save verification code
        $verificationCode = Str::random(6);
        $hod->verification_code = $verificationCode;
        $hod->save();

        // Send verification code via notification
        try {
            $hod->notify(new ResetPassword($verificationCode));
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to send email'], 500);
        }

        return response()->json(['message' => 'Verification code sent to your email'], 200);
    }
    /**
     * @OA\Post(
     *      path="/api/verify-code",
     *      security={{"Bearer": {}}},
     *      operationId="verify-code Hod",
     *      tags={"Authentication"},
     *      summary="verify-code forget password for hod",
     *      description="verify-code forget password for user and return the code is verfied",
     *    
     *      @OA\Parameter(
     *          name="email",
     *          description="hod's email",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="verification_code",
     *          description="hod's verification_code",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *     
     *      
     *      @OA\Response(
     *          response=200,
     *          description="hod successfully registered",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="hod_name", type="string", example="kirabo phionah"),
     *              @OA\Property(property="email", type="string", example="kirabo@gmail.com"),
     *              @OA\Property(property="verification_code", type="string", example="643266"),
     *              
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad hod input"
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
    public function verifyCode(Request $request)
    {
        // Validate email and code
        $request->validate([
            'email' => 'required|email',
            'verification_code' => 'required|string',
        ]);

        // Find HOD or employee by email
        $hod = Hod::where('email', $request->email)->first();
        $employee = Employee::where('email', $request->email)->first();
        $user = $hod ?: $employee;

        if (!$user || $user->verification_code !== $request->verification_code) {
            return response()->json(['message' => 'Invalid verification code'], 401);
        }

        return response()->json(['message' => 'Verification code verified successfully'], 200);
    }
    /**
     * @OA\Post(
     *      path="/api/reset-password/{code}",
     *      security={{"Bearer": {}}},
     *      operationId="reset-password Hod",
     *      tags={"Authentication"},
     *      summary="reset-password for hod",
     *      description="verify-code forget password for user and return the code is verfied",
     *    
     *      @OA\Parameter(
     *          name="email",
     *          description="hod's email",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="verification_code",
     *          description="hod's verification_code",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="password",
     *          description="hod's new password",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      * @OA\Parameter(
        *     name="password_confirmation",
        *     description="Confirmation of the new password",
        *     required=true,
        *     in="query",
        *     @OA\Schema(
    *         type="string"
    *     )
    * )
     * ,
     *     
     *      
     *      @OA\Response(
     *          response=200,
     *          description="hod successfully resetted password successfuly",
     *          @OA\JsonContent(
     *              type="object",
     *              
     *              @OA\Property(property="email", type="string", example="kirabo@gmail.com"),
     *              @OA\Property(property="verification_code", type="string", example="643266"),
     *              @OA\Property(property="password", type="string", example="kirabo=123P"),
     *              @OA\Property(property="password_confirmation", type="string", example="kirabo=123P"),
     *              
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad hod input"
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
    public function resetPassword(Request $request)
    {
        // Validate input
        $request->validate([
            'email' => 'required|email',
            'verification_code' => 'required|string',
            'password' => 'required|string|confirmed|min:8',
           
            'password_confirmation' => 'required|string|min:8', // Ensure this is validated
        ]);

        // Find HOD or employee by email
        $hod = Hod::where('email', $request->email)->first();
        $employee = Employee::where('email', $request->email)->first();
        $user = $hod ?: $employee;

        if (!$user || $user->verification_code !== $request->verification_code) {
            return response()->json(['message' => 'Invalid verification code'], 401);
        }

        // Reset password and clear verification code
        $user->password = Hash::make($request->password);
        $user->verification_code = null; // Clear the code after use
        $user->save();

        return response()->json(['message' => 'Password reset successfully'], 200);
    }

    /**
     * @OA\Post(
     *      path="/api/logout",
     *      security={{"Bearer": {}}},
     *      operationId="logout",
     *      tags={"Authentication"},
     *      summary="Log out user",
     *      description="Logs out the authenticated HOD or Employee.",
     *    
     *      @OA\Response(
     *          response=200,
     *          description="User logged out successfully",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="HOD has logged out successfully"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="No user is currently logged in",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="No user is currently logged in"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Logout failed",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="status", type="string", example="error"),
     *              @OA\Property(property="message", type="string", example="Logout failed: Internal Server Error"),
     *          )
     *      )
     * )
     */


    public function logout()
    {
        try {
            // Check if the current user is authenticated via the 'hod' guard
            if (Auth::guard('hod')->check()) {
                Auth::guard('hod')->logout();
                return response()->json(['message' => 'HOD has logged out successfully']);
            }

            // Check if the current user is authenticated via the 'employee' guard
            if (Auth::guard('employee')->check()) {
                Auth::guard('employee')->logout();
                return response()->json(['message' => 'Employee has logged out successfully']);
            }

            // If no user is authenticated
            return response()->json(['message' => 'No user is currently logged in'], 400);
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
     *          name="default_password",
     *          description="given default_password for  creating the employee",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
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
     *                  @OA\Property(property="default_password", type="string", example="e43gh45u"),
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
        'email' => 'required|email|unique:employees,email|max:255|unique:hods,email',
        'personalemail' => 'required|email|max:255',
        'contact_number' => 'required|numeric|min:10',
        'position' => 'required|string|max:255',
        'default_password' => 'required|string',
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
        'default_password' =>$defaultPassword, // Hash the default password
    ]);

    // Send email to employee with default password and link to set a new password
    $verificationLink = route('employee.verify.default.password', ['email' => $employee->personalemail]);

    Mail::to($employee->personalemail)->send(new EmployeeCreated($employee, $defaultPassword, $verificationLink));

    return response()->json([
        'message' => 'Employee created successfully, email sent!',
        'employee' => $employee
    ], 201);
}

    /**
     * @OA\Post(
     *      path="/api/employees-verify-default-password",
     *      security={{"Bearer": {}}},
     *      operationId="verifyDefaultPassword",
     *      tags={"Employee Authentication"},
     *      summary="Verify Employee's default password",
     *      description="Verify the default password for an employee during the initial login process",
     *      @OA\Parameter(
     *          name="email",
     *          description="Employee's company email",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string",
     *              format="email"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="default_password",
     *          description="The default password sent to the employee",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Default password verified successfully",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="email", type="string", example="pk@gmail.com"),
     *              @OA\Property(property="default_password", type="string", example="235jjlkh="),
     *          )
     *      ),
     * 
     *      
     *      @OA\Response(
     *          response=400,
     *          description="Bad user input"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Employee not found"
     *      )
     * )
     */

 public function verifyDefaultPassword(Request $request)
    {
        Log::info('Verifying default password', ['request' => $request->all()]);

        $request->validate([
            'email' => 'required|email',
            'default_password' => 'required|string',
        ]);

        $employee = Employee::where('email', $request->email)->first();
        Log::info('Employee retrieved', ['employee' => $employee]);

        if ($employee->default_password==$request->default_password && $employee->email==$request->email) {
            Log::warning('Invalid password attempt', ['email' => $request->email]);
            return response()->json(['message' => 'default password is valid'], 200);
        }
        else{
          return response()->json(['message' => 'default password is invalid'], 401);
        }
    }
     
    /**
     * @OA\Post(
     *      path="/api/employee-reset-password/{default_password}",
     *      security={{"Bearer": {}}},
     *      operationId="employeeResetPassword",
     *      tags={"Employee Authentication"},
     *      summary="Reset Employee's password",
     *      description="Allows an employee to reset their password by providing their email, default password, and new password",
     *      @OA\Parameter(
     *          name="email",
     *          description="Employee's company email",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string",
     *              format="email"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="default_password",
     *          description="Employee's default password",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="password",
     *          description="The new password the employee wants to set",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string",
     *              minLength=6
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Password updated successfully",
     *          @OA\JsonContent(
     *              type="object",
     *             @OA\Property(property="email", type="string", example="pk@gmail.com"),
     *              @OA\Property(property="default_password", type="string", example="235jjlkh="),
     *              @OA\Property(property="password", type="string", example="235jjlkh=1"),
     *          )
     *      ),
     *      
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

    public function employeeResetpassword(Request $request)
    {
        \Log::info('Reset password request received:', $request->all());

        // Validate the input, including the email, default password, and confirmed new password
        $validated = $request->validate([
            'email' => 'required|string|email', // Validate email format
            'default_password' => 'required|string', // Ensure default_password is provided
            'password' => 'required|string|min:8', // Confirm password with password_confirmation
        ]);

        // Log validation success
        \Log::info('Validation passed:', $validated);

        // Retrieve the employee by their email
        $employee = Employee::where('email', $request->email)->first();

        // Check if employee is found
        if (!$employee) {
            \Log::info('Employee not found with email: ' . $request->email);
            return response()->json(['error' => 'Employee not found'], 404);
        }

      
        // Check if the employee exists and the default_password matches
        if ($employee->default_password==$request->default_password && $employee->email==$request->email) {
            Employee::where('email', $request->email)->where('default_password',$request->default_password)
            ->update([
                'password' =>bcrypt($request->password),
            ]);
            return response()->json(['success' => 'Password updated successfully']);
        } else {
            return response()->json(['error' => 'Incorrect default password or email'], 401);
        }

    }

}

