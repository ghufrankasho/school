<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use App\Models\Examp;
use App\Models\TypeSection;
use App\Models\Teacher;
class ExampController extends Controller
{
    public function index(){
        $examps=examp::get();
        if($examps){
            return response()->json(
                [
                      'status' => true,
                      'message' => 'تم الحصول على البيانات بنجاح', 
                      'data'=> $examps,
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
        
        $validateaexamp = Validator::make($request->all(), 
        [
           'name' => 'string|required',
           'time' => 'date_format:H:i|required',
           'day' => 'date|required',
           'type_section_id' => 'integer|required|exists:type_sections,id',
           
          
           
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
            $TypeSection=TypeSection::find($request->type_section_id);
            $result=$examp->type_section()->associate($TypeSection);
            $examp->save();
       if ($result){
           
            return response()->json(
                   [ 'status' => true,
                    'message' => 'تم أضافة البيانات  بنجاح', 
                    'data'=> $examp,]
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
        
        $examps_examps= $examp->examps_examps()->get();
        if($examps_examps){
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