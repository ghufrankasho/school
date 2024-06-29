<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use App\Models\Lesson;
use App\Models\Subject;
class LessonController extends Controller
{
public function index(){
        $lessons=lesson::get();
        if($lessons)
        {  return response()->json(
          [
                'status' => true,
                'message' => 'تم الحصول على البيانات بنجاح', 'data'=> $lessons,
            ],200);}
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
        
        $validatealesson = Validator::make($request->all(), 
        [
           'name' => 'string|required|unique:lessons',
           'description' => 'string|required',
           'activity' => 'string|required',
           'text' => 'string|required',
           'subject_id' => 'integer|exists:subjects,id',
           'image' => 'file|required|mimetypes:image/jpeg,image/png,image/gif,image/svg+xml,image/webp,application/wbmp',

        ]);
        $validatealesson->sometimes('image', 'required|mimetypes:image/vnd.wap.wbmp', function ($input) {
            return $input->file('image') !== null && $input->file('image')->getClientOriginalExtension() === 'wbmp';
        });
    

        if($validatealesson->fails()){
            return response()->json([
                'status' => false,
                'message' => 'خطأ في التحقق',
                'errors' => $validatealesson->errors()
            ], 422);
        }
        if($request->hasFile('image') and $request->file('image')->isValid()){
            $image= $this->store_image($request->file('image')); 
        }
        $lesson = lesson::create(array_merge(
            $validatealesson->validated()
            
            ));
        $lesson->image=$image;  
        $subject=subject::find($request->subject_id);
        $lesson->subject()->associate($subject);    
        $result=$lesson->save();
       if ($result){
           
            return response()->json(
                [
                    'status' => true,
                    'message' => 'تم أضافة البيانات  بنجاح', 
                    'data'=> $lesson,
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
            ['id'=>'required|integer|exists:lessons,id']);
        if($validate->fails()){
        return response()->json([
           'status' => false,
           'message' => 'خطأ في التحقق',
           'errors' => $validate->errors()
        ], 422);}
      
        $lesson=lesson::find($request->id);
     
       
      if($lesson){ 
        if($lesson->image!=null){
            $this->deleteImage($lesson->image);
        } 
         //dissociate lesson from subject
         $subject=$lesson->subject()->first();
         if($subject){
             $lesson->subject()->dissociate($subject);
             }
            $result= $lesson->delete();
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
        return response()->json(
        [ 'status' => false,
        'message' => $e->errors(),
        'data' => null], 422);
    } 
    catch (\Exception $e) {
        return response()->json(['message' => 'An error occurred while deleting the lesson.'], 500);
    }
}
public function update(Request $request){
    try{
       
            
     
        
        $validatelesson = Validator::make($request->all(), [
            'id'=>'required|integer|exists:lessons,id',
            'name' => 'nullable|string',
            'description' => 'nullable|string',
          ]);
       
        if($validatelesson->fails()){
            return response()->json([
                'status' => false,
                'message' => 'خطأ في التحقق',
                'errors' => $validatelesson->errors()
            ], 422);
        }
        $lesson=lesson::find($request->id);
        if($lesson){  
            $lesson->update($validatelesson->validated());
            if($request->hasFile('image') and $request->file('image')->isValid()){
                if($lesson->image !=null){
                    $this->deleteImage($lesson->image);
                }
                $lesson->image = $this->store_image($request->file('image')); 
            }
          
           $result= $lesson->save();
            
           if($result){ return response()->json(
                 [
                    'status' => true,
                    'message' =>   'تم تعديل البيانات  بنجاح', 'data'=> $lesson,
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
public function deleteImage( $url){
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
public function store_image( $file){
    $extension = $file->getClientOriginalExtension();
       
    $imageName = uniqid() . '.' .$extension;
    $file->move(public_path('lessons'), $imageName);

    // Get the full path to the saved image
    $imagePath = asset('lessons/' . $imageName);
            
     
   
   return $imagePath;

}
}