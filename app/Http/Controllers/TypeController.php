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
        if($types){
            return response()->json(
                [
                      'status' => true,
                      'message' => 'تم الحصول على البيانات بنجاح', 
                      'data'=> $types,
                  ],200);}
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
            [
                'status' => true,
                'message' => 'تم أضافة البيانات  بنجاح', 
                'data'=> $type,
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
            ['id'=>'required|integer|exists:types,id']);
        if($validate->fails()){
        return response()->json([
           'status' => false,
           'message' => 'خطأ في التحقق',
           'errors' => $validate->errors()
        ], 422);}
      
        $type=type::find($request->id);
     
       
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
        return response()->json(['message' => 'An error occurred while deleting the type.'], 500);
    }
}
public function update(Request $request){
    try{
     
            
        
        
        $validatetype = Validator::make($request->all(), [
            'name' => 'nullable|string',
            'description' => 'nullable|string',
            'total_amount' => 'nullable|integer',
            'id'=>'required|integer|exists:types,id'
          ]);
       
        if($validatetype->fails()){
            return response()->json([
                'status' => false,
                'message' => 'خطأ في التحقق',
                'errors' => $validatetype->errors()
            ], 422);
        }
        $type=type::find($request->id);
        if($type){  
            $type->update($validatetype->validated());
          
            $result= $type->save();
            
            if($result){
              return response()->json(
                  [
                     'status' => true,
                     'message' =>   'تم تعديل البيانات  بنجاح', 'data'=> $type,
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