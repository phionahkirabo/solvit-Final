<?php

namespace App\Http\Controllers\reset_password;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Resetpassword;
use App\Models\Hod; // or Employee, depending on your structure

use Illuminate\Support\Facades\Mail;
use App\Mail\resetpasswordmail;
use Illuminate\Mail\Mailable;

class forgetpasswordcontroller extends Controller
{
    function forgetpassword(Request $req){
         $data = $req ->validate
         ([
            'email'=> 'required|email|exists:hods',
         ]);
         Resetpassword::where('email',$req->email)->delete();
         $data['code']= mt_rand(100000,999999);
         $codedata = Resetpassword::create($data);
         Mail::to($req->email)->send(new resetpasswordmail($codedata->code));
         return response()->json([
            'message'=>trans('check your email ,we sent you a code for reset your password')

         ],200
        );

    }


}
