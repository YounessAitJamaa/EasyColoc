<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\Adhesion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ColocationController extends Controller
{

    public function create()
    {
        return view('colocations.create');
    }

    public function leave($id)
    {
        $colocation = Colocation::findOrFail($id);

        if ($colocation->statut === 'annulee') {
            return redirect()->route('dashboard')->with('error', 'Cette colocation est annulee.');
        }

        $adhesion = Adhesion::where('colocation_id', $colocation->id)
            ->where('utilisateur_id', Auth::id())
            ->whereNull('left_at')
            ->first();

        if (!$adhesion) {
            abort(403, 'Tu ne fais pas partie de cette colocation.');
        }

        if ($adhesion->role_dans_colocation === 'owner') {
            return redirect()->route('colocations.show', $colocation->id)
                ->with('error', 'Le owner ne peut pas quitter la colocation.');
        }

        $adhesion->left_at = now();
        $adhesion->save();

        return redirect()->route('dashboard')->with('success', 'Tu as quitte la colocation.');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $user = Auth::user();
        if ($user->role_global !== 'admin' && $user->hasActiveColocation()) {
            return back()->withInput()->with('error', 'Tu as deja une colocation active.');
        }

        try {
            DB::beginTransaction();

            $colocation = Colocation::create([
                'nom' => $validated['nom'],
                'description' => $validated['description'],
                'cree_par' => Auth::id(),
                'statut' => 'active',
            ]);

            Adhesion::create([
                'utilisateur_id' => Auth::id(),
                'colocation_id' => $colocation->id,
                'role_dans_colocation' => 'owner',
                'date_adhesion' => now(),
            ]);

            DB::commit();

            return redirect()->route('colocations.admin', $colocation->id)
                ->with('success', 'C\'est bon.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Probleme lors de la creation.');
        }
    }

    public function show($id)
    {
        $colocation = Colocation::findOrFail($id);

        if ($colocation->statut === 'annulee') {
            return redirect()->route('dashboard')->with('error', 'Cette colocation est annulee.');
        }

        if (!$colocation->membres()->where('utilisateur_id', Auth::id())->exists()) {
            abort(403, 'C\'est pas pour toi ca.');
        }

        $membres = $colocation->membres;
        $depenses = $colocation->depenses;

        $totalDepenses = $depenses->sum('montant');
        $nombreMembres = $membres->count();
        $partIndividuelle = $nombreMembres > 0 ? $totalDepenses / $nombreMembres : 0;

        $balances = [];

        foreach ($membres as $membre) {
            $payParCeMembre = $depenses->where('payeur_id', $membre->id)->sum('montant');

            $remboursementDonnes = $colocation->paiements->where('payeur_id', $membre->id)->sum('montant');
            $remboursementRecus = $colocation->paiements->where('beneficiaire_id', $membre->id)->sum('montant');

            $totalReellementPaye = $payParCeMembre + $remboursementDonnes - $remboursementRecus;
 
            $solde = $totalReellementPaye - $partIndividuelle;

            $balances[$membre->id] = [
                'id' => $membre->id,
                'nom' => $membre->name,
                'paye' => $payParCeMembre,
                'solde' => $solde,
            ];
        }
        
        // --- Logique "Qui doit a qui" ---
        $debiteurs = [];
        $creanciers = [];

        foreach ($balances as $b) {
            if ($b['solde'] < -0.01) {
                $debiteurs[] = ['id' => $b['id'], 'nom' => $b['nom'], 'montant' => abs($b['solde'])];
            } elseif ($b['solde'] > 0.01) {
                $creanciers[] = ['id' => $b['id'], 'nom' => $b['nom'], 'montant' => $b['solde']];
            }
        }

        $suggestions = [];
        $d = 0; // index debiteurs
        $c = 0; // index creanciers

        while ($d < count($debiteurs) && $c < count($creanciers)) {
            $payeur = &$debiteurs[$d];
            $receveur = &$creanciers[$c];

            $montantAPayer = min($payeur['montant'], $receveur['montant']);

            if ($montantAPayer > 0) {
                $suggestions[] = [
                    'payeur_id' => $payeur['id'],
                    'payeur_nom' => $payeur['nom'],
                    'receveur_id' => $receveur['id'],
                    'receveur_nom' => $receveur['nom'],
                    'montant' => $montantAPayer,
                ];
            }

            $payeur['montant'] -= $montantAPayer;
            $receveur['montant'] -= $montantAPayer;

            if ($payeur['montant'] < 0.01)
                $d++;
            if ($receveur['montant'] < 0.01)
                $c++;
        }

        return view('colocations.show', [
            'colocation' => $colocation->load(['membres', 'categories', 'depenses.payeur', 'depenses.categorie', 'paiements']),
            'totalDepenses' => $totalDepenses,
            'partIndividuelle' => $partIndividuelle,
            'balances' => $balances,
            'suggestions' => $suggestions,
        ]);
    }
}
