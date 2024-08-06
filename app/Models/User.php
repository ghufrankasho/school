<?php

namespace App\Models;

use Examps;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
     
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
   
    public $fillable=['address','class_name','phone','fcm_token'];
    public function account()
    {
        return $this->belongsTo(Account::class);
    }
    public function type_section()
    {
        return $this->belongsTo(TypeSection::class);
    }
    public function  chats()
    {
        return $this->hasMany(Chat::class);
    }
    
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
    public function homework()
    {
        return $this->belongsToMany(Homework::class,'user_homework')->withPivot('answer');
    }
    public function payments()
    {
        return $this->belongsToMany(Payment::class,'user_payments');
    }
    public function reports()
    {
        return $this->hasMany(Report::class,'student_id','id');
    }
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
    public function  examps()
    {
        return $this->belongsToMany(Examp::class,'users_examps',);
    }
     
}