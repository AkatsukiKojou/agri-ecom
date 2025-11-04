<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserReport;

class SuperAdminReportController extends Controller
{
    public function index()
    {
        $reports = UserReport::with(['user', 'reporter'])->latest()->paginate(20);
        return view('superadmin.reports.reports', compact('reports'));
    }
}
