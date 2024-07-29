<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;
    
    public $fillable=['attendance_day'];
    
    public function user(){
        return $this->belongsTo(User::class);
        
    }
}