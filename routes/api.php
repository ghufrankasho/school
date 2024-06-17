<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExampController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TypeController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\UserController;
 
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherController;
use App\Models\Employee;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::group([  'middleware' => 'api','prefix' => 'auth'], function ($router) {
    Route::post('/login',  [AuthController::class,'login']);
    Route::post('/register', [AuthController::class,'register']);
    Route::get('/logout',  [AuthController::class,'logout']);
    Route::post('/refresh',  [AuthController::class,'refresh']);
    Route::post('/update',  [AuthController::class,'update'] );
});
Route::controller(UserController::class)->prefix('user')->group(function (){
    
    Route::get('/','index');
   
    Route::post('/add','store');
    Route::delete('/delete/{id}','destroy');
    Route::post('/update/{id}','update');
    // route belongto account
    
    
    
});
Route::controller(TeacherController::class)->prefix('teacher')->group(function (){
    
    Route::get('/','index');
   
    Route::post('/add','store');
    Route::delete('/delete/{id}','destroy');
    Route::post('/update/{id}','update');
   
});
Route::controller(SubjectController::class)->prefix('subject')->group(function (){
    
    Route::get('/','index');
    Route::post('/add','store');
    Route::delete('/delete/{id}','destroy');
    Route::post('/update/{id}','update');
 
});
Route::controller(SectionController::class)->prefix('section')->group(function (){
    
    Route::get('/','index');
    Route::post('/add','store');
    Route::delete('/delete/{id}','destroy');
    Route::post('/update/{id}','update');
 
});
Route::controller(ProgramController::class)->prefix('profram')->group(function (){
    
    Route::get('/','index');
    Route::post('/add','store');
    Route::delete('/delete/{id}','destroy');
    Route::post('/update/{id}','update');
 
});
Route::controller(TypeController::class)->prefix('type')->group(function (){
    
    Route::get('/','index');
    Route::post('/add','store');
    Route::delete('/delete/{id}','destroy');
    Route::post('/update/{id}','update');
    Route::post('/assing_section_to_type','assing_section_to_type');
 
});

Route::controller(ExampController::class)->prefix('examp')->group(function (){
    
    Route::get('/','index');
    Route::post('/add','store');
    Route::delete('/delete/{id}','destroy');
    Route::post('/update/{id}','update');
 
 
});
 
 