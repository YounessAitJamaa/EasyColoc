<?php

namespace App\Http\Controllers;

use App\Models\Depense;
use App\Models\Colocation;
use App\Models\Categorie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepenseController extends Controller
{
    public function index($colocationId)
    {
        $colocation = Colocation::with(['depenses.payeur', 'depenses.categorie'])->findOrFail($colocationId);

        if (!$colocation->membres()->where('utilisateur_id', Auth::id())->exists()) {
            abort(403, 'Pas le droit.');
        }

        $categories = $colocation->categories;

        return view('depenses.index', compact('colocation', 'categories'));
    }

    public function store(Request $request, $colocationId)
    {
        $colocation = Colocation::findOrFail($colocationId);

        if (!$colocation->membres()->where('utilisateur_id', Auth::id())->exists()) {
            abort(403, 'Pas le droit.');
        }

        $request->validate([
            'titre' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0',
            'date_depense' => 'required|date',
            'categorie_id' => 'nullable|exists:categories,id',
        ]);

        $colocation->depenses()->create([
            'titre' => $request->titre,
            'montant' => $request->montant,
            'date_depense' => $request->date_depense,
            'categorie_id' => $request->categorie_id,
            'payeur_id' => Auth::id(),
        ]);

        return redirect()->route('categories.index', $colocationId)
            ->with('success', 'C\'est bon.');
    }

    public function destroy($id)
    {
        $depense = Depense::findOrFail($id);
        $colocationId = $depense->colocation_id;

        if (!$depense->colocation->membres()->where('utilisateur_id', Auth::id())->exists()) {
            abort(403, 'Pas le droit.');
        }

        $depense->delete();

        return redirect()->route('categories.index', $colocationId)
            ->with('success', 'C\'est vire.');
    }
}
