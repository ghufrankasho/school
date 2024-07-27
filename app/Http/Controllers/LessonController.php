<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\Type;
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
           'video' => 'nullable|string',
           'file' => 'nullable|string',
           'subject_id' => 'integer|exists:subjects,id',
           'type_id' => 'integer|exists:types,id',
           'image' => 'string|required'

        ]);
       
    

        if($validatealesson->fails()){
            return response()->json([
                'status' => false,
                'message' => 'خطأ في التحقق',
                'errors' => $validatealesson->errors()
            ], 422);
        }
         
         
        
        $lesson = lesson::create(array_merge(
            $validatealesson->validated()
            
            ));
        $lesson->image=$this->upLoadImage($request->image);  
        if($request->file !==null){
            $lesson->file=$this->upLoadfile($request->file);  
        }
        if($request->video !==null){
            $lesson->video=$this->upLoadvideo($request->video);  
        }
        $subject=subject::find($request->subject_id);
        if( $subject)$lesson->subject()->associate($subject);  
        $type=type::find($request->type_id);
        if( $type)$lesson->type()->associate($type);   
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
        if($lesson->file!=null){
            $this->deleteImage($lesson->file);
        } 
        if($lesson->video!=null){
            $this->deleteImage($lesson->video);
        } 
         //dissociate lesson from subject
         $subject=$lesson->subject()->first();
        if($subject){
             $lesson->subject()->dissociate($subject);
             
             }
         //dissociate lesson from type    
        $type=$lesson->type()->first();
        if($type){
            $lesson->type()->dissociate($type);
                 
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
           
            'name' => 'nullable|string|unique:lessons',
            'description' => 'nullable|string',
            'video' => 'nullable|string',
            'file' => 'nullable|string',
            'subject_id' => 'nullable|integer|exists:subjects,id',
            'type_id' => 'nullable|integer|exists:types,id',
            'image' => 'nullable|string'
           
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
            if($request->image!=null){
                if($lesson->image !=null){
                    $this->deleteImage($lesson->image);
                }
                $lesson->image = $this->upLoadImage($request->image); 
            }
            if($request->subject_id != null){
                 //dissociate lesson from subject
                $subject=$lesson->subject()->first();
                if($subject){ $lesson->subject()->dissociate($subject); }
                //associate lesson with new  subject
                $subject=subject::find($request->subject_id);
                if( $subject)$lesson->subject()->associate($subject); 
                
            }
            if($request->type_id !=null){
                 //dissociate lesson from type    
                $type=$lesson->type()->first();
                if($type){ $lesson->type()->dissociate($type);}
                //associate lesson with new  type
                $type=type::find($request->type_id);
                if( $type)$lesson->type()->associate($type);  
            }
          
           $result= $lesson->save();
            
           if($result){
             return response()->json(
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
    $path='lessons/'.$png_url;
    $success = file_put_contents($path, $file);
    $url  = asset('lessons/'. $png_url);
    return    $url;
      
    
}
public function upLoadfile($file){
    $file = base64_decode($file);
    $png_url = uniqid().".pdf";
    $path='lessons/'.$png_url;
    $success = file_put_contents($path, $file);
    $url  = asset('lessons/'. $png_url);
    return    $url;
      
    
}
public function upLoadvideo($file){
    $file = base64_decode($file);
    $png_url = uniqid().".pdf";
    $path='lessons/'.$png_url;
    $success = file_put_contents($path, $file);
    $url  = asset('lessons/'. $png_url);
    return    $url;
      
    
}
}