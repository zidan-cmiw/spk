<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parameter extends Model
{
    use HasFactory;

    protected $fillable = [
        'criteria_id',
        'label',
        'batas_min',
        'batas_max',
        'skor',
    ];

    protected $casts = [
        'batas_min' => 'float',
        'batas_max' => 'float',
        'skor' => 'float',
    ];

    public function criterion()
    {
        return $this->belongsTo(Criterion::class, 'criteria_id');
    }
}
