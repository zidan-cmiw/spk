<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AhpSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'period_id',
        'nama_sesi',
        'deskripsi',
        'lambda_max',
        'ci',
        'cr',
        'is_consistent',
        'is_active',
    ];

    protected $casts = [
        'lambda_max' => 'float',
        'ci' => 'float',
        'cr' => 'float',
        'is_consistent' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function period()
    {
        return $this->belongsTo(Period::class);
    }

    public function comparisons()
    {
        return $this->hasMany(AhpComparison::class, 'session_id');
    }

    public function weights()
    {
        return $this->hasMany(CriteriaWeight::class, 'session_id');
    }
}
