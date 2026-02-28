<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\Adhesion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ColocationAdminController extends Controller
{
    public function index($id)
    {
        $colocation = Colocation::findOrFail($id);

        $isOwner = $colocation->membres()
            ->where('utilisateur_id', Auth::id())
            ->wherePivot('role_dans_colocation', 'owner')
            ->exists();

        if (!$isOwner) {
            abort(403, 'Tu n\'as pas le droit d\'aller ici.');
        }

        return view('colocations.admin', compact('colocation'));
    }

    public function update(Request $request, $id)
    {
        $colocation = Colocation::findOrFail($id);

        if ($colocation->statut === 'annulee') {
            return redirect()->route('dashboard')->with('error', 'Cette colocation est annulee.');
        }

        $isOwner = $colocation->membres()
            ->where('utilisateur_id', Auth::id())
            ->wherePivot('role_dans_colocation', 'owner')
            ->exists();

        if (!$isOwner) {
            abort(403, 'Tu peux pas changer ca.');
        }

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $colocation->update($validated);

        return redirect()->route('colocations.admin', $colocation->id)
            ->with('success', 'C\'est bon, c\'est enregistre.');
    }

    public function destroy($id)
    {
        $colocation = Colocation::findOrFail($id);

        $isOwner = $colocation->membres()
            ->where('utilisateur_id', Auth::id())
            ->wherePivot('role_dans_colocation', 'owner')
            ->exists();

        if (!$isOwner) {
            abort(403, 'Tu peux pas supprimer ca.');
        }

        $colocation->delete();

        return redirect()->route('dashboard')
            ->with('success', 'C\'est supprime.');
    }

    public function cancel($id)
    {
        $colocation = Colocation::findOrFail($id);

        $isOwner = $colocation->membres()
            ->where('utilisateur_id', Auth::id())
            ->wherePivot('role_dans_colocation', 'owner')
            ->exists();

        if (!$isOwner) {
            abort(403, 'Tu peux pas annuler ca.');
        }

        if ($colocation->statut === 'annulee') {
            return redirect()->route('colocations.admin', $colocation->id)
                ->with('error', 'La colocation est deja annulee.');
        }

        $colocation->statut = 'annulee';
        $colocation->date_annulation = now();
        $colocation->save();

        return redirect()->route('colocations.admin', $colocation->id)
            ->with('success', 'La colocation est annulee.');
    }

    public function removeMember($colocationId, $userId)
    {
        $colocation = Colocation::findOrFail($colocationId);

        if ($colocation->statut === 'annulee') {
            return redirect()->route('dashboard')->with('error', 'Cette colocation est annulee.');
        }

        $isOwner = $colocation->membres()
            ->where('utilisateur_id', Auth::id())
            ->wherePivot('role_dans_colocation', 'owner')
            ->exists();

        if (!$isOwner) {
            abort(403, 'Tu peux pas retirer ce membre.');
        }

        if ((int) $userId === Auth::id()) {
            return redirect()->route('colocations.show', $colocation->id)
                ->with('error', 'Utilise le bouton quitter pour toi.');
        }

        $adhesion = Adhesion::where('colocation_id', $colocation->id)
            ->where('utilisateur_id', $userId)
            ->whereNull('left_at')
            ->first();

        if (!$adhesion) {
            return redirect()->route('colocations.show', $colocation->id)
                ->with('error', 'Ce membre n\'est pas actif dans cette colocation.');
        }
        
        if ($adhesion->role_dans_colocation === 'owner') {
            return redirect()->route('colocations.show', $colocation->id)
                ->with('error', 'Tu ne peux pas retirer le owner.');
        }

        $adhesion->left_at = now();
        $adhesion->save();

        return redirect()->route('colocations.show', $colocation->id)
            ->with('success', 'Membre retire de la colocation.');
    }
}
