<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\User;
use App\Models\Account;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller

{

    public function __construct(){
        $this->middleware('auth:api',['except'=>['register','login','logout','profile']]);
    }
    public function register(Request $request)  {

        $validator=Validator::make($request->all(),[
            
            'email'=>'required|string|email|unique:accounts',
            'password'=>'required|string|confirmed|min:6'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors' => $validator->errors()
            ], 400);
        }

        $account=Account::create(array_merge(
            $validator->validated(),
            ['password'=>bcrypt($request->password)]
       ));
        

       return response()->json(
        [ 'status'=>true,
          'message'=>'User reigster Successfully',
          'account'=>$account
         ],201);


        

    }
    public function login(Request $request)  {
        
        $validator=Validator::make($request->all(),[
            'email'=>'required|email',
            'password'=>'required|string|min:6'
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors' => $validator->errors()
            ], 400);
        }
        $account=Account::where('email',$request->email)->first();
        if($account) {

            if(Hash::check($request->password, $account->password)){
                
                $token=auth()->attempt($validator->validated());
                return $this->createNewToken($token);}
            else{
                return response()->json([
                    'status' => false,
                    'message'=>'Password  is not correct',
                    'errors' =>  'Password  is not correct',

                    ], 400);
            }
        }
        else{
            return response()->json([
                'status' => false,
                'message'=>'Email is not correct',
                'errors' =>  'Email is not correct',

                ], 400);
        }

    }
    protected function createNewToken($token) {
        return response()->json([
            'access_token'=>$token,
            'token_type'=>'bearer',
            'expires_in'=>auth()->factory()->getTTL()*1200,
            'message'=>'Logged in successfully',
            'user'=>auth()->user()
        ]);


    }
    public function logout() {
        auth()->logout();
        return response()->json(
            [ 'message'=>'User Logged out Successfully'

             ]);


    }
    public function profile(){
        return response()->json(auth()->user());
    }
    public function refresh(){

        return $this->createNewToken(auth()->refresh());

    }
    public function reset_password(Request $request){
        $validator = Validator::make($request->all(), [
            'old_password' => 'required|string',
            'new_password' => 'required|string|confirmed|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 400);
        }

        $user = auth()->user();

        if (Hash::check($request->old_password, $user->password)) {
            $user->update([
                'password' => bcrypt($request->new_password),
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Password changed successfully',
            ]);
        } 
        else {
            return response()->json([
                'status' => false,
                'errors' => 'Old password is incorrect',
            ], 400);
        }
    }

}