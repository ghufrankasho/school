<?php

namespace App\Http\Controllers;

use App\Models\Homework;
use App\Models\Message;
use App\Models\teacher;
use App\Models\User;
use App\Models\TypeSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class HomeworkController extends Controller
{
public function index(){
        $homeworks=homework::get();
        return response()->json(
            $homeworks
            ,200);
}
public function store(Request $request){
    
    try{
        
        $validateahomework = Validator::make($request->all(), 
        [
           'text' => 'string|required',
           'end_date' => 'date|required',
           'type_section_id' => 'integer|required|exists:type_sections,id',
           
           'teacher_id' => 'integer|required|exists:teachers,id',
           
        ]);
    

        if($validateahomework->fails()){
            return response()->json([
                'status' => false,
                'message' => 'خطأ في التحقق',
                'errors' => $validateahomework->errors()
            ], 422);
        }

        $homework = homework::create(array_merge(
            $validateahomework->validated()
            
            ));
        $teacher=teacher::find($request->teacher_id);
        $homework->teacher()->associate($teacher); 
        $TypeSection=TypeSection::find($request->type_section_id);
        $homework->type_section()->associate($TypeSection);    
        $result=$homework->save();
       if ($result){
           
            return response()->json(
           ['status'=>true, 
            'message'=> 'تم أضافة البيانات  بنجاح',
            'data'=>$homework]
             , 201);
            }
       else{
        return response()->json(
            [  'status' => false,
            'message' => 'حدث خطأ أثناء أضافة البيانات',
            'data' => null],
            422);
            }

    }
    catch (\Throwable $th) {
        return response()->json([
            'status' => false,
            'message' =>  $th->getMessage(),
            // "حدث خطأ أثناء أضافة البيانات"
        ], 500);
    }
   
    
}
public function destroy($id){
    try {  
         
        $input = [ 'id' =>$id ];
        $validate = Validator::make( $input,
            ['id'=>'required|integer|exists:homeworks,id']);
        if($validate->fails()){
        return response()->json([
           'status' => false,
           'message' => 'خطأ في التحقق',
           'errors' => $validate->errors()
        ], 422);}
      
        $homework=homework::find($id);
     
       
      if($homework){ 
        
        
        
        //dissociate this homework from teacher
        $teacher= $homework->teacher()->first();
        if($teacher) $homework->teacher()->dissociate( $teacher);
       //dissociate this homework from TypeSection
       $type_section= $homework->type_section()->first();
       if($teacher) $homework->type_section()->dissociate( $type_section);
      
        $result= $homework->delete();
        if($result){ 
            return response()->json(
            ' تم حذف البيانات بنجاح'
            , 200);
        }
        }

        return response()->json(null, 422);
    }
    catch (ValidationException $e) {
        return response()->json(['errors' => $e->errors()], 422);
    } 
    catch (\Exception $e) {
        return response()->json(['message' => 'An error occurred while deleting the homework.'], 500);
    }
}
public function add_hw_to_users(Request $request){
    try {  
         
       
        $validate = Validator::make( $request->all(),
            ['homework_id'=>'required|integer|exists:homework,id',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',]);
            
        if($validate->fails()){
        return response()->json([
           'status' => false,
           'message' => 'خطأ في التحقق',
           'errors' => $validate->errors()
        ], 422);}
      
        $homework=homework::find($request->homework_id);
     
    //    return  $homework;
      if($homework){ 
            $ids= $request->user_ids;
            
            $userIdsArray = explode(',', $ids[0]);
            
            // Convert the array elements to integers (if needed)
             $userIdsArray = array_map('intval', $userIdsArray);
             foreach($userIdsArray as $id){
                $user=User::find($id);
                 
                  if($user)$homework->users()->attach($user); 
                  
             }
             $result= $homework->users()->get();
             
       
     
        if($result){ 
           
            return response()->json(
                [
                    'status' => true,
                    'message' => 'تم أضافة البيانات  بنجاح', 
                    'data'=> $homework,
                ]
             , 201);
        }
        }
        return response()->json(
            [  'status' => false,
            'message' => 'حدث خطأ أثناء أضافة البيانات',
            'data' => null],
            422);
      
    }
    catch (ValidationException $e) {
        return response()->json(['errors' => $e->errors()], 422);
    } 
    catch (\Exception $e) {
        return response()->json(['message' => 'An error occurred while adding the homework.'], 500);
    }
    
}
}