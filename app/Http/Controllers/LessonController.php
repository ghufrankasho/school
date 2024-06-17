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
        return response()->json(
            $lessons
            ,200);
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

        $lesson = lesson::create(array_merge(
            $validatealesson->validated()
            
            ));
        $subject=subject::find($request->subject_id);
        $lesson->subject()->associate($subject);    
        $result=$lesson->save();
       if ($result){
           
            return response()->json(
             'تم أضافة البيانات  بنجاح'
             , 201);
            }
       else{
            return response()->json('حدث خطأ أثناء أضافة البيانات', 422);
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
            ['id'=>'required|integer|exists:lessons,id']);
        if($validate->fails()){
        return response()->json([
           'status' => false,
           'message' => 'خطأ في التحقق',
           'errors' => $validate->errors()
        ], 422);}
      
        $lesson=lesson::find($id);
     
       
      if($lesson){ 
        if($lesson->image!=null){
            $this->deleteImage($lesson->image);
        } 
            $result= $lesson->delete();
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
        return response()->json(['message' => 'An error occurred while deleting the lesson.'], 500);
    }
}
public function update(Request $request, $id){
    try{
        $input = [ 'id' =>$id ];
        $validate = Validator::make( $input,
        ['id'=>'required|integer|exists:lessons,id']);
        if($validate->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'خطأ في التحقق',
                    'errors' => $validate->errors()
                ], 422);
            }
            
        $lesson=lesson::find($id);
        
        $validatelesson = Validator::make($request->all(), [
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
        if($lesson){  
            $lesson->update($validatelesson->validated());
          
            $lesson->save();
            
            return response()->json(
                'تم تعديل البيانات  بنجاح'
                , 200);
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
}