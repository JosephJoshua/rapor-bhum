<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubIndicator extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'indicator_id'];

    public function indicator()
    {
        return $this->belongsTo(Indicator::class);
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_sub_indicators');
    }
}
