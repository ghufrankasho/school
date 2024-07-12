<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;
    public $timestamps=true;
    public $fillable=['description','name'];
    
    public function type_section(){
        
        return $this->belongsTo(TypeSection::class,'type_section_id','id');
    }
    public function program_lesson()
    {
        return $this->hasMany(ProgramLesson::class);
    }
}