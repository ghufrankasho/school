<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\Teacher;
use App\Models\Account;
class TeacherController extends Controller
{
public function index(){
        $teachers=teacher::get();
        return response()->json(
        $teachers
        ,200);
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
            'image' => 'file|required|mimetypes:image/jpeg,image/png,image/gif,image/svg+xml,image/webp,application/wbmp',

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

        $teacher = teacher::create(array_merge(
            $validateateacher->validated()
            
            ));
        $account=Account::find($request->account_id);
        $teacher->account()->associate($account);   
        $result=$teacher->save();
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
            ['id'=>'required|integer|exists:teachers,id']);
        if($validate->fails()){
        return response()->json([
           'status' => false,
           'message' => 'خطأ في التحقق',
           'errors' => $validate->errors()
        ], 422);}
      
        $teacher=teacher::find($id);
     
       
      if($teacher){ 
            if($teacher->image!=null){
                $this->deleteImage($teacher->image);
            } 
             //delete account of teacher     
            $account=$teacher->account()->first();
             
            if($account){
                $teacher->account()->dissociate( $account);
                $account->delete();
            }   
            $result= $teacher->delete();
        if($result){ 
            return response()->json(
            ' تم حذف بيانات  بنجاح'
            , 200);
        }
        }

        return response()->json(null, 422);
    }
    catch (ValidationException $e) {
        return response()->json(['errors' => $e->errors()], 422);
    } 
    catch (\Exception $e) {
        return response()->json(['message' => 'An error occurred while deleting the teacher.'], 500);
    }
}
public function update(Request $request, $id){
    try{
        $input = [ 'id' =>$id ];
        $validate = Validator::make( $input,
        ['id'=>'required|integer|exists:teachers,id']);
        if($validate->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'خطأ في التحقق',
                    'errors' => $validate->errors()
                ], 422);
            }
            
        $teacher=teacher::find($id);
        
        $validateteacher = Validator::make($request->all(), [
            'name' => 'nullable|string',
            'email'=>'nullable|string|email|unique:teachers',
            'specilty' => 'nullable|string',
            'phone' => 'nullable|string',
            'description' => 'nullable|string',
            'image' => 'nullable|file|mimetypes:image/jpeg,image/png,image/gif,image/svg+xml,image/webp,application/wbmp',
         ]);
        $validateteacher->sometimes('image', 'mimetypes:image/vnd.wap.wbmp', function ($input) {
            return $input->file('image') !== null && $input->file('image')->getClientOriginalExtension() === 'wbmp';
        });
        
        if($validateteacher->fails()){
            return response()->json([
                'status' => false,
                'message' => 'خطأ في التحقق',
                'errors' => $validateteacher->errors()
            ], 422);
        }
        if($teacher){  
            $teacher->update($validateteacher->validated());
            if($request->hasFile('image') and $request->file('image')->isValid()){
                if($teacher->image !=null){
                    $this->deleteImage($teacher->image);
                }
                $teacher->image = $this->store_image($request->file('image')); 
            }

            
            
            $teacher->save();
            
            return response()->json(
                'تم تعديل بيانات المستخدم بنجاح'
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
    $file->move(public_path('teachers'), $imageName);

    // Get the full path to the saved image
    $imagePath = asset('teachers/' . $imageName);
            
     
   
   return $imagePath;

}
}