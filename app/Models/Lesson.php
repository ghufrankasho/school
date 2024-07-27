<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;
    public $fillable=['description','name'];
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
     
    public function programlesson()
    {
      return $this->hasMany(ProgramLesson::class);
    }

    public function type()
    {
      return $this->belongsTo(Type::class);
    }
    
}