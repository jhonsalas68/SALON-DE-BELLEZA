<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $logsCount = \App\Models\ActivityLog::count();
        \Illuminate\Support\Facades\Log::info("Dashboard accedido. Conteo de logs: " . $logsCount);
        
        return view('dashboard', [
            'user' => Auth::user(),
            'stats' => [
                'users_count' => \App\Models\User::count(),
                'roles_count' => \App\Models\Role::count(),
                'logs_count' => $logsCount,
                'appointments_today' => 0, // No table yet
                'total_sales' => 0,         // No table yet
            ],
            'recent_logs' => \App\Models\ActivityLog::with('user')->latest()->take(5)->get()
        ]);
    }
}
