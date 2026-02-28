<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Colocation;
use App\Models\Depense;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $stats = [
            'total_utilisateurs' => User::count(),
            'total_colocations' => Colocation::count(),
            'total_depenses' => Depense::sum('montant'),
        ];

        $users = User::orderBy('name');

        if ($request->filled('q')) {
            $q = $request->input('q');
            $users = $users->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        $users = $users->get();

        return view('admin.dashboard', compact('stats', 'users'));
    }

    public function ban(User $user)
    {
        if ($user->role_global === 'admin' && User::where('role_global', 'admin')->count() <= 1) {
            return redirect()->route('admin.dashboard')->with('error', 'Impossible de bannir l\' admin.');
        }
        $user->update(['est_banni' => true]);
        return redirect()->route('admin.dashboard')->with('success', 'Utilisateur banni.');
    }

    public function unban(User $user)
    {
        $user->update(['est_banni' => false]);
        return redirect()->route('admin.dashboard')->with('success', 'Utilisateur debanni.');
    }
}
