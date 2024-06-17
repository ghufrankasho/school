<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;
    public $fillable=['description','name','day'];
    public function section_type(){
        return $this->belongsTo(TypeSection::class,'section_type_id','id');
    }
    public function program_lesson()
    {
        return $this->hasMany(ProgramLesson::class);
    }
}