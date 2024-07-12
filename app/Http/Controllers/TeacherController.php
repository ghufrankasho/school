<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\Teacher;
use App\Models\Account;
use App\Models\Subject;
class TeacherController extends Controller
{
public function index(){
        $teachers=teacher::get();
        if($teachers){
            return response()->json(
                [
                      'status' => true,
                      'message' => 'تم الحصول على البيانات بنجاح', 
                      'data'=> $teachers,
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
public function block(Request $request){
        
        
    $validate = Validator::make( $request->all(),
        ['id'=>'required|integer|exists:teachers,id']);
    if($validate->fails()){
    return response()->json([
       'status' => false,
       'message' => 'خطأ في التحقق',
       'errors' => $validate->errors()
    ], 422);}
  
    $teacher=teacher::find($request->id);
    if($teacher){
        $teacher->block=true;
        $result=$teacher->save();
        if($result){
        return response()->json(
            [
                  'status' => true,
                  'message' => 'تم حظر المستخدم بنجاح', 
                  'data'=> $teacher,
              ],200);
            }
        }
         else{
              return response()->json(
                      [  'status' => false,
                      'message' => 'حدث خطأ أثناء  حظر المستخدم',
                      'data' => null],
                      422);
              }
}
public function store(Request $request){
    
    try{
        
        $validateateacher = Validator::make($request->all(), 
        [
            'name' => 'string|required',
            'email'=>'required|string|email|unique:teachers',
            'specilty' => 'nullable|string',
            'phone' => 'string|required',
            'description' => 'string|required',
            'account_id' => 'integer|exists:accounts,id|unique:teachers',
            'subject_id' => 'integer|exists:subjects,id',
            'image' => 'string|required'
         ]);
       
    

        if($validateateacher->fails()){
            return response()->json([
                'status' => false,
                'message' => 'خطأ في التحقق',
                'errors' => $validateateacher->errors()
            ], 422);
        }
        $image= $this->upLoadImage($request->image); 
          
        $teacher = teacher::create(array_merge(
            $validateateacher->validated()
            
            ));
        $teacher->image=$image;
        
        $account=Account::find($request->account_id);
        
        if($account)$teacher->account()->associate($account); 
         
        else{   return response()->json( [  'status' => false,
            'message' => 'حدث خطأ أثناء ربط الاستاذ مع حسابه ',
            'data' => null],
               422);
            } 
        $subject=subject::find($request->subject_id);
        if($subject)$teacher->subject()->associate($subject);  
       
        $result=$teacher->save();
       if ($result){
           
            return response()->json(
                [
                    'status' => true,
                    'message' => 'تم أضافة البيانات  بنجاح', 
                    'data'=> $teacher,
                ]
             , 201);
            }
       else{
            return response()->json( [  'status' => false,
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
            ['id'=>'required|integer|exists:teachers,id']);
        if($validate->fails()){
        return response()->json([
           'status' => false,
           'message' => 'خطأ في التحقق',
           'errors' => $validate->errors()
        ], 422);}
      
        $teacher=teacher::find($request->id);
     
       
      if($teacher){ 
            if($teacher->image!=null){
                $this->deleteImage($teacher->image);
            } 
             //delete account of teacher     
            $account=$teacher->account()->first();
             
            if($account){
                $result=$teacher->account()->dissociate($account);
                $teacher->save();
                $account->delete();
            } 
               //dissociate teacher from subject     
               $subject=$teacher->subject()->first();
             
               if($subject){
                   $result=$teacher->subject()->dissociate($subject);
                   $teacher->save();
                   
               }   
            $result= $teacher->delete();
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
        return response()->json(['message' => 'An error occurred while deleting the teacher.'], 500);
    }
}
public function update(Request $request){
    try{
        
         
            
      
        
        $validateteacher = Validator::make($request->all(), [
            'name' => 'nullable|string',
            'id'=>'required|integer|exists:teachers,id',
            'email'=>'nullable|string|email|unique:teachers',
            'specilty' => 'nullable|string',
            'phone' => 'nullable|string',
            'description' => 'nullable|string',
            'image' => 'nullable|string'
         ]);
       
        if($validateteacher->fails()){
            return response()->json([
                'status' => false,
                'message' => 'خطأ في التحقق',
                'errors' => $validateteacher->errors()
            ], 422);
        }
        $teacher=teacher::find($request->id);
        if($teacher){  
            $teacher->update($validateteacher->validated());
            if($request->image != null){
                if($teacher->image != null){
                    $this->deleteImage($teacher->image);
                }
                $teacher->image=$this->upLoadImage($request->image); 

            } 

            
            
            $result= $teacher->save();
            
            if($result){
                return response()->json(
                    [
                       'status' => true,
                       'message' =>   'تم تعديل البيانات  بنجاح',
                       'data'=> $teacher,
                   ], 200);}
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
public function deleteImage($url){
 
    // Get the full path to the image
   
    $fullPath =$url;
     
    $parts = explode('/',$fullPath,5);
   
    $fullPath = public_path($parts[3].'/'.$parts[4]);

    // Check if the image file exists and delete it
    if (file_exists($fullPath)) {
        unlink($fullPath);
        
        return true;
     }
     else return false;
}
public function upLoadImage($photo){
    $file = base64_decode($photo);
    $png_url = uniqid().".png";
    $path='teachers/'.$png_url;
    $success = file_put_contents($path, $file);
    $url  = asset('teachers/'. $png_url);
    return    $url;
      
    
}
}