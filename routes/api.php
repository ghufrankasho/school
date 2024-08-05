<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExampController;
use App\Http\Controllers\HomeworkController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SecretaryController;
use App\Http\Controllers\TypeController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PushNotificationController;
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

 
Route::get('/send-notification', [PushNotificationController::class, 'sendPushNotification']);
Route::group([  'middleware' => 'api','prefix' => 'auth'], function ($router) {
    Route::post('/login',  [AuthController::class,'login']);
    Route::post('/register', [AuthController::class,'register']);
    Route::get('/logout',  [AuthController::class,'logout']);
    Route::get('/profile',  [AuthController::class,'profile']);
    Route::post('/refresh',  [AuthController::class,'refresh']);
    Route::post('forgot_password', [AuthController::class, 'forgot_password']);
    Route::post('verify-reset-code', [AuthController::class, 'verify_reset_code']);
    Route::post('reset-password', [AuthController::class, 'reset_password']);
});
Route::controller(UserController::class)->prefix('user')->group(function (){
    
    Route::get('/','index');
    Route::get('/unblock','unblock');
    Route::post('/add','store');
    Route::get('/block','block');
    // قبول او رفض الطالب
    Route::get('/accept','accept');
    Route::get('/user_hw','user_hw');
    Route::get('/user_start_examp','user_start_examp');
    Route::get('/user_lessons','user_lessons');
    Route::get('/attendance','attendance');
    Route::get('/examps','examps');
    Route::get('/delete','destroy');
    Route::get('/assign_user_to_class_section','assign_user_to_class_section');
    Route::post('/update','update');
    Route::post('/sendNotification','sendNotification');
    // route belongto account
    
    
    
});
Route::controller(TeacherController::class)->prefix('teacher')->group(function (){
    
    Route::get('/','index');
    Route::get('/block','block');
    Route::get('/unblock','unblock');
    Route::post('/add','store');
    Route::post('/rate_student','rate_student');
    // قبول او رفض teacher
    Route::get('/accept','accept');
    Route::get('/delete','destroy');
    Route::get('/homework','index_hw');
    Route::post('/update','update');
   
});
Route::controller(SubjectController::class)->prefix('subject')->group(function (){
    
    Route::get('/','index');
    Route::post('/add','store');
    Route::get('/show','show');
    Route::get('/delete','destroy');
    Route::post('/update','update');
 
});
Route::controller(SectionController::class)->prefix('section')->group(function (){
    
    Route::get('/','index');
    Route::post('/add','store');
    Route::get('/delete','destroy');
    Route::post('/update','update');
 
});
Route::controller(ProgramController::class)->prefix('program')->group(function (){
    
    Route::get('/','index');
    Route::post('/add','store');
    Route::get('/delete','destroy');
    Route::post('/attach','sotre_program_detailes');
    Route::post('/update','update');
 
});
Route::controller(TypeController::class)->prefix('type')->group(function (){
    
    Route::get('/','index');
  
    Route::post('/add','store');
    Route::get('/delete','destroy');
    Route::post('/update','update');
    Route::get('/assing_section_to_type','assing_section_to_type');
 
});
Route::controller(LessonController::class)->prefix('lesson')->group(function (){
    
    Route::get('/','index');
    Route::post('/add','store');
    Route::get('/delete','destroy');
    Route::post('/update','update');
    Route::get('/show','show');
   
});

Route::controller(ExampController::class)->prefix('examp')->group(function (){
    
    Route::get('/','index');
    Route::post('/add','store');
    Route::post('/add_question','store_question');
    Route::get('/delete','destroy');
    Route::post('/update','update');
    Route::get('/show','show');
 
 
});
Route::controller(HomeworkController::class)->prefix('homework')->group(function (){
    
    // Route::get('/','index');
    Route::post('/add','store');
    Route::get('/delete','destroy');
    Route::post('/add_hw_to_users','add_hw_to_users');
 
 
});
Route::controller(PaymentController::class)->prefix('payment')->group(function (){
    
    Route::get('/','index');
    Route::post('/add','store');
    Route::get('/studnet_payment','studnet_payment');
    Route::post('/pay','pay');
 
 
});
Route::controller(SecretaryController::class)->prefix('secretary')->group(function (){
    
    Route::get('/add_users_attendance','add_users_attendance');
    // Route::post('/add','store');
    // Route::get('/show','show');
    // Route::get('/delete','destroy');
    // Route::post('/update','update');
 
});
  
// Route::get('/view-clear', function() {
//     $exitCode = Artisan::call('cache:clear');
//     $exitCode = Artisan::call('view:clear');
//     $exitCode = Artisan::call('config:cache');

//     return '<h1>View cache cleared</h1>';
// });