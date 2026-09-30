<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalculationResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'period_id',
        'session_id',
        'student_id',
        'mode_bobot',
        'utility_scores',
        'nilai_akhir',
        'ranking',
    ];

    protected $casts = [
        'utility_scores' => 'array',
        'nilai_akhir' => 'float',
        'ranking' => 'integer',
    ];

    public function period()
    {
        return $this->belongsTo(Period::class);
    }

    public function session()
    {
        return $this->belongsTo(AhpSession::class, 'session_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
