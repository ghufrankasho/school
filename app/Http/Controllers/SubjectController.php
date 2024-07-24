<?php

namespace App\Http\Controllers;
use App\Models\Subject;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
class SubjectController extends Controller
{  
public function index(){
    
       $subjects=subject::get();
       if($subjects)
        {  return response()->json(
            [
                    'status' => true,
                    'message' => 'تم الحصول على البيانات بنجاح', 'data'=> $subjects,
                ],200);
        }
       else{
            return response()->json(
                    [  'status' => false,
                    'message' => 'حدث خطأ أثناء جلب البيانات',
                    'data' => null],
                    422);
            }
}
public function store(Request $request){
    
    try{
        
          
        $validateasubject = Validator::make($request->all(), 
        [
           'name' => 'string|required|unique:subjects',
           'description' => 'string|required',
           
        ]);
    

        if($validateasubject->fails()){
            return response()->json([
                'status' => false,
                'message' => 'خطأ في التحقق',
                'errors' => $validateasubject->errors()
            ], 422);
        }

        $subject = subject::create(array_merge(
            $validateasubject->validated()
            
            ));
            
        $result=$subject->save();
       if ($result){
        return response()->json(
            [
                'status' => true,
                'message' => 'تم أضافة البيانات  بنجاح', 
                'data'=> $subject,
            ]
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
public function destroy(Request $request){
    try {  
         
       
        $validate = Validator::make( $request->all(),
            ['id'=>'required|integer|exists:subjects,id']);
        if($validate->fails()){
        return response()->json([
           'status' => false,
           'message' => 'خطأ في التحقق',
           'errors' => $validate->errors()
        ], 422);}
      
        $subject=subject::find($request->id);
     
       
      if($subject){ 
            
        $result= $subject->delete();
        if($result){ 
            return response()->json(
                [
                     'status' => true,
                     'message' =>' تم حذف البيانات بنجاح', 
                     'data'=> $result,
                 ], 200);
              
         }
         }
 
         return response()->json(    
             [  'status' => false,
             'message' => 'حدث خطأ أثناء حذف البيانات',
             'data' => null],
             422);
    }
    catch (ValidationException $e) {
        return response()->json(['errors' => $e->errors()], 422);
    } 
    catch (\Exception $e) {
        return response()->json(['message' => 'An error occurred while deleting the subject.'], 500);
    }
}
public function update(Request $request){
    try{
      
            
       
        
        $validatesubject = Validator::make($request->all(), [
            'id'=>'required|integer|exists:subjects,id',
            'name' => 'nullable|string',
            'description' => 'nullable|string',
          ]);
       
        if($validatesubject->fails()){
            return response()->json([
                'status' => false,
                'message' => 'خطأ في التحقق',
                'errors' => $validatesubject->errors()
            ], 422);
        }
        $subject=subject::find($request->id);
        if($subject){  
            $subject->update($validatesubject->validated());
            $result=$subject->save();
           if(  $result) {
            return response()->json(
                [
                   'status' => true,
                   'message' =>   'تم تعديل البيانات  بنجاح', 'data'=> $subject,
               ], 200);
           }
            
           
        }
        
        return response()->json([
            'status' => false,
            'message' =>  'فشلت عملية التعديل ',
            'data'=> null
            ], 422);
        

    }
    catch (\Throwable $th) {
        return response()->json([
        'status' => false,
        'message' => $th->getMessage()
        ], 500);
    }
  
    
}
public function show(Request $request){
    try {  
         
       
        $validate = Validator::make( $request->all(),
            ['id'=>'required|integer|exists:subjects,id']);
        if($validate->fails()){
        return response()->json([
           'status' => false,
           'message' => 'خطأ في التحقق',
           'errors' => $validate->errors()
        ], 422);}
      
        $subject=subject::with('lessons')->find($request->id);
     
       
      if($subject){ 
         
            return response()->json(
                [
                     'status' => true,
                     'message' =>' تم الحصول على البيانات بنجاح', 
                     'data'=> $subject,
                 ], 200);
              
         
         }
 
         return response()->json(    
             [  'status' => false,
             'message' => 'حدث خطأ أثناء الحصول على  البيانات',
             'data' => null],
             422);
    }
    catch (ValidationException $e) {
        return response()->json(['errors' => $e->errors()], 422);
    } 
    catch (\Exception $e) {
        return response()->json(['message' => 'An error occurred while deleting the subject.'], 500);
    }
    
}

 
}