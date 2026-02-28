<?php

namespace App\Http\Controllers;

use App\Models\Paiement;
use Illuminate\Http\Request;

class PaiementController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'colocation_id' => 'required|exists:colocations,id',
            'payeur_id' => 'required|exists:users,id',
            'beneficiaire_id' => 'required|exists:users,id',
            'montant' => 'required|numeric|min:0.01',
        ]);

        Paiement::create([
            'colocation_id' => $validated['colocation_id'],
            'payeur_id' => $validated['payeur_id'],
            'beneficiaire_id' => $validated['beneficiaire_id'],
            'montant' => $validated['montant'],
        ]);

        return back()->with('success', 'Remboursement enregistre !');
    }
}
