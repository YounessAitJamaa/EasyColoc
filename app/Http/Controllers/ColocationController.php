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

        $solde = $colocation->calculerSoldePourUtilisateur(Auth::id());
        
        $user = Auth::user();

        if ($solde < -0.01) {
            $user->decrement('score_reputation');
        } else {
            $user->increment('score_reputation');
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

    public function show(Request $request, $id)
    {
        $colocation = Colocation::findOrFail($id);

        if ($colocation->statut === 'annulee') {
            return redirect()->route('dashboard')->with('error', 'Cette colocation est annulee.');
        }

        $adhesion = Adhesion::where('colocation_id', $colocation->id)
            ->where('utilisateur_id', Auth::id())
            ->first();

        if (!$adhesion) {
            abort(403, 'C\'est pas pour toi ca.');
        }

        $estAncienMembre = $adhesion->left_at !== null;

        $resultats = $colocation->calculerBalancesEtSuggestions();
        $totalDepenses = $resultats['totalDepenses'];
        $partIndividuelle = $resultats['partIndividuelle'];
        $balances = $resultats['balances'];
        $suggestions = $resultats['suggestions'];

        $colocation->load(['membres', 'categories', 'depenses.payeur', 'depenses.categorie', 'paiements']);

        $moisFiltre = $request->input('mois');
        $depenses = $colocation->depenses;
        if ($moisFiltre) {
            $depenses = $depenses->filter(function ($d) use ($moisFiltre) {
                return $d->date_depense->format('Y-m') === $moisFiltre;
            });
        }

        $statsParCategorie = [];
        foreach ($colocation->depenses as $d) {
            $nom = $d->categorie ? $d->categorie->nom : 'Sans categorie';
            if (!isset($statsParCategorie[$nom])) {
                $statsParCategorie[$nom] = 0;
            }
            $statsParCategorie[$nom] += $d->montant;
        }

        $statsParMois = [];
        foreach ($colocation->depenses as $d) {
            $mois = $d->date_depense->format('Y-m');
            if (!isset($statsParMois[$mois])) {
                $statsParMois[$mois] = 0;
            }
            $statsParMois[$mois] += $d->montant;
        }
        krsort($statsParMois);

        $listeMois = $colocation->depenses->pluck('date_depense')->map(fn ($d) => $d->format('Y-m'))->unique()->sortDesc()->values();

        return view('colocations.show', [
            'colocation' => $colocation,
            'estAncienMembre' => $estAncienMembre,
            'depensesFiltrees' => $depenses,
            'totalDepenses' => $totalDepenses,
            'partIndividuelle' => $partIndividuelle,
            'balances' => $balances,
            'suggestions' => $suggestions,
            'moisFiltre' => $moisFiltre,
            'statsParCategorie' => $statsParCategorie,
            'statsParMois' => $statsParMois,
            'listeMois' => $listeMois,
        ]);
    }
}
