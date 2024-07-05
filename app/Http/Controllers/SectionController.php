<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use App\Models\Section;

class SectionController extends Controller
{
public function index(){
        $sections=section::get();
        if( $sections){
       return response()->json(
                [
                    'status' => true,
                    'message' => "تم الحصول على البيانات بنجاح", 
                    'data'=> $sections,
                ]
             , 200);
            }
       else{
            return response()->json(
                [  'status' => false,
                'message' => ' حدث خطأ الحصول على البيانات' ,
                'data' => null],
                422);
            }
}
public function store(Request $request){
    
    try{
        
        $validateasection = Validator::make($request->all(), 
        [
           'name' => 'string|required|unique:sections',
           'description' => 'string|required',
           
        ]);
    

        if($validateasection->fails()){
            return response()->json([
                'status' => false,
                'message' => 'خطأ في التحقق',
                'errors' => $validateasection->errors()
            ], 422);
        }

        $section = section::create(array_merge(
            $validateasection->validated()
            
            ));
            
        $result=$section->save();
       if ($result){
           
           return response()->json(
                [
                    'status' => true,
                    'message' => 'تم أضافة البيانات  بنجاح', 
                    'data'=> $section,
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
            ['id'=>'required|integer|exists:sections,id']);
        if($validate->fails()){
        return response()->json([
           'status' => false,
           'message' => 'خطأ في التحقق',
           'errors' => $validate->errors()
        ], 422);}
      
        $section=section::find( $request->id);
     
   
      if($section){ 
            $types=$section->types()->get();
            if($types){
                foreach($types as $type){
                    $type->sections()->detach($section);
                    $type->save();
                }
            }
            $result= $section->delete();
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
        return response()->json(['message' => 'An error occurred while deleting the section.'], 500);
    }
}
public function update(Request $request){
    try{
         
      
            
       
        
        $validatesection = Validator::make($request->all(), [
            'id'=>'required|integer|exists:sections,id',
            'name' => 'nullable|string',
            'description' => 'nullable|string',
          ]);
       
        if($validatesection->fails()){
            return response()->json([
                'status' => false,
                'message' => 'خطأ في التحقق',
                'errors' => $validatesection->errors()
            ], 422);
        }
        $section=section::find($request->id);
        if($section){  
            $section->update($validatesection->validated());
          
            $result=  $section->save();
            
            if($result){
                 return response()->json(
                [
                   'status' => true,
                   'message' =>   'تم تعديل البيانات  بنجاح',
                    'data'=> $result,
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
 
}