<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    public $fillable=['amount','type','title','date'];
   
    public function users(){
        return $this->belongsToMany(User::class,'user_payments');
    }
   
}