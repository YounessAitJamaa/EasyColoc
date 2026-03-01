<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Colocation extends Model
{
    protected $fillable = [
        'nom',
        'description',
        'statut',
        'date_annulation',
        'cree_par',
    ];

    protected function casts(): array
    {
        return [
            'date_annulation' => 'datetime',
        ];
    }

    public function createur()
    {
        return $this->belongsTo(User::class, 'cree_par');
    }

    public function adhesions()
    {
        return $this->hasMany(Adhesion::class);
    }

    public function membres()
    {
        return $this->belongsToMany(User::class, 'adhesions', 'colocation_id', 'utilisateur_id')
            ->withPivot(['role_dans_colocation', 'date_adhesion', 'left_at'])
            ->wherePivotNull('left_at')
            ->withTimestamps();
    }

    public function depenses()
    {
        return $this->hasMany(Depense::class);
    }

    public function categories()
    {
        return $this->hasMany(Categorie::class);
    }

    public function invitations()
    {
        return $this->hasMany(Invitation::class);
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }

    public function dettesImputees()
    {
        return $this->hasMany(DetteImputee::class);
    }

    public function calculerSoldePourUtilisateur(int $utilisateurId): float
    {
        $this->loadMissing(['membres', 'depenses', 'paiements', 'dettesImputees']);

        $membres = $this->membres;
        $depenses = $this->depenses;

        $totalDepenses = $depenses->sum('montant');
        $membresActifs = $membres->count();
        $membresRetires = $this->dettesImputees->pluck('membre_retire_id')->unique()->count();
        $nombreMembres = $membresActifs + $membresRetires;

        if ($nombreMembres === 0) {
            return 0;
        }

        $partIndividuelle = $totalDepenses / $nombreMembres;

        $payeParCeMembre = $depenses->where('payeur_id', $utilisateurId)->sum('montant');

        $remboursementDonnes = $this->paiements->where('payeur_id', $utilisateurId)->sum('montant');
        $remboursementRecus = $this->paiements->where('beneficiaire_id', $utilisateurId)->sum('montant');

        $totalReellementPaye = $payeParCeMembre + $remboursementDonnes - $remboursementRecus;

        $solde = $totalReellementPaye - $partIndividuelle;

        $dettesImputeesPayeur = $this->dettesImputees->where('payeur_id', $utilisateurId)->sum('montant');
        $solde = $solde - $dettesImputeesPayeur;

        return $solde;
    }

    public function getDettesDuMembre(int $membreId): array
    {
        $this->loadMissing(['membres', 'depenses', 'paiements']);

        $membres = $this->membres;
        $depenses = $this->depenses;

        $totalDepenses = $depenses->sum('montant');
        $nombreMembres = $membres->count();

        if ($nombreMembres === 0) {
            return [];
        }

        $partIndividuelle = $totalDepenses / $nombreMembres;

        $balances = [];

        foreach ($membres as $m) {
            $paye = $depenses->where('payeur_id', $m->id)->sum('montant');
            $donnes = $this->paiements->where('payeur_id', $m->id)->sum('montant');
            $recus = $this->paiements->where('beneficiaire_id', $m->id)->sum('montant');
            $solde = $paye + $donnes - $recus - $partIndividuelle;
            $balances[$m->id] = ['id' => $m->id, 'solde' => $solde];
        }

        $debiteurs = [];
        $creanciers = [];
        
        foreach ($balances as $b) {
            if ($b['solde'] < -0.01) {
                $debiteurs[] = ['id' => $b['id'], 'montant' => abs($b['solde'])];
            } elseif ($b['solde'] > 0.01) {
                $creanciers[] = ['id' => $b['id'], 'montant' => $b['solde']];
            }
        }

        $suggestions = [];
        $d = 0;
        $c = 0;

        while ($d < count($debiteurs) && $c < count($creanciers)) {
            $montant = min($debiteurs[$d]['montant'], $creanciers[$c]['montant']);
            if ($montant > 0) {
                $suggestions[] = [
                    'payeur_id' => $debiteurs[$d]['id'],
                    'receveur_id' => $creanciers[$c]['id'],
                    'montant' => $montant,
                ];
                $debiteurs[$d]['montant'] -= $montant;
                $creanciers[$c]['montant'] -= $montant;
            }
            if ($debiteurs[$d]['montant'] < 0.01) {
                $d++;
            }
            if ($creanciers[$c]['montant'] < 0.01) {
                $c++;
            }
        }

        $dettes = [];
        foreach ($suggestions as $s) {
            if ($s['payeur_id'] === $membreId) {
                $dettes[] = ['receveur_id' => $s['receveur_id'], 'montant' => $s['montant']];
            }
        }

        return $dettes;
    }

    public function calculerBalancesEtSuggestions(): array
    {
        $this->loadMissing(['membres', 'depenses', 'paiements', 'dettesImputees']);

        $membres = $this->membres;
        $depenses = $this->depenses;

        $totalDepenses = $depenses->sum('montant');
        $membresActifs = $membres->count();
        $membresRetires = $this->dettesImputees->pluck('membre_retire_id')->unique()->count();
        $nombreMembres = $membresActifs + $membresRetires;
        $partIndividuelle = $nombreMembres > 0 ? $totalDepenses / $nombreMembres : 0;

        $balances = [];
        foreach ($membres as $membre) {
            $payParCeMembre = $depenses->where('payeur_id', $membre->id)->sum('montant');
            $remboursementDonnes = $this->paiements->where('payeur_id', $membre->id)->sum('montant');
            $remboursementRecus = $this->paiements->where('beneficiaire_id', $membre->id)->sum('montant');

            $totalReellementPaye = $payParCeMembre + $remboursementDonnes - $remboursementRecus;
            $solde = $totalReellementPaye - $partIndividuelle;

            $dettesImputeesPayeur = $this->dettesImputees->where('payeur_id', $membre->id)->sum('montant');
            $solde = $solde - $dettesImputeesPayeur;

            $balances[$membre->id] = [
                'id' => $membre->id,
                'nom' => $membre->name,
                'paye' => $payParCeMembre,
                'solde' => $solde,
            ];
        }

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
        $d = 0;
        $c = 0;

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

            if ($payeur['montant'] < 0.01) {
                $d++;
            }
            if ($receveur['montant'] < 0.01) {
                $c++;
            }
        }

        return [
            'totalDepenses' => $totalDepenses,
            'partIndividuelle' => $partIndividuelle,
            'balances' => $balances,
            'suggestions' => $suggestions,
        ];
    }
}
