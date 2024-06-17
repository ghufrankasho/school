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
        return response()->json(
            $sections
            ,200);
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
            ['id'=>'required|integer|exists:sections,id']);
        if($validate->fails()){
        return response()->json([
           'status' => false,
           'message' => 'خطأ في التحقق',
           'errors' => $validate->errors()
        ], 422);}
      
        $section=section::find($id);
     
       
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
        return response()->json(['message' => 'An error occurred while deleting the section.'], 500);
    }
}
public function update(Request $request, $id){
    try{
        $input = [ 'id' =>$id ];
        $validate = Validator::make( $input,
        ['id'=>'required|integer|exists:sections,id']);
        if($validate->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'خطأ في التحقق',
                    'errors' => $validate->errors()
                ], 422);
            }
            
        $section=section::find($id);
        
        $validatesection = Validator::make($request->all(), [
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
        if($section){  
            $section->update($validatesection->validated());
          
            $section->save();
            
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