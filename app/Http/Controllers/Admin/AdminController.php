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

        return view('admin.dashboard', compact('stats'));
    }
}
