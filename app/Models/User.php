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
   
    public $fillable=['description','email','phone','name'];
    public function account()
    {
        return $this->belongsTo(Account::class);
    }
    public function student_chats()
    {
        return $this->hasMany(Chat::class,'student_id','id');
    }
    public function teacher_chats()
    {
        return $this->hasMany(Chat::class,'teacher_id','id');
    }
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
    public function payment()
    {
        return $this->hasMany(Payment::class);
    }
    public function reports()
    {
        return $this->hasMany(Report::class,'student_id','id');
    }
    public function users_examps()
    {
        return $this->hasMany(UserExamp::class);
    }
     
}