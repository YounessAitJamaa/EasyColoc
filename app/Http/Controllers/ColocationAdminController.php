<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
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
}
