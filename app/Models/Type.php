<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    use HasFactory;
    
    public $fillable=['description','total_amount','name'];
    
    public function sections(){
        
        return $this->belongsToMany(Section::class,'type_sections');
    }
    public function lessons(){
        
        return $this->hasMany(Lesson::class);
    }
}