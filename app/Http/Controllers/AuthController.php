<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Account;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail; 
use App\Mail\ForgetPassword;
use App\Models\Teacher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class AuthController extends Controller

{

    public function __construct() {
        $this->middleware('auth:api', ['except' => ['register', 'login', 'logout', 'profile', 'forgot_password', 'reset_password','verify_reset_code']]);
    }
    public function register(Request $request)  {

        $validator=Validator::make($request->all(),[
            
            'email'=>'required|string|email|unique:accounts',
            'password'=>'required|string|confirmed|min:6',
            'name'=>'required|string',
            'type'=>'required|in:1,2,3'// 1=> user  ,2=>teacher , 3=>secretary
        
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
        
       $account=$this->login($request);
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
            'user'=>auth()->user(),
       
        ]);


    }
    public function logout() {
        auth()->logout();
        return response()->json(
            [ 'message'=>'User Logged out Successfully'

             ]);


    }
    public function profile(){
        $account=auth()->user();
        if($account->is_accept)
        {
                if($account->type==1)$result=User::where('account_id',$account->id)->with('homework','notifications')->first();
                if($account->type==2)$result=Teacher::where('account_id',$account->id)->first();
                if(!$result->block)return response()->json($result);
                else  return response()->json(    
                    [  'status' => false,
                    'message' => 'أنت   محظور في هذا التطبيق   الرجاء مراجعة المدير. ',
                    'data' => null],
                    422);
         }
         
        return response()->json(    
            [  'status' => false,
               'message' => 'أنت غير مقبول بعد  الرجاء انتظار موافقة المدير. ',
               'data' => null],
            422);
        
    }
    public function refresh(){

        return $this->createNewToken(auth()->refresh());

    }
    // public function reset_password(Request $request){
    //     $validator = Validator::make($request->all(), [
    //         'old_password' => 'required|string',
    //         'new_password' => 'required|string|confirmed|min:6',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Validation error',
    //             'errors' => $validator->errors(),
    //         ], 400);
    //     }

    //     $user = auth()->user();

    //     if (Hash::check($request->old_password, $user->password)) {
    //         $user->update([
    //             'password' => bcrypt($request->new_password),
    //         ]);

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Password changed successfully',
    //         ]);
    //     } 
    //     else {
    //         return response()->json([
    //             'status' => false,
    //             'errors' => 'Old password is incorrect',
    //         ], 400);
    //     }
    // }
    public function forgot_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:accounts,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 400);
        }

        $code = Str::random(6); // Generate a random 6-character code

        DB::table('password_reset_codes')->updateOrInsert(
            ['email' => $request->email],
            ['code' => $code, 'created_at' => Carbon::now()]
        );

        // Send the code via email using the mailable
        Mail::to($request->email)->send(new ForgetPassword($code));

        return response()->json(['status' => true, 'message' => 'Reset code sent to your email.']);
    }
    public function verify_reset_code(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:accounts,email',
            'code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 400);
        }

        $record = DB::table('password_reset_codes')
            ->where('email', $request->email)
            ->where('code', $request->code)
            ->first();

        if ($record) {
            return response()->json([
                'status' => true,
                'message' => 'Code verified successfully.'
            ]);
        } 
        else {
            return response()->json([
                'status' => false,
                'message' => 'Invalid or expired code.',
            ], 400);
        }
    }

    public function reset_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:accounts,email',
            'code' => 'required|string',
            'password' => 'required|string|confirmed|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 400);
        }

        $record = DB::table('password_reset_codes')
            ->where('email', $request->email)
            ->where('code', $request->code)
            ->first();

        if ($record) {
            $user = Account::where('email', $request->email)->first();
            $user->password = Hash::make($request->password);
            $user->save();

            // Optionally delete the reset code record
            DB::table('password_reset_codes')->where('email', $request->email)->delete();

            return response()->json([
                'status' => true,
                'message' => 'Password reset successfully.'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Invalid or expired code.',
            ], 400);
        }
    }
 
}