<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeSection extends Model
{
    use HasFactory;
   public $table='type_sections';
   
    public function program(){
        return $this->hasOne(Program::class);
    }
    public function homeworks(){
        return $this->hasMany(Homework::class,'type_section_id');
    }
}