<?php

namespace App\Http\Controllers;

 
use Kreait\Firebase\Factory;
use App\Models\User;
use App\Models\Notification;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Kreait\Firebase\Messaging\CloudMessage;

class PushNotificationController extends Controller
{
    public function sendPushNotification(Request $request)
    {
        $validateauser = Validator::make($request->all(),
         [
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string',
            'message' => 'required|string',
            
        ]);
        $notification = Notification::create(array_merge(
            $validateauser->validated()
            
            ));
        // Use dependency injection to get the FirebaseService instance
        $user=User::find($request->user_id);
      
        if($user){
            
            $notification->user()->associate($user);
            $notification->save();
            
         $firebase = (new Factory)
            ->withServiceAccount('C:\Users\Mokas\OneDrive\Desktop\Baraa\school\config\credentials.json');

         
        $messaging = $firebase->createMessaging();
        
        $message = CloudMessage::withTarget('topic', 'global') ;
        
        $message = CloudMessage::fromArray([
            'notification' => [
                'title' => $request->title,
                'body' => $request->message
            ],
            'topic' => 'global'
        ]);

        $messaging->send($message);

        return response()->json(['message' => 'Push notification sent successfully']);
    
    }
}
}