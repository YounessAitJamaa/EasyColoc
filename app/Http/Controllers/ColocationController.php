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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

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
            $solde = $payParCeMembre - $partIndividuelle;

            $balances[$membre->id] = [
                'nom' => $membre->name,
                'paye' => $payParCeMembre,
                'solde' => $solde,
            ];
        }

        return view('colocations.show', [
            'colocation' => $colocation->load(['membres', 'categories', 'depenses']),
            'totalDepenses' => $totalDepenses,
            'partIndividuelle' => $partIndividuelle,
            'balances' => $balances,
        ]);
    }
}
