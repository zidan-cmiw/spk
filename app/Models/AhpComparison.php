<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AhpComparison extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'criteria_a_id',
        'criteria_b_id',
        'nilai',
    ];

    protected $casts = [
        'nilai' => 'float',
    ];

    public function session()
    {
        return $this->belongsTo(AhpSession::class, 'session_id');
    }

    public function criteriaA()
    {
        return $this->belongsTo(Criterion::class, 'criteria_a_id');
    }

    public function criteriaB()
    {
        return $this->belongsTo(Criterion::class, 'criteria_b_id');
    }
}
