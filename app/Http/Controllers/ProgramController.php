<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use App\Models\Program;
use App\Models\ProgramLesson;
use App\Models\Lesson;
use App\Models\Teacher;
use App\Models\TypeSection;

class ProgramController extends Controller
{
public function index(){
        $programs=program::with('program_lesson')->get();
        if($programs){
            return response()->json(
                [
                      'status' => true,
                      'message' => 'تم الحصول على البيانات بنجاح', 
                      'data'=> $programs,
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
        
        $validateaprogram = Validator::make($request->all(), 
        [
           'name' => 'string|required',
           'description' => 'string|required',
           
           'type_section_id' => 'integer|required|exists:type_sections,id',
           
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
           
        $section_type=TypeSection::find($request->type_section_id); 
        
        if($section_type){
            
            $program->type_section()->associate($section_type);
             
             } 
        
        $result=$program->save();
        if ($result){
           
            return response()->json(
                [
                    'status' => true,
                    'message' => 'تم أضافة البيانات  بنجاح', 
                    'data'=> $program,
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
            ['id'=>'required|integer|exists:programs,id']);
        if($validate->fails()){
            
        return response()->json([
           'status' => false,
           'message' => 'خطأ في التحقق',
           'errors' => $validate->errors()
        ], 422);}
      
    $program=program::find($request->id);
     
       
    if($program){ 
        // dissociate program from section type 'the class'
        $section_type=$program->section_type()->first();
        if($section_type){
            $program->section_type()->dissociate($section_type);
            $program->save();
        }
        // delete all program data in program_lesson table
        $program_lessons=$program->program_lesson()->get();
        
        foreach($program_lessons as $program_lesson){
            $program_lesson->program()->dissociate($program);
            $program_lesson->save();
            $program_lesson->delete();
        }
        
        
        $result= $program->delete();
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
        return response()->json(['message' => 'An error occurred while deleting the program.'], 500);
    }
}
public function update(Request $request){
    try{
        

        
        $validateprogram = Validator::make($request->all(), [
           
           'id'=>'required|integer|exists:programs,id',
           'name' => 'nullable|string|unique:programs',
           'description' => 'nullable|string',
           
           'type_section_id' => 'nullable|integer|exists:type_sections,id',
           
          ]);
       
        if($validateprogram->fails()){
            return response()->json([
                'status' => false,
                'message' => 'خطأ في التحقق',
                'errors' => $validateprogram->errors()
            ], 422);
        }
        $program=program::find($request->id);
        if($program){  
           
            $program->update($validateprogram->validated());
            
            if($request->type_section_id!=null)
            {
                return 
                $section_type=TypeSection::find($request->type_section_id); 
                
                $current_section_type=$program->section_type()->first();
                
                if($current_section_type)$program->section_type()->dissociate($current_section_type->id);
                
                if($section_type) $program->section_type()->associate($section_type->id);
            } 
             $result=$program->save();
            
            if($result){
                return response()->json(
                    [
                       'status' => true,
                       'message' =>   'تم تعديل البيانات  بنجاح',
                       'data'=> $program,
                   ], 200);
                }
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
public function sotre_program_detailes(Request $request){
    
    try{
        
        $validateaprogram = Validator::make($request->all(), 
        [
           'time' =>'date_format:H:i',     
           'program_id' => 'integer|required|exists:programs,id',
           'teacher_id' => 'integer|required|exists:teachers,id',
           'lesson_id' => 'integer|required|exists:lessons,id',
           
        ]);
    

        if($validateaprogram->fails()){
            return response()->json([
                'status' => false,
                'message' => 'خطأ في التحقق',
                'errors' => $validateaprogram->errors()
            ], 422);
        }
       
        $program_lesson = ProgramLesson::create(array_merge(
            $validateaprogram->validated()
            
            ));
           
        $lesson=lesson::find($request->lesson_id); 
        $teacher=teacher::find($request->teacher_id); 
        $program=program::find($request->program_id); 
        if($lesson){ $program_lesson->lesson()->associate($lesson); } 
        if($teacher){ $program_lesson->teacher()->associate($teacher); } 
        if($program){ $program_lesson->program()->associate($program); } 
        
        $result=$program_lesson->save();
        if ($result){
           
            return response()->json(
                [
                    'status' => true,
                    'message' => 'تم أضافة البيانات  بنجاح', 
                    'data'=> $program_lesson,
                ]
             , 200);
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
public function attach_lessos( $request,$program){
         
    // Split the string into an array of category IDs
     $ids= $request->category_ids;
     
     $categoryIdsArray = explode(',', $ids[0]);
     
     // Convert the array elements to integers (if needed)
      $categoryIdsArray = array_map('intval', $categoryIdsArray);
     $program->subjects()->attach($categoryIdsArray);
}
}