<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait LogsActivity
{
    /**
     * Log an activity to the database.
     *
     * @param string $action (e.g., 'CREATE', 'UPDATE', 'DELETE', 'LOGIN', 'LOGOUT')
     * @param string $description
     * @param array|null $details
     * @return void
     */
    protected function logActivity(string $action, string $description, ?array $details = null): void
    {
        \Illuminate\Support\Facades\Log::info("Trait logActivity llamado para: " . $action);
        try {
            \App\Models\ActivityLog::create([
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
                'action' => $action,
                'description' => $description,
                'details' => $details,
                'ip_address' => request()->ip(),
                'user_agent' => request()->header('User-Agent'),
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("BITACORA ERROR: " . $e->getMessage());
        }
    }
}
