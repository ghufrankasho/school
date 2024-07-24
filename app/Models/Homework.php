<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Homework extends Model
{
    use HasFactory;
    public $fillable=['title','end_date'];
    public function users(){
        
        return $this->hasMany(User::class);
    }
    public function teacher(){
        
        return $this->belongsTo(Teacher::class);
    }
    public function type_section(){
        
        return $this->belongsTo(TypeSection::class,'type_section_id');
    }
}