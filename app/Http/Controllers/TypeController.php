<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use App\Models\Type;
use App\Models\Section;

class TypeController extends Controller
{
public function index(){
        $types=Type::with('sections')->get();
        return response()->json(
            $types
            ,200);
}
public function store(Request $request){
    
    try{
        
        $validateatype = Validator::make($request->all(), 
        [
           'name' => 'string|required|unique:types',
           'description' => 'string|required',
           'total_amount' => 'integer|required',
           
        ]);
    

        if($validateatype->fails()){
            return response()->json([
                'status' => false,
                'message' => 'خطأ في التحقق',
                'errors' => $validateatype->errors()
            ], 422);
        }

        $type = type::create(array_merge(
            $validateatype->validated()
            
            ));
            
        $result=$type->save();
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
            ['id'=>'required|integer|exists:types,id']);
        if($validate->fails()){
        return response()->json([
           'status' => false,
           'message' => 'خطأ في التحقق',
           'errors' => $validate->errors()
        ], 422);}
      
        $type=type::find($id);
     
       
      if($type){ 
        $sections=$type->sections()->get();
        if($sections){
            foreach($sections as $section){
                $section->types()->detach($type);
                $section->save();
            }
        }
            $result= $type->delete();
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
        return response()->json(['message' => 'An error occurred while deleting the type.'], 500);
    }
}
public function update(Request $request, $id){
    try{
        $input = [ 'id' =>$id ];
        $validate = Validator::make( $input,
        ['id'=>'required|integer|exists:types,id']);
        if($validate->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'خطأ في التحقق',
                    'errors' => $validate->errors()
                ], 422);
            }
            
        $type=type::find($id);
        
        $validatetype = Validator::make($request->all(), [
            'name' => 'nullable|string',
            'description' => 'nullable|string',
            'total_amount' => 'nullable|integer',
          ]);
       
        if($validatetype->fails()){
            return response()->json([
                'status' => false,
                'message' => 'خطأ في التحقق',
                'errors' => $validatetype->errors()
            ], 422);
        }
        if($type){  
            $type->update($validatetype->validated());
          
            $type->save();
            
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
public function assing_section_to_type(Request $request){
    try {  
         
        
        $validate = Validator::make( $request->all(),
            ['type_id'=>'required|integer|exists:types,id',
             'section_id'=>'required|integer|exists:sections,id']);
        if($validate->fails()){
        return response()->json([
           'status' => false,
           'message' => 'خطأ في التحقق',
           'errors' => $validate->errors()
        ], 422);}
      
        $type=type::find($request->type_id);
       
        $section=section::find($request->section_id);
       
        $result=false;
        
       if($type && $section){
        $type->sections()->attach($section);
        return response()->json(
            "assing section to type successfuly"
            , 200);
         
       }
        
      
         
        
        

        return response()->json("null", 422);
    }
    catch (ValidationException $e) {
        return response()->json(['errors' => $e->errors()], 422);
    } 
    catch (\Exception $e) {
        return response()->json(['message' =>$e->getMessage(),
         'An error occurred while assing_section_to_type.'], 500);
    }
}
}