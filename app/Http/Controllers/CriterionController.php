<?php

namespace App\Http\Controllers;

use App\Models\Criterion;
use App\Models\Parameter;
use Illuminate\Http\Request;

class CriterionController extends Controller
{
    public function index()
    {
        $criteria = Criterion::with('parameters')->orderBy('urutan')->get();
        return view('criteria.index', compact('criteria'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|max:10|unique:criteria,kode',
            'nama' => 'required|string|max:100',
            'tipe' => 'required|in:benefit,cost',
            'bobot_default' => 'nullable|numeric|min:0|max:1',
            'deskripsi' => 'nullable|string',
        ]);

        $maxUrutan = Criterion::max('urutan') ?? 0;

        Criterion::create([
            'kode' => strtoupper($request->kode),
            'nama' => $request->nama,
            'tipe' => $request->tipe,
            'bobot_default' => $request->bobot_default ?? 0.10,
            'urutan' => $maxUrutan + 1,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->route('criteria.index')->with('success', 'Kriteria berhasil ditambahkan.');
    }

    public function update(Request $request, Criterion $criterion)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'tipe' => 'required|in:benefit,cost',
            'bobot_default' => 'nullable|numeric|min:0|max:1',
            'deskripsi' => 'nullable|string',
        ]);

        $criterion->update([
            'nama' => $request->nama,
            'tipe' => $request->tipe,
            'bobot_default' => $request->bobot_default ?? $criterion->bobot_default,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->route('criteria.index')->with('success', 'Kriteria ' . $criterion->kode . ' berhasil diperbarui.');
    }

    public function destroy(Criterion $criterion)
    {
        $kode = $criterion->kode;
        $criterion->delete();

        return redirect()->route('criteria.index')->with('success', 'Kriteria ' . $kode . ' berhasil dihapus.');
    }

    public function storeParameter(Request $request, Criterion $criterion)
    {
        $request->validate([
            'label' => 'required|string|max:150',
            'skor' => 'required|numeric|min:0|max:100',
            'batas_min' => 'nullable|numeric',
            'batas_max' => 'nullable|numeric',
        ]);

        Parameter::create([
            'criteria_id' => $criterion->id,
            'label' => $request->label,
            'skor' => $request->skor,
            'batas_min' => $request->batas_min,
            'batas_max' => $request->batas_max,
        ]);

        return redirect()->route('criteria.index')->with('success', 'Parameter untuk ' . $criterion->kode . ' berhasil ditambahkan.');
    }

    public function destroyParameter(Parameter $parameter)
    {
        $criteriaKode = $parameter->criterion->kode ?? '';
        $parameter->delete();

        return redirect()->route('criteria.index')->with('success', 'Parameter ' . $criteriaKode . ' berhasil dihapus.');
    }
}
