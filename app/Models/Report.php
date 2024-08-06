<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;
    
    public $fillable=['note','date','name'];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function teacher()
    {
        return $this->belongsTo(Teacher::class,);
    }
    public function examps()
    {
        return $this->hasMany(Examp::class,'report_id');
    }
    public function userSubjects()
    {
        return $this->hasMany(UserSubject::class, 'report_id');
    }
}