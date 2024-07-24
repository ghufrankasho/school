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
   
    public $fillable=['address','class_name','phone','name'];
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
        return $this->hasMany(Homework::class);
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