<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;
    public $fillable=['description','name'];
    
    public function types(){
        return $this->belongsToMany(Type::class,'type_sections');
    }
}