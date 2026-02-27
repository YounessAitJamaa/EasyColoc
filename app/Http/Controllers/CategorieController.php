<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use App\Models\Colocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategorieController extends Controller
{
    public function index($colocationId)
    {
        $colocation = Colocation::findOrFail($colocationId);

        if (!$colocation->membres()->where('utilisateur_id', Auth::id())->exists()) {
            abort(403);
        }

        $categories = $colocation->categories;

        return view('categories.index', compact('colocation', 'categories'));
    }

    public function store(Request $request, $colocationId)
    {
        $colocation = Colocation::findOrFail($colocationId);

        if (!$colocation->membres()->where('utilisateur_id', Auth::id())->exists()) {
            abort(403);
        }

        $request->validate([
            'nom' => 'required|string|max:255',
        ]);

        $colocation->categories()->create([
            'nom' => $request->nom,
        ]);

        return redirect()->route('categories.index', $colocationId)
            ->with('success', 'C\'est bon, c\'est ajoute.');
    }

    public function destroy($id)
    {
        $categorie = Categorie::findOrFail($id);
        $colocationId = $categorie->colocation_id;

        if (!$categorie->colocation->membres()->where('utilisateur_id', Auth::id())->exists()) {
            abort(403);
        }

        $categorie->delete();

        return redirect()->route('categories.index', $colocationId)
            ->with('success', 'C\'est bon, c\'est vire.');
    }
}
