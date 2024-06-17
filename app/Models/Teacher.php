<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;
    public function programlesson(){
        return $this->hasOne(ProgramLesson::class);
    }
    public function account()
    {
        return $this->belongsTo(Account::class);
    }
    public function examps()
    {
        return $this->hasMany(Examp::class);
    }

}