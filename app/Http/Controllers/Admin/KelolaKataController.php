<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\KataPositif;
use App\Models\KataNegatif;

class KelolaKataController extends Controller
{
    public function index()
    {
        $positives = KataPositif::orderBy('word')->paginate(15, ['*'], 'pos_page');
        $negatives = KataNegatif::orderBy('word')->paginate(15, ['*'], 'neg_page');
        
        return view('admin.kata-sentimen.indeks', compact('positives', 'negatives'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'word' => 'required|string|max:100',
            'type' => 'required|in:positive,negative',
        ]);

        $word = strtolower(trim($request->word));

        if ($request->type === 'positive') {
            KataPositif::firstOrCreate(['word' => $word]);
        } else {
            KataNegatif::firstOrCreate(['word' => $word]);
        }

        return redirect()->back()->with('success', "Kata '{$word}' berhasil ditambahkan ke kamus.");
    }

    public function destroy($type, $id)
    {
        if ($type === 'positive') {
            KataPositif::findOrFail($id)->delete();
        } else {
            KataNegatif::findOrFail($id)->delete();
        }

        return redirect()->back()->with('success', 'Kata berhasil dihapus dari kamus.');
    }
}
