<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSubject extends Model
{
    use HasFactory;
    use HasFactory;

    protected $table = 'user_subjects';

     public $timestamps=false;
    protected $fillable = ['written_average', 'number_average', 'user_id', 'subject_id', 'report_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function report()
    {
        return $this->belongsTo(Report::class);
    }
}