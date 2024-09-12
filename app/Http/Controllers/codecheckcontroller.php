<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Resetpassword;
use Carbon\Carbon; // Import Carbon for date and time handling

class CodeCheckController extends Controller
{
    public function codeChecker(Request $req)
    {
        // Validate the incoming request
        $req->validate([
            'code' => 'required|string|min:6|exists:resetpasswords,code', // Specify the field to check in the table
        ]);

        // Find the reset password entry by code
        $checkCode = Resetpassword::firstWhere('code', $req->code);

        // Check if the code is expired (more than 1 hour old)
        if ($checkCode && Carbon::parse($checkCode->created_at)->addHour()->isPast()) {
            // If expired, delete the code and return a message
            $checkCode->delete();
            return response()->json([
                'message' => 'Your code has expired, please generate another code.'
            ], 422);
        }

        // If the code is still valid, return a success message
        return response()->json([
            'message' => 'Your code is valid.',
        ]);
    }
}
