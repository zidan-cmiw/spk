<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Criterion extends Model
{
    use HasFactory;

    protected $table = 'criteria';

    protected $fillable = [
        'kode',
        'nama',
        'tipe',
        'bobot_default',
        'urutan',
        'deskripsi',
    ];

    protected $casts = [
        'bobot_default' => 'float',
        'urutan' => 'integer',
    ];

    public function parameters()
    {
        return $this->hasMany(Parameter::class, 'criteria_id')->orderBy('skor', 'desc');
    }

    public function criteriaWeights()
    {
        return $this->hasMany(CriteriaWeight::class, 'criteria_id');
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class, 'criteria_id');
    }
}
