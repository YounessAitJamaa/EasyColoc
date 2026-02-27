<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(Request $request): View
    {
        $inviteToken = $request->query('invite');
        $email = '';

        if ($inviteToken) {
            $invitation = Invitation::where('token', $inviteToken)->first();
            if ($invitation && !$invitation->estExpiree() && $invitation->statut === 'en_attente') {
                $email = $invitation->email;
            }
        }

        return view('auth.register', compact('inviteToken', 'email'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if (User::count() === 1) {
            $user->role_global = 'admin';
            $user->save();
        }

        event(new Registered($user));

        Auth::login($user);

        if ($request->filled('invite_token')) {
            $invitation = Invitation::where('token', $request->invite_token)->first();

            if ($invitation && !$invitation->estExpiree() && $invitation->statut == 'en_attente') {
                $invitation->update(['statut' => 'acceptee']);

                $invitation->colocation->membres()->attach($user->id, [
                    'role_dans_colocation' => 'membre',
                    'date_adhesion' => now(),
                ]);
                return redirect()->route('colocations.show', $invitation->colocation_id)
                    ->with('success', 'T\'es dedans now !');
            }
        }

        return redirect(route('dashboard', absolute: false));
    }
}
