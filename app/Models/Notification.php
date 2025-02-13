<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    public $timestamps=true;
    public $fillable=['title','message'];
    
    public function user(){
        
        return $this->belongsTo(User::class);
    }
    public function teacher(){
        
        return $this->belongsTo(Teacher::class);
    }

}