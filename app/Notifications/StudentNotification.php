<?php 
namespace App\Notifications;
use Illuminate\Bus\Queueable; 
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Kawankoding\Fcm\FcmMessage; 
class UserNotification extends Notification
 { 
    use Queueable; 
    protected $message;
    protected $title; 
    
    public function __construct($title, $message) {
        
        $this->title = $title;
        $this->message = $message;
    }

    public function via($notifiable)
    {
    return ['fcm'];
    }

    public function toFcm($notifiable)
    {
    return (new FcmMessage)
    ->content([
    'title' => $this->title,
    'body' => $this->message,
    ])
    ->options([
    'priority' => 'high',
    ])
    ->data([
    'extra_data' => 'some_value',
    ]);
    }
    }