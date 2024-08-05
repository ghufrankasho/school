<?php

namespace App\Http\Controllers;
use App\Models\Payment;
use App\Models\TypeSection;
use App\Models\User;
use Stripe\Stripe;
use Stripe\Charge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
class PaymentController extends Controller
{
    public function index(){
        $payments=payment::latest()->get();
        if($payments){
            return response()->json(
                [
                      'status' => true,
                      'message' => 'تم الحصول على البيانات بنجاح', 
                      'data'=> $payments,
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
            
              
            $validateapayment = Validator::make($request->all(), 
            [
               
               'title' => 'nullable|string',
               'date' => 'date|required',
               'type'=>'required|in:0,1,2,3',   //['activity,0','exapm,1','monthly_installment,2','trip,3']
               'type_section_id' => 'integer|required|exists:type_sections,id',
               'amount' => 'integer|min:0|max:1000000|required',
            ]);
      
        
           

            if($validateapayment->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'خطأ في التحقق',
                    'errors' => $validateapayment->errors()
                ], 422);
            }

           
            
            $payment = payment::create(array_merge(
                $validateapayment->validated()
                
                ));
            
            $type_section=TypeSection::find($request->type_section_id);
            $users=$type_section->users()->get();
            // return count($users);
            foreach($users as $user){
                $payment->users()->attach($user);
               
            }
           
            
       if ($payment){
           
            return response()->json(
                [
                    'status' => true,
                    'message' => 'تم أضافة البيانات  بنجاح', 
                    'data'=> $payment,
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
    public function studnet_payment(Request $request){
        try {  
            
            $validate = Validator::make( $request->all(),
                ['user_id'=>'required|integer|exists:users,id']);
            if($validate->fails()){
            return response()->json([
               'status' => false,
               'message' => 'خطأ في التحقق',
               'errors' => $validate->errors()
            ], 422);}
          
            $user=User::find($request->user_id);
            $payments=$user->payments;
           
        //   if($user){ 
                 
        // foreach($payments as $payment){
        //   $d= $payment->pivot;
        //   return [$d->is_paid];
          
        // }
                return response()->json(
                    [
                         'status' => true,
                         'message' =>' تم الحصول على البيانات بنجاح', 
                         'data'=> $payments,
                     ], 200);
                  
             
             
     
             return response()->json(    
                 [  'status' => false,
                    'message' => 'حدث خطأ جلب البيانات',
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
    public function pay(Request $request)
    {
        
        $validateapayment = Validator::make($request->all(), 
        [
           
        //    'title' => 'nullable|string',
        //    'date' => 'date|required',
        //    'type'=>'required|in:0,1,2,3',   //['activity,0','exapm,1','monthly_installment,2','trip,3']
           'user_id' => 'integer|required|exists:users,id',
           'payment_id' => 'integer|required|exists:payments,id',
           'amount' => 'integer|min:0|max:1000000|required',
        ]);
        
        if($validateapayment->fails()){
            return response()->json([
                'status' => false,
                'message' => 'خطأ في التحقق',
                'errors' => $validateapayment->errors()
            ], 422);
        }
        $user=User::find($request->user_id);
        if($user){
            $user->payments()->updateExistingPivot($request->payment_id,['is_paid'=>1]);
        }
       

       

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $charge = Charge::create([
                'amount' => $request->amount, // Amount in cents
                'currency' => 'usd',
                'source' => $request->stripeToken,
                'description' => 'Test payment from Laravel Stripe Integration'
            ]);

            if($charge){
                
            }
            return response()->json(['message' => 'Payment successful!'], 200);
        } catch (\Exception $ex) {
            return response()->json(['error' => 'Error! ' . $ex->getMessage()], 500);
        }
    }
}