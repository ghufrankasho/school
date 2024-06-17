<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use App\Models\Examp;
use App\Models\Teacher;
class ExampController extends Controller
{
public function index(){
        $examps=examp::get();
        return response()->json(
            $examps
            ,200);
}
public function store(Request $request){
    
    try{
        
        $validateaexamp = Validator::make($request->all(), 
        [
           'name' => 'string|required|unique:examps',
           'teacher_id' => 'integer|exists:teachers,id|unique:examps',
           
        ]);
    

        if($validateaexamp->fails()){
            return response()->json([
                'status' => false,
                'message' => 'خطأ في التحقق',
                'errors' => $validateaexamp->errors()
            ], 422);
        }

        $examp = examp::create(array_merge(
            $validateaexamp->validated()
            
            ));
        $teacher=teacher::find($request->teacher_id);
        $examp->teacher()->associate($teacher);    
        $result=$examp->save();
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
            ['id'=>'required|integer|exists:examps,id']);
        if($validate->fails()){
        return response()->json([
           'status' => false,
           'message' => 'خطأ في التحقق',
           'errors' => $validate->errors()
        ], 422);}
      
        $examp=examp::find($id);
     
       
      if($examp){ 
        
        $users_examps= $examp->users_examps()->get();
        if($users_examps){
            return response()->json("you can not delete this examp becase is student need this eaxmp informations", 422); 
        }
        //delete all questions related to this eaxmp
        $questions= $examp->questions()->get();
        foreach($questions as $question){
            $question->examp()-> dissociate($examp);
            $question->save();
        }
        //dissociate this examp from teacher
        $teacher= $examp->teacher()->first();
        if($teacher) $examp->teacher()->dissociate( $teacher);
       
        $result= $examp->delete();
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
        return response()->json(['message' => 'An error occurred while deleting the examp.'], 500);
    }
}
public function update(Request $request, $id){
    try{
        $input = [ 'id' =>$id ];
        $validate = Validator::make( $input,
        ['id'=>'required|integer|exists:examps,id']);
        if($validate->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'خطأ في التحقق',
                    'errors' => $validate->errors()
                ], 422);
            }
            
        $examp=examp::find($id);
        
        $validateexamp = Validator::make($request->all(), [
            'name' => 'nullable|string',
            
          ]);
       
        if($validateexamp->fails()){
            return response()->json([
                'status' => false,
                'message' => 'خطأ في التحقق',
                'errors' => $validateexamp->errors()
            ], 422);
        }
        if($examp){  
            $examp->update($validateexamp->validated());
          
            $examp->save();
            
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