<?php

namespace App\Http\Controllers\reset_password;

use App\Http\Controllers\Controller;
use App\Models\Resetpassword;
use App\Models\Hod;
use Illuminate\Http\Request;

class resetcontroller extends Controller
{
 public function resetpassword(Request $request){
    $request ->validate([
        'code'=>'required|exists:resetpasswords',
        'email'=>'required|email|exists:resetpasswords',
        'password'=>'required|string|min:8|max:32',
        
    ]);
    $code = $request->code;
    $email = $request->email;
    $password = $request->password;
    $forgetModel = Resetpassword::firstWhere('email',$email)->firstWhere('code',$code);
     if($forgetModel->created_at > now()->addHour()){
        $forgetModel->delete();
        return response()->json([
            'message'=> 'your code is expired, please generate another code'
        ],422);
     }
     $userModel=Hod::firstWhere('email',$forgetModel->email);
     $userModel->update([
        'password'=>bcrypt($password)
     ]);
     $forgetModel->delete();
    return response()->json([
       'message'=>'password is updated succesfuly'

    ],200);
 }
}
