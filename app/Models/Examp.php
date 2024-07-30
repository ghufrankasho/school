<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\Console\Question\Question;

class Examp extends Model
{
    use HasFactory;
    public $fillable=['name','time','day'];
   
    
    // public function teacher()
    // {
    //     return $this->belongsTo(teacher::class);
    // }
    
    public function type_section()
    {
        return $this->belongsTo(TypeSection::class);
    }
    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}