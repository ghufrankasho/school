<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;
    
    public $fillable=['description','phone','name'];
    public $table='teachers';
    public function programlesson(){
        
        return $this->hasMany(ProgramLesson::class);
    }
    public function homeworks(){
        
        return $this->hasMany(Homework::class,'teacher_id');
    }
    public function account()
    {
        return $this->belongsTo(Account::class);
    }
    // public function examps()
    // {
    //     return $this->hasMany(Examp::class);
    // }
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

}