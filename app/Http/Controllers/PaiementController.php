<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\DetteImputee;
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

        $colocation = Colocation::findOrFail($validated['colocation_id']);
        if ($colocation->statut === 'annulee') {
            return back()->with('error', 'Cette colocation est annulee.');
        }

        $di = DetteImputee::where('colocation_id', $validated['colocation_id'])
            ->where('payeur_id', $validated['payeur_id'])
            ->where('beneficiaire_id', $validated['beneficiaire_id'])
            ->first();

        if ($di) {
            $reste = (float) $di->montant - (float) $validated['montant'];
            if ($reste <= 0) {
                $di->delete();
            } else {
                $di->update(['montant' => $reste]);
            }

            return back()->with('success', 'Dette imputee reduite !');
        }

        Paiement::create([
            'colocation_id' => $validated['colocation_id'],
            'payeur_id' => $validated['payeur_id'],
            'beneficiaire_id' => $validated['beneficiaire_id'],
            'montant' => $validated['montant'],
        ]);

        return back()->with('success', 'Remboursement enregistre !');
    }
}
