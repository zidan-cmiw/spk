<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CriteriaWeight extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'criteria_id',
        'bobot',
    ];

    protected $casts = [
        'bobot' => 'float',
    ];

    public function session()
    {
        return $this->belongsTo(AhpSession::class, 'session_id');
    }

    public function criterion()
    {
        return $this->belongsTo(Criterion::class, 'criteria_id');
    }
}
