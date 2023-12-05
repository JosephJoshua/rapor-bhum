<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'unit_id', 'teacher_user_id'];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_user_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
