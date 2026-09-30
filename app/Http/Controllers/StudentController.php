<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Assessment;
use App\Models\Period;
use App\Models\Criterion;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::with('assessments.criterion')->orderBy('kode')->get();
        $nextKode = 'A' . ($students->count() + 1);

        return view('students.index', compact('students', 'nextKode'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|max:10|unique:students,kode',
            'nama' => 'required|string|max:150',
            'nis' => 'nullable|string|max:30',
            'kelas' => 'required|string|max:50',
            'jurusan' => 'required|string|max:100',
            'jenis_kelamin' => 'required|in:L,P',
        ]);

        $student = Student::create([
            'kode' => strtoupper(trim($request->kode)),
            'nama' => trim($request->nama),
            'nis' => $request->nis,
            'kelas' => $request->kelas,
            'jurusan' => $request->jurusan,
            'jenis_kelamin' => $request->jenis_kelamin,
        ]);

        // Automatically initialize default assessments for this student across active period criteria
        $period = Period::where('is_active', true)->first();
        if ($period) {
            $criteria = Criterion::all();
            foreach ($criteria as $crit) {
                Assessment::firstOrCreate(
                    [
                        'student_id' => $student->id,
                        'criteria_id' => $crit->id,
                        'period_id' => $period->id,
                    ],
                    [
                        'nilai_asli' => '20',
                        'skor_parameter' => 20, // default minimum parameter score
                    ]
                );
            }
        }

        return redirect()->route('students.index')->with('success', 'Alternatif siswa ' . $student->nama . ' (' . $student->kode . ') berhasil ditambahkan. Silakan lengkapi nilai pada menu Penilaian.');
    }

    public function update(Request $request, Student $student)
    {
        $request->validate([
            'nama' => 'required|string|max:150',
            'nis' => 'nullable|string|max:30',
            'kelas' => 'required|string|max:50',
            'jurusan' => 'required|string|max:100',
            'jenis_kelamin' => 'required|in:L,P',
        ]);

        $student->update([
            'nama' => trim($request->nama),
            'nis' => $request->nis,
            'kelas' => $request->kelas,
            'jurusan' => $request->jurusan,
            'jenis_kelamin' => $request->jenis_kelamin,
        ]);

        return redirect()->route('students.index')->with('success', 'Data siswa ' . $student->kode . ' berhasil diperbarui.');
    }

    public function destroy(Student $student)
    {
        $nama = $student->nama;
        $student->delete();

        return redirect()->route('students.index')->with('success', 'Data siswa ' . $nama . ' berhasil dihapus.');
    }
}
