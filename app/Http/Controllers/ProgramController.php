<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use App\Models\Program;
use App\Models\TypeSection;

class ProgramController extends Controller
{
public function index(){
        $programs=program::get();
        return response()->json(
            $programs
            ,200);
}
public function store(Request $request){
    
    try{
        
        $validateaprogram = Validator::make($request->all(), 
        [
           'name' => 'string|required|unique:programs',
           'description' => 'string|required',
           'day' => 'date|required',
           'section_type_id' => 'integer|required|exists:section_type',
           
        ]);
    

        if($validateaprogram->fails()){
            return response()->json([
                'status' => false,
                'message' => 'خطأ في التحقق',
                'errors' => $validateaprogram->errors()
            ], 422);
        }
        
        $program = program::create(array_merge(
            $validateaprogram->validated()
            
            ));
        $section_type=TypeSection::find($request->section_type_id); 
        if($section_type) $program->section_type()->associate($section_type);  
        
        $result=$program->save();
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
            ['id'=>'required|integer|exists:programs,id']);
        if($validate->fails()){
        return response()->json([
           'status' => false,
           'message' => 'خطأ في التحقق',
           'errors' => $validate->errors()
        ], 422);}
      
        $program=program::find($id);
     
       
      if($program){ 
            
        $result= $program->delete();
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
        return response()->json(['message' => 'An error occurred while deleting the program.'], 500);
    }
}
public function update(Request $request, $id){
    try{
        $input = [ 'id' =>$id ];
        $validate = Validator::make( $input,
        ['id'=>'required|integer|exists:programs,id']);
        if($validate->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'خطأ في التحقق',
                    'errors' => $validate->errors()
                ], 422);
            }
            
        $program=program::find($id);
        
        $validateprogram = Validator::make($request->all(), [
            'name' => 'nullable|string|unique:programs',
           'description' => 'nullable|string',
           'day' => 'nullable|date',
           'section_type_id' => 'nullable|integer|exists:section_type',
           
          ]);
       
        if($validateprogram->fails()){
            return response()->json([
                'status' => false,
                'message' => 'خطأ في التحقق',
                'errors' => $validateprogram->errors()
            ], 422);
        }
        
        if($program){  
           
            $program->update($validateprogram->validated());
            
            if($request->section_type_id!=null)
            {
                $section_type=TypeSection::find($request->section_type_id); 
                
                $current_section_type=$program->section_type()->first();
                
                if($current_section_type)$program->section_type()->dissociate($current_section_type);
                
                if($section_type) $program->section_type()->associate($section_type);
            } 
            $program->save();
            
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