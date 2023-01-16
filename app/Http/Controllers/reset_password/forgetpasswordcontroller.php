<?php

namespace App\Http\Controllers\reset_password;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\resetpassword;
use Illuminate\Support\Facades\Mail;
use App\Mail\resetpasswordmail;
use Illuminate\Mail\Mailable;

class forgetpasswordcontroller extends Controller
{
    function forgetpassword(Request $req){
         $data = $req ->validate
         ([
            'email'=> 'required|email|exists:users',
         ]);
         resetpassword::where('email',$req->email)->delete();
         $data['code']= mt_rand(100000,999999);
         $codedata = resetpassword::create($data);
         Mail::to($req->email)->send(new resetpasswordmail($codedata->code));
         return response()->json([
            'message'=>trans('check your email ,we sent you a code for reset your password')

         ],200
        );

    }


}
