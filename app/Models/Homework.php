<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Homework extends Model
{
    use HasFactory;
    public $table='homework';
    
    public $fillable=['text','end_date'];
    
    public function users(){
        
        return $this->belongsToMany(User::class,'user_homework')->withPivot('answer');
    }
    public function teacher(){
        
        return $this->belongsTo(Teacher::class);
    }
    public function type_section(){
        
        return $this->belongsTo(TypeSection::class,'type_section_id');
    }
}