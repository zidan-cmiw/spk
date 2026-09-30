<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Period extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'semester',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function ahpSessions()
    {
        return $this->hasMany(AhpSession::class);
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class);
    }

    public function results()
    {
        return $this->hasMany(CalculationResult::class);
    }
}
