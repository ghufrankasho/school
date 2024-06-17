<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;
    public $fillable=['description','name'];
    public function lessons()
    {
        return $this->hasMany(Lesson::class,'subject_id');
    }
}