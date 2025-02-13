<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\Teacher;
use App\Models\Account;
use App\Models\Subject;
use App\Models\Homework;
use App\Models\User;

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
public function unblock(Request $request){
        
        
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
        $teacher->block=false;
        $result=$teacher->save();
        if($result){
        return response()->json(
            [
                  'status' => true,
                  'message' => 'تم  إلغاء حظر الاستاذ بنجاح', 
                  'data'=> $teacher,
              ],200);
            }
        }
         else{
              return response()->json(
                      [  'status' => false,
                      'message' => 'حدث خطأ أثناء  إلغاء حظر الاستاذ',
                      'data' => null],
                      422);
              }
}
public function store(Request $request){
    
    try{
        
        $validateateacher = Validator::make($request->all(), 
        [
            'name' => 'string|required',
             
            'phone' => 'string|required',
            'description' => 'string|required',
            'account_id' => 'integer|exists:accounts,id|unique:teachers',
            'subject_id' => 'integer|exists:subjects,id',
            'image' => 'file|mimetypes:image/jpeg,image/png,image/gif,image/svg+xml,image/webp,application/wbmp',
        ]);
    $validateateacher->sometimes('image', 'required|mimetypes:image/vnd.wap.wbmp', function ($input) {
        return $input->file('image') !== null && $input->file('image')->getClientOriginalExtension() === 'wbmp';
    });
       
    

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
public function upLoadImage( $file){
    $extension = $file->getClientOriginalExtension();
       
    $imageName = uniqid() . '.' .$extension;
    $file->move(public_path('teachers'), $imageName);

    // Get the full path to the saved image
    $imagePath = asset('teachers/' . $imageName);
            
     
   
   return $imagePath;

}
public function accept(Request $request)
{
    try {  
        
        $validate = Validator::make( $request->all(),
            ['id'=>'required|integer|exists:teachers,id',
            'accept'=>'required|boolean']);
        if($validate->fails()){
        return response()->json([
           'status' => false,
           'message' => 'خطأ في التحقق',
           'errors' => $validate->errors()
        ], 422);}
      
        $teacher=teacher::find($request->id);
        
      
      if($teacher){ 
            $account=$teacher->account;
          
            $account->is_accept=$request->accept;
            $result= $account->save();
        if($result){
             
            return response()->json(
                [
                     'status' => true,
                     'message' =>' تمت العملية البيانات بنجاح', 
                     'data'=> $teacher,
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
public function rate_student(Request $request)
{
    try {  
        
        $validate = Validator::make( $request->all(),
            ['teacher_id'=>'required|integer|exists:teachers,id',
            'user_id'=>'required|integer|exists:users,id',
            'grading'=>'required|integer|min:0|max:100']);
        if($validate->fails()){
        return response()->json([
           'status' => false,
           'message' => 'خطأ في التحقق',
           'errors' => $validate->errors()
        ], 422);}
      
        $teacher=teacher::find($request->teacher_id);
        $user=User::find($request->user_id);
      
      if($teacher && $user){ 
            $user->grading=$request->grading;
            $result=$user->save();
           
        if($result){
             
            return response()->json(
                [
                     'status' => true,
                     'message' =>' تم تقييم الطالب  بنجاح', 
                     'data'=> $user,
                 ], 200);
              
         }
         }
 
         return response()->json(    
             [  'status' => false,
                'message' => 'حدث خطأ أثناء تقييم الطالب ',
                'data' => null],
             422);
    }
    catch (ValidationException $e) {
        return response()->json(['errors' => $e->errors()], 422);
    } 
    catch (\Exception $e) {
        return response()->json(['message' =>  'حدث خطأ أثناء تقييم الطالب '], 500);
    }
}
// public function deleteImage($url){
 
//     // Get the full path to the image
   
//     $fullPath =$url;
     
//     $parts = explode('/',$fullPath,5);
   
//     $fullPath = public_path($parts[3].'/'.$parts[4]);

//     // Check if the image file exists and delete it
//     if (file_exists($fullPath)) {
//         unlink($fullPath);
        
//         return true;
//      }
//      else return false;
// }
// public function upLoadImage($photo){
//     $file = base64_decode($photo);
//     $png_url = uniqid().".png";
//     $path='teachers/'.$png_url;
//     $success = file_put_contents($path, $file);
//     $url  = asset('teachers/'. $png_url);
//     return    $url;
      
    
// }
// ///////////////HOMEWORK FUNCTIONS////
/**
 * get homework that belongto teacher 
 */
public function index_hw(Request $request){
    
    try {  
         
 
        $validate = Validator::make( $request->all(),
            ['teacher_id'=>'required|integer|exists:teachers,id']);
        if($validate->fails()){
        return response()->json([
           'status' => false,
           'message' => 'خطأ في التحقق',
           'errors' => $validate->errors()
        ], 422);}
      
        $homworks=Teacher::with('homeworks')->find($request->teacher_id);
        if($homworks)
         {  return response()->json(
             [
                     'status' => true,
                     'message' => 'تم الحصول على البيانات بنجاح', 
                     'data'=> $homworks,
                 ],200);
         }
        else{
             return response()->json(
                     [  'status' => false,
                     'message' => 'حدث خطأ أثناء جلب البيانات',
                     'data' => null],
                     422);
             } }
    catch (ValidationException $e) {
        return response()->json(['errors' => $e->errors()], 422);
    } 
    catch (\Exception $e) {
        return response()->json(['message' => 'An error occurred while getting this date.'], 500);
    }
}
/**
 * store homework 
 */
public function store_hm(Request $request){
    
    try{
        
        $validateateacher = Validator::make($request->all(), 
        [
            'text' => 'string|required',
            'end_date'=>'required|date',
            'teacher_id' => 'integer|exists:teachers,id|unique:teachers',
            'type_section_id' => 'nullable|integer|exists:type_sections,id',
            
         ]);
       
    

        if($validateateacher->fails()){
            return response()->json([
                'status' => false,
                'message' => 'خطأ في التحقق',
                'errors' => $validateateacher->errors()
            ], 422);
        }
         
        $homwork = Homework::create(array_merge(
            $validateateacher->validated()
            
            ));
        
        $teacher=Teacher::find($request->teacher_id);
        
        if($teacher)$homwork->teacher()->associate($teacher); 
         
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
}