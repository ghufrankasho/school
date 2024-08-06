<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quest extends Model
{
    use HasFactory;
    protected $table = 'questions';
    public $fillable=['option2','option3','option1','name','answer','mark'];
   
    public function examp(){
        return $this->belongsTo(Examp::class);
    }
}