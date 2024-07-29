<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;

class SecretaryController extends Controller
{
    public  function  add_users_attendance(Request $request){
      
        try{
            
           
            $validateuser = Validator::make($request->all(), [
                'user_ids' => 'required|array',
                'user_ids.*' => 'exists:users,id',
                'attendance_day' => 'required|date|string',
                 ]);
            
            
             
            
            if($validateuser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'خطأ في التحقق',
                    'errors' => $validateuser->errors()
                ], 422);
            }
            foreach($request->user_ids as $id){
                $user=User::find($id);
                $attendance = Attendance::create(array_merge(
                    $validateuser->validated()
                    
                    ));
                $attendance->user()->associate($user);
                $attendance->save();
                
                 
            }
           
            
            
                return response()->json(
                    [
                       'status' => true,
                       'message' =>   'تم أضافة البيانات  بنجاح',
                       'data'=> "",
                   ], 200);
        
            

        }
        catch (\Throwable $th) {
            return response()->json([
            'status' => false,
            'message' => $th->getMessage()
            ], 500);
        }
    }
}