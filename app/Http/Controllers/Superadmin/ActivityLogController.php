<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index()
    {
        $logs = \App\Models\ActivityLog::orderBy('timestamp', 'desc')->get();
        return view('superadmin.activitylog', compact('logs'));
    }
}
