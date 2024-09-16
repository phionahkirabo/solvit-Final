<?php

namespace App\Http\Controllers;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class employeeController extends Controller
{
   public function verifyDefaultPassword(Request $request, $id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json(['message' => 'Invalid employee.'], 404);
        }

        $request->validate([
            'default_password' => 'required|string',
            'password' => 'required|string|confirmed',
        ]);

        // Check if default password matches
        if (!Hash::check($request->default_password, $employee->default_password)) {
            return response()->json(['message' => 'Default password is incorrect.'], 422);
        }

        // Update employee password
        $employee->password = Hash::make($request->password);
        $employee->default_password = null; // Clear default password field
        $employee->save();

        return response()->json(['message' => 'Password successfully updated.'], 200);
    }

}
