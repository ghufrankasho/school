<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Account;
use App\Models\TypeSection;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function index(){
        $users=User::get();
        if($users){
            return response()->json(
                [
                      'status' => true,
                      'message' => 'تم الحصول على البيانات بنجاح', 
                      'data'=> $users,
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
            ['id'=>'required|integer|exists:users,id']);
        if($validate->fails()){
        return response()->json([
           'status' => false,
           'message' => 'خطأ في التحقق',
           'errors' => $validate->errors()
        ], 422);}
      
        $user=User::find($request->id);
        if($user){
            $user->block=true;
            $result=$user->save();
            if($result){
            return response()->json(
                [
                      'status' => true,
                      'message' => 'تم حظر المستخدم بنجاح', 
                      'data'=> $user,
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
            ['id'=>'required|integer|exists:users,id']);
        if($validate->fails()){
        return response()->json([
           'status' => false,
           'message' => 'خطأ في التحقق',
           'errors' => $validate->errors()
        ], 422);}
      
        $user=user::find($request->id);
        if($user){
            $user->block=false;
            $result=$user->save();
            if($result){
            return response()->json(
                [
                      'status' => true,
                      'message' => 'تم  إلغاء حظر الطالب بنجاح', 
                      'data'=> $user,
                  ],200);
                }
            }
             else{
                  return response()->json(
                          [  'status' => false,
                          'message' => 'حدث خطأ أثناء  إلغاء حظر الطالب',
                          'data' => null],
                          422);
                  }
    }
    public function store(Request $request){
        
        try{
            
              
            $validateauser = Validator::make($request->all(), 
            [
               'name' => 'string|required',

               'address' => 'nullable|string',
               'phone' => 'string|required',
               'class_name' => 'string|required',
               'account_id' => 'integer|exists:accounts,id|unique:users',
               'image' => 'file|mimetypes:image/jpeg,image/png,image/gif,image/svg+xml,image/webp,application/wbmp',
            ]);
        $validateauser->sometimes('image', 'required|mimetypes:image/vnd.wap.wbmp', function ($input) {
            return $input->file('image') !== null && $input->file('image')->getClientOriginalExtension() === 'wbmp';
        });
        
           

            if($validateauser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'خطأ في التحقق',
                    'errors' => $validateauser->errors()
                ], 422);
            }

           
             
            $image= $this->store_image($request->image); 
          
            $user = User::create(array_merge(
                $validateauser->validated()
                
                ));
            $user->image=$image;
            
            $account=Account::find($request->account_id);
            $user->account()->associate($account);
            $result=$user->save();
            
       if ($result){
           
            return response()->json(
                [
                    'status' => true,
                    'message' => 'تم أضافة البيانات  بنجاح', 
                    'data'=> $user,
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
                ['id'=>'required|integer|exists:users,id']);
            if($validate->fails()){
            return response()->json([
               'status' => false,
               'message' => 'خطأ في التحقق',
               'errors' => $validate->errors()
            ], 422);}
          
            $user=User::find($request->id);
         
           
          if($user){ 
                if($user->image!=null){
                    $this->deleteImage($user->image);
                } 
                 //delete account of user     
                $account=$user->account()->first();
                 
                if($account){
                    $user->account()->dissociate($account);
                    $user->save();
                    $account->delete();
                     
                }   
                $result= $user->delete();
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
            return response()->json(['message' => 'An error occurred while deleting the user.'], 500);
        }
    }
    public function update(Request $request){
        try{
            
           
            $validateuser = Validator::make($request->all(), [
                'id'=>'required|integer|exists:users,id',
                'name' => 'nullable|string',

                'address' => 'nullable|string',
                'phone' => 'nullable|string',
                'class_name' => 'nullable|string',
                'image' => 'file|mimetypes:image/jpeg,image/png,image/gif,image/svg+xml,image/webp,application/wbmp',
                ]);
            $validateuser->sometimes('image', 'required|mimetypes:image/vnd.wap.wbmp', function ($input) {
                return $input->file('image') !== null && $input->file('image')->getClientOriginalExtension() === 'wbmp';
            });
            
             
            
            if($validateuser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'خطأ في التحقق',
                    'errors' => $validateuser->errors()
                ], 422);
            }
            $user=User::find($request->id);
            
            if($user){  
                $user->update($validateuser->validated());
                if($request->image != null){
                    if($user->image != null){
                        $this->deleteImage($user->image);
                    }
                    $user->image=$this->store_image($request->image); 

                } 
                $result= $user->save();
            
                if($result){
                  return response()->json(
                      [
                         'status' => true,
                         'message' =>   'تم تعديل البيانات  بنجاح',
                         'data'=> $user,
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
    //     $path='users/'.$png_url;
    //     $success = file_put_contents($path, $file);
    //     $url  = asset('users/'. $png_url);
    //     return    $url;
          
        
    // }
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
        $file->move(public_path('users'), $imageName);

        // Get the full path to the saved image
        $imagePath = asset('users/' . $imageName);
                
         
       
       return $imagePath;

    }
    public function assign_user_to_class_section(Request $request){
        try {  
            
            $validate = Validator::make( $request->all(),
                [
                'user_id'=>'required|integer|exists:users,id',
                'type_section_id'=>'required|integer|exists:type_sections,id'
                ]);
            if($validate->fails()){
            return response()->json([
               'status' => false,
               'message' => 'خطأ في التحقق',
               'errors' => $validate->errors()
            ], 422);}
          
            $user=User::find($request->user_id);
         
           
          if($user){ 
                 
            $type_section=TypeSection::find($request->type_section_id);
           if($type_section) $user->type_section()->associate($type_section);
            $result=$user->save(); 
                 
               
            if($result && $type_section){
                 
                return response()->json(
                    [
                         'status' => true,
                         'message' =>' تم أضافة طالب الى شعبة بنجاح', 
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
            return response()->json(['message' => 'An error occurred while adding  the user to class section.'], 500);
        }
    }
    
}