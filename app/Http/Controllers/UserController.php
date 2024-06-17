<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Account;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function index(){
        $users=User::get();
        return response()->json(
            $users
            ,200);
    }
    public function store(Request $request){
        
        try{
            
              
            $validateauser = Validator::make($request->all(), 
            [
               'name' => 'string|required',
               'email'=>'required|string|email|unique:users',
               'address' => 'nullable|string',
               'phone' => 'string|required',
               'description' => 'string|required',
               'account_id' => 'integer|exists:accounts,id|unique:users',
               'image' => 'file|required|mimetypes:image/jpeg,image/png,image/gif,image/svg+xml,image/webp,application/wbmp',

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

           
             
                
            if($request->hasFile('image') and $request->file('image')->isValid()){
                $image= $this->store_image($request->file('image')); 
            }
          
            $user = User::create(array_merge(
                $validateauser->validated()
                
                ));
            $user->image=$image;
            
            $account=Account::find($request->account_id);
            $user->account()->associate($account);
            $result=$user->save();
           if ($result){
               
                return response()->json(
                 'تم أضافة بيانات البروفايل بنجاح'
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
                ['id'=>'required|integer|exists:users,id']);
            if($validate->fails()){
            return response()->json([
               'status' => false,
               'message' => 'خطأ في التحقق',
               'errors' => $validate->errors()
            ], 422);}
          
            $user=User::find($id);
         
           
          if($user){ 
                if($user->image!=null){
                    $this->deleteImage($user->image);
                } 
                 //delete account of user     
                $account=$user->account()->first();
                 
                if($account){
                    $user->account()->dissociate( $account);
                    $account->delete();
                }   
                $result= $user->delete();
            if($result){ 
                return response()->json(
                ' تم حذف بيانات البروفايل بنجاح'
                , 200);
            }
            }

            return response()->json(null, 422);
        }
        catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } 
        catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while deleting the user.'], 500);
        }
    }
    public function update(Request $request, $id){
        try{
            $input = [ 'id' =>$id ];
            $validate = Validator::make( $input,
            ['id'=>'required|integer|exists:users,id']);
            if($validate->fails()){
                    return response()->json([
                        'status' => false,
                        'message' => 'خطأ في التحقق',
                        'errors' => $validate->errors()
                    ], 422);
                }
                
            $user=User::find($id);
            
            $validateuser = Validator::make($request->all(), [
                'name' => 'nullable|string',
                'email'=>'nullable|string|email|unique:users',
                'address' => 'nullable|string',
                'phone' => 'nullable|string',
                'description' => 'nullable|string',
                'image' => 'nullable|file|mimetypes:image/jpeg,image/png,image/gif,image/svg+xml,image/webp,application/wbmp',
             ]);
            $validateuser->sometimes('image', 'mimetypes:image/vnd.wap.wbmp', function ($input) {
                return $input->file('image') !== null && $input->file('image')->getClientOriginalExtension() === 'wbmp';
            });
            
            if($validateuser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'خطأ في التحقق',
                    'errors' => $validateuser->errors()
                ], 422);
            }
            if($user){  
                $user->update($validateuser->validated());
                if($request->hasFile('image') and $request->file('image')->isValid()){
                    if($user->image !=null){
                        $this->deleteImage($user->image);
                    }
                    $user->image = $this->store_image($request->file('image')); 
                }
    
                
                
                $user->save();
                
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
        $file->move(public_path('users'), $imageName);

        // Get the full path to the saved image
        $imagePath = asset('users/' . $imageName);
                
         
       
       return $imagePath;

    }
    
}