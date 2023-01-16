<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\facades\Auth;
use Illuminate\Support\Facades\Hash;
use Session;


class authApiController extends Controller
{
    function registerUser(Request $req){
    //     $user = userTB::create([
    //         'firstName' => $req->fname,
    //         'lastName' =>$req->lname,
    //         'email' => $req->email,
    //         'department' => $req->dep,
    //         'password' => bcrypt($req->pswd)
    //     ]);
        
    //     // $user->save();
    //     $token = Auth::login($user);
    //     return response()->json([
    //        'message' => 'new user is created',
    //        'user data'=> $user,
    //     'user token' => $token,
    //     ]);
    // }
    // function test(){
    //     return ["name"=>"kirabo"];
    // }
    $user = user::create([
        'full_name' => $req->fname,
        'userName' =>$req->userName,
        'email' => $req->email,
        'registered_date' => $req->regdate,
        'password' => Hash::make($req->pswd)
    ]);
    
    // $user->save();
    $token = Auth::login($user);
    return response()->json([
       'message' => 'new user is created',
       'user data'=> $user,
    'user token' => $token,
    ]);
}
// function test(){
//     return ["name"=>"kirabo"];
// }
function login(Request $request){
    $this->validate($request,[
        
        'email'=>'required|email',
        'pswd'=>'required',
    ]);

    $user   =   User::where('email',$request->email)->first();

    if (!$user) {
        return response()->json(['message'=>"User Not Found"],200);
    }
    // return $user;
    // return Hash::check($request->password,$user->pswd);
    if($user && Hash::check($request->pswd,$user->password)){
        // $user->createToken($request->email)->plainTextToken;
        $token = $user->createToken($request->email)->plainTextToken;
        return response()->json([
            'user'=>$user,
            'token'=>$token,
        ]);
    }
    else{
        return response()->json(['message'=>'invalid credentials'],200);
    }

}
public function logout(){
    session::flush();
    Auth::logout();
    return response()->json(['message'=>'you logged out succesful']);
}

    
}
