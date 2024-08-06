<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\Console\Question\Question;

class Examp extends Model
{
    use HasFactory;
    protected $table = 'examps';
    public $fillable=['name','time','day','duration'];
   
    
    public function users()
    {
        return $this->belongsToMany(User::class,'users_examps') ->withPivot('result', 'rate');
    }
    
    public function type_section()
    {
        return $this->belongsTo(TypeSection::class);
    }
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
    public function quest()
    {
        return $this->hasMany(Quest::class);
    }
}