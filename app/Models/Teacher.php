<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;
    public $fillable=['description','email','phone','name','specilty'];
    public function programlesson(){
        
        return $this->hasMany(ProgramLesson::class);
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

}