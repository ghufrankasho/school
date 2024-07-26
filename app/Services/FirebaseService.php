<?php
namespace App\Services;

use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Factory;

class FirebaseService
{
protected $messaging;

public function __construct()
{
    
$firebase = (new Factory)
->withServiceAccount(base_path('stellar-6795c-653f927adc7d.json'));
 
$this->messaging = $firebase->createMessaging();
}

public function sendNotification($deviceToken, $title, $body)
{
$message = CloudMessage::withTarget('token', $deviceToken)
->withNotification([
'title' => $title,
'body' => $body,
]);

return $this->messaging->send($message);
}
}