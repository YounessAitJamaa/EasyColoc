<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Colocation;
use App\Models\Depense;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $stats = [
            'total_utilisateurs' => User::count(),
            'total_colocations' => Colocation::count(),
            'total_depenses' => Depense::sum('montant'),
        ];

        $users = User::orderBy('name')->get();

        return view('admin.dashboard', compact('stats', 'users'));
    }

    public function ban(User $user)
    {
        $user->update(['est_banni' => true]);
        return redirect()->route('admin.dashboard')->with('success', 'Utilisateur banni.');
    }

    public function unban(User $user)
    {
        $user->update(['est_banni' => false]);
        return redirect()->route('admin.dashboard')->with('success', 'Utilisateur debanni.');
    }
}
