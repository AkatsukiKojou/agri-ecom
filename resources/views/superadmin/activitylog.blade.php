{{-- Activity Log Page --}}
@extends('superadmin.layout')

@section('title', 'Activity Log')

@section('content')
<div class="w-full p-8">
    <h1 class="text-3xl font-bold mb-6 text-green-900 flex items-center gap-2">
        <i class="bi bi-gear"></i> Activity Log
    </h1>
    <div class="bg-white rounded-xl shadow p-6">
        <p class="text-green-800">This is the Activity Log page. Display recent activities, actions, and system logs here.</p>
        <!-- Example Table -->
        <div class="overflow-x-auto mt-6">
            <table class="min-w-full bg-white border border-green-200 rounded-lg">
                <thead class="bg-green-100">
                    <tr>
                        <th class="py-2 px-4 border-b">Timestamp</th>
                        <th class="py-2 px-4 border-b">User/Admin</th>
                        <th class="py-2 px-4 border-b">Action</th>
                        <th class="py-2 px-4 border-b">Details</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td class="py-2 px-4 border-b">{{ $log->timestamp }}</td>
                            <td class="py-2 px-4 border-b">{{ $log->user_admin }}</td>
                            <td class="py-2 px-4 border-b">{{ $log->action }}</td>
                            <td class="py-2 px-4 border-b">{{ $log->details }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-4 px-4 text-center text-green-700">No activity logs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
