<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\CanResetPassword;
use App\Notifications\ResetPasswordNotification as note; // Add this line to import the notification class

class Account extends Authenticatable implements CanResetPassword,JWTSubject
{
    use Notifiable;
    use HasFactory;
    protected $fillable = [
         'email', 'password','name','type'
    ];
   /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    
     
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function user()
    {
        return $this->hasOne(User::class);
    }
    public function teacher()
    {
        return $this->hasOne(Teacher::class); 
    }
}