<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserExamp extends Model
{
    use HasFactory;
    public $fillable=['result'];
    
    public function users()
    {
        return $this->belongsTo(User::class);
    }
    public function examps()
    {
        return $this->belongsTo(Examp::class);
    }
}