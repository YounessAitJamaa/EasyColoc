<?php

namespace App\Http\Controllers;

use App\Mail\InvitationMail;
use App\Models\Adhesion;
use App\Models\Colocation;
use App\Models\Invitation;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class InvitationController extends Controller
{

    public function create($colocationId)
    {
        $colocation = Colocation::findOrFail($colocationId);

        if (!$colocation->membres()->where('utilisateur_id', Auth::id())->exists()) {
            abort(403, 'Pas le droit.');
        }

        return view('invitations.create', compact('colocation'));
    }

    public function store(Request $request, $colocationId)
    {
        $colocation = Colocation::findOrFail($colocationId);

        if (!$colocation->membres()->where('utilisateur_id', Auth::id())->exists()) {
            abort(403, 'Pas le droit.');
        }

        $request->validate([
            'email' => 'required|email',
        ]);

        $invitation = Invitation::create([
            'colocation_id' => $colocation->id,
            'email' => $request->email,
            'token' => Invitation::genererToken(),
            'statut' => 'en_attente',
            'expire_le' => now()->addDays(1),
        ]);

        Mail::to($request->email)->send(new InvitationMail($invitation));

        return redirect()->route('colocations.show', $colocation->id)
            ->with('success', 'C\'est envoye !');
    }

    public function show($token)
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();

        if ($invitation->estExpiree()) {
            return redirect()->route('dashboard')->with('error', 'L\'invitation est expiree.');
        }

        if ($invitation->statut !== 'en_attente') {
            return redirect()->route('dashboard')->with('error', 'L\'invitation n\'est plus bonne.');
        }

        return view('invitations.show', compact('invitation'));
    }


    public function accept($token)
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();

        if ($invitation->estExpiree() || $invitation->statut !== 'en_attente') {
            return redirect()->route('dashboard')->with('error', 'Invitation pas bonne.');
        }

        $user = Auth::user();

        $invitation->update(['statut' => 'acceptee']);

        if ($invitation->colocation->membres()->where('utilisateur_id', $user->id)->exists()) {
            return redirect()->route('colocations.show', $invitation->colocation_id)
                ->with('success', 'T\'es deja dedans.');
        }

        $invitation->colocation->membres()->attach($user->id, [
            'role_dans_colocation' => 'membre',
            'date_adhesion' => now(),
        ]);

        return redirect()->route('colocations.show', $invitation->colocation_id)
            ->with('success', 'T\'es dedans now !');
    }

    public function refuse($token)
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();

        if ($invitation->statut === 'en_attente') {
            $invitation->update(['statut' => 'refusee']);
            return redirect()->route('dashboard')->with('success', 'Ok, refuse.');
        }

        return redirect()->route('dashboard')->with('error', 'On peut plus refuser.');
    }

}
