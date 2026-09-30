<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'criteria_id',
        'period_id',
        'nilai_asli',
        'skor_parameter',
    ];

    protected $casts = [
        'skor_parameter' => 'float',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function criterion()
    {
        return $this->belongsTo(Criterion::class, 'criteria_id');
    }

    public function period()
    {
        return $this->belongsTo(Period::class);
    }
}
