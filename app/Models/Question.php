<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;
    public $fillable=['option2','option3','option1','name','answer'];
   
    public function examp(){
        return $this->belongsTo(Examp::class);
    }
}