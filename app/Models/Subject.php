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
    public function examps()
    {
        return $this->hasMany(Examp::class,);
    }
    public function teachers()
    {
        return $this->hasMany(Teacher::class);
    }
    public function users()
    {
        return $this->belongsToMany(User::class,'user_subjects') ->withPivot('written_average', 'number_average', 'report_id');
    }
}