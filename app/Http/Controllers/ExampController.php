<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use App\Models\Examp;
use App\Models\TypeSection;
use App\Models\User;
use App\Models\Quest;
use App\Models\Teacher;
class ExampController extends Controller
{
    public function index(){
        $examps=examp:: get();
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
            'duration' => 'date_format:H:i|required',
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
    public function store_question(Request $request){
        
        try{
            
            $validateaquestion = Validator::make($request->all(), 
            [
            'name' => 'string|required',
            'option1' => 'string|required',
            'option2' => 'string|required',
            'option3' => 'string|required',
            'answer' => 'string|required',
            'mark' => 'integer|required',
            'examp_id' => 'integer|required|exists:examps,id',
            
            ]);
            if($validateaquestion->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'خطأ في التحقق',
                    'errors' => $validateaquestion->errors()
                ], 422);
            }

            $question = Quest::create(array_merge(
                $validateaquestion->validated()
                
                ));
                $examp=Examp::find($request->examp_id);
                $result=$question->examp()->associate($examp);
                $question->save();
            if ($result){
                
                    return response()->json(
                        [ 'status' => true,
                            'message' => 'تم أضافة البيانات  بنجاح', 
                            'data'=> $question,]
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
    public function show(Request $request){
        try {  
            
            
            
            $validate = Validator::make( $request->all(),
                ['id'=>'required|integer|exists:examps,id']);
            if($validate->fails()){
            return response()->json([
               'status' => false,
               'message' => 'خطأ في التحقق',
               'errors' => $validate->errors()->first()
            ], 422);}
       
        
            $examp=Examp::with('quest')->find($request->id);
           
         
          if($examp){ 
            return response()->json(
                [
                      'status' => true,
                      'message' => 'تم الحصول على البيانات بنجاح', 
                      'data'=> $examp,
                  ],200);
            } 
                 

            return response()->json(['message'=>" حدث خطأ أثناء عملية جلب البيانات "], 422);
        }
        catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } 
        catch (\Exception $e) {
            return response()->json(['message' =>$e
            //  'حدث خطأ أثناء عملية جلب البيانات'
            ], 
             500);
        }
    }
    public function  set_result_examp(Request $request){
        try {  
            
            $validate = Validator::make( $request->all(),
                [
                    'user_id'=>'required|integer|exists:users,id',
                    'examp_id'=>'required|integer|exists:examps,id',
                    'result'=>'required|integer|min:0|max:100'
                ]);
            if($validate->fails()){
            return response()->json([
               'status' => false,
               'message' => 'خطأ في التحقق',
               'errors' => $validate->errors()
            ], 422);}
          
            $user= User::find($request->user_id);
         
           $examp=Examp::find($request->examp_id);
          if($examp && $user){ 
                 
            $user->examps()->updateExistingPivot($request->examp_id,['result'=>$request->result]);
            
            }
       
            $result= $user->save();
            
             if($result){
                $pivotData = $user->examps()->wherePivot('examp_id', $request->examp_id)->first()->pivot;

                return response()->json([
                    'status' => true,
                    'message' => 'تم أضافة النتيجة  بنجاح',
                    'data' => [
                        // 'examp' => $examp,
                        'pivot' => $pivotData
                    ]
                ], 200);
             }
                  
             
             
     
             return response()->json(    
                 [  'status' => false,
                    'message' => 'حدث خطأ بدأ المذاكرة ',
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
}