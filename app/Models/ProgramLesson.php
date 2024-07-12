<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramLesson extends Model
{
    use HasFactory;
    public $fillable=['time'];
    public function program()
    {
        return $this->belongsTo(Program::class);
    }
    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}