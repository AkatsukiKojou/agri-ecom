<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserReport;
use Illuminate\Support\Facades\Auth;

class UserReportController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'reason' => 'required|string|max:1000',
        ]);

        $report = UserReport::create([
            'user_id' => $request->user_id,
            'reporter_id' => Auth::id(),
            'reason' => $request->reason,
        ]);

        return response()->json(['success' => true, 'report_id' => $report->id]);
    }
}
