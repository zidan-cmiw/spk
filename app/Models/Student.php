<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode',
        'nis',
        'nama',
        'kelas',
        'jurusan',
        'jenis_kelamin',
    ];

    public function assessments()
    {
        return $this->hasMany(Assessment::class);
    }

    public function results()
    {
        return $this->hasMany(CalculationResult::class);
    }
}
